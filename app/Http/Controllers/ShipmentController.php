<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // <-- untuk HTTP request
use App\Models\Shipment;
use App\Models\Fleet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\TrackingPoint;
use App\Helpers\AuditHelper;
use GeoIP;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ShipmentController extends Controller
{
    // List semua shipment
    public function index()
    {
        $shipments = Shipment::with(['driver', 'fleet', 'creator'])->paginate(15);
        return view('shipments.index', compact('shipments'));
    }

    // Form create
    public function create()
    {
        $fleets = Fleet::all();
        $drivers = User::whereIn('role', ['driver'])->get();
        return view('shipments.create', compact('fleets', 'drivers'));
    }

    // Simpan shipment baru
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'origin_address' => 'required|string',
            'destination_address' => 'required|string',
            'weight' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'scheduled_at' => 'nullable|date',
            'assigned_fleet_id' => 'nullable|exists:fleets,id',
            'assigned_driver_id' => 'nullable|exists:users,id',
        ]);

        $code = Shipment::generateCode();

        $shipment = Shipment::create([
            'code' => $code,
            'customer_name' => $request->customer_name,
            'origin_address' => $request->origin_address,
            'destination_address' => $request->destination_address,
            'weight' => $request->weight,
            'volume' => $request->volume,
            'scheduled_at' => $request->scheduled_at,
            'assigned_fleet_id' => $request->assigned_fleet_id,
            'assigned_driver_id' => $request->assigned_driver_id,
            'created_by_user_id' => Auth::id(),
            'status' => 'draft',
        ]);

        $shipmentCode = $shipment->code;

        // Encode kode untuk URL
        $encoded = urlencode($shipmentCode);

        // Gunakan API gratis seperti goqr.me
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$encoded}";

        // Simpan URL ke database
        $shipment->update([
            'barcode_data' => $qrUrl,
        ]);

        // Catat audit log, hanya field yang diupdate
        AuditHelper::log($shipment->id, 'create_shipment', [
            'changes' => $shipment
        ]);

        return redirect()->route('shipments.index')->with('success', 'Surat jalan berhasil dibuat dengan QR Code.');
    }

    // Detail shipment
    public function show(Shipment $shipment)
    {
        $shipment->load(['driver', 'fleet', 'creator', 'trackingPoints', 'deliveryProofs', 'auditLogs']);
        return view('shipments.show', compact('shipment'));
    }

    // Form edit
    public function edit(Shipment $shipment)
    {
        $fleets = Fleet::all();
        $drivers = User::whereIn('role', ['driver'])->get();
        return view('shipments.edit', compact('shipment', 'fleets', 'drivers'));
    }

    // Update shipment
    public function update(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'origin_address' => 'required|string',
            'destination_address' => 'required|string',
            'weight' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'scheduled_at' => 'nullable|date',
            'assigned_fleet_id' => 'nullable|exists:fleets,id',
            'assigned_driver_id' => 'nullable|exists:users,id',
            'status' => 'required|in:draft,assigned,on_progress,delivered,cancelled'
        ]);

        // Update shipment hanya sekali
        $shipment->update($validated);

        // Catat audit log, hanya field yang diupdate
        AuditHelper::log($shipment->id, 'update_shipment', [
            'changes' => $validated
        ]);

        return redirect()->route('shipments.index')
            ->with('success', 'Surat jalan berhasil diupdate.');
    }

    // Delete shipment
    public function destroy(Shipment $shipment)
    {
        $shipment->delete();
        return redirect()->route('shipments.index')->with('success', 'Surat jalan berhasil dihapus.');
    }

    public function showMap(Shipment $shipment)
    {
        // Load tracking points, fleet, driver
        $shipment->load('trackingPoints', 'fleet', 'driver');

        return view('shipments.map_single', compact('shipment'));
    }

    public function scan(Request $request, Shipment $shipment)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $driver = Auth::user();

        $ip = $request->ip();
        $ipLocation = null;

        try {
            // Gunakan GeoAPI
            $apiKey = env('GEOAPIFY_KEY');
            $url = "https://api.geoapify.com/v1/ipinfo?apiKey={$apiKey}&ip={$ip}";

            $response = Http::get($url);
            if ($response->ok()) {
                $data = $response->json();
                if (isset($data['location']['latitude']) && isset($data['location']['longitude'])) {
                    $ipLocation = [
                        'lat' => (float) $data['location']['latitude'],
                        'lng' => (float) $data['location']['longitude'],
                        'city' => $data['city']['name'] ?? null,
                        'region' => $data['state']['name'] ?? null,
                        'country' => $data['country']['name'] ?? null,
                        'ip' => $ip,
                    ];
                }
            }
        } catch (\Exception $e) {
            $ipLocation = null;
        }

        // Cek fake GPS berdasarkan jarak antara GPS device dan lokasi IP
        $isFake = false;
        if ($ipLocation && isset($ipLocation['lat'], $ipLocation['lng'])) {
            $distance = $this->distance($request->lat, $request->lng, $ipLocation['lat'], $ipLocation['lng']);
            // Threshold: 150 km
            $isFake = $distance > 150;
        }

        $tracking = TrackingPoint::create([
            'shipment_id' => $shipment->id,
            'fleet_id' => $shipment->assigned_fleet_id,
            'driver_id' => $driver->id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'source' => 'QR_SCAN',
            'ip_address' => $ip,
            'ip_geo' => $ipLocation,
            'device_info' => json_encode($request->header()),
            'is_ip_mismatch' => $isFake,
        ]);

        AuditHelper::log($shipment->id, 'scan_qr', [
            'tracking_id' => $tracking->id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'ip_address' => $ip,
            'device_info' => $request->header(),
            'ip_geo' => $ipLocation,
            'is_ip_mismatch' => $isFake
        ]);

        $message = $isFake
            ? 'Lokasi dicurigai tidak valid (Fake GPS). Silakan cek perangkat.'
            : 'Lokasi berhasil diupdate via scan QR';

        return response()->json([
            'message' => $message,
            'tracking' => $tracking
        ]);
    }

    /**
     * Hitung jarak antara dua koordinat (Haversine formula)
     */
    private function distance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
