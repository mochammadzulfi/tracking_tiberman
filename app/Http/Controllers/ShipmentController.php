<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\Fleet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Models\TrackingPoint;
use App\Helpers\AuditHelper;
use GeoIP;

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
        $drivers = User::whereIn('role', ['creator', 'admin'])->get();
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

        // Generate QR Code
        $qrPath = 'qrcodes/' . $shipment->id . '.png';
        Storage::disk('public')->put($qrPath, QrCode::format('png')->size(300)->generate(route('shipments.show', $shipment->id)));

        // Simpan path QR ke shipment
        $shipment->update(['barcode_data' => $qrPath]);

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
        $drivers = User::whereIn('role', ['creator', 'admin'])->get();
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

    public function scan(Request $request, Shipment $shipment)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $driver = Auth::user();

        // Ambil lokasi berdasarkan IP
        $ipLocation = geoip($request->ip());

        // Hitung jarak GPS vs IP (km)
        $distance = $this->distance($request->lat, $request->lng, $ipLocation->lat, $ipLocation->lon);

        $isMismatch = $distance > 50; // threshold 50 km

        $tracking = TrackingPoint::create([
            'shipment_id' => $shipment->id,
            'fleet_id' => $shipment->assigned_fleet_id,
            'driver_id' => $driver->id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'source' => 'QR_SCAN',
            'ip_address' => $request->ip(),
            'ip_geo' => json_encode($ipLocation),
            'device_info' => json_encode([
                'user_agent' => $request->header('User-Agent'),
                'accept_language' => $request->header('Accept-Language'),
            ]),
            'is_ip_mismatch' => $isMismatch,
            'created_at' => now(),
        ]);

        AuditHelper::log($shipment->id, 'scan_qr', [
            'tracking_id' => $tracking->id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'ip_address' => $request->ip(),
            'distance_km' => $distance,
            'is_ip_mismatch' => $isMismatch,
        ]);

        return response()->json([
            'message' => $isMismatch
                ? 'Lokasi GPS tidak valid (dideteksi fake GPS)'
                : 'Lokasi berhasil diupdate via scan QR',
            'tracking' => $tracking,
            'distance_km' => $distance,
            'is_ip_mismatch' => $isMismatch
        ], 200);
    }

    public function map()
    {
        // Ambil shipment + tracking point terakhir
        $shipments = Shipment::with(['trackingPoints' => function ($q) {
            $q->latest()->first();
        }])->get();

        return view('shipments.map', compact('shipments'));
    }

    function distance($lat1, $lng1, $lat2, $lng2)
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
