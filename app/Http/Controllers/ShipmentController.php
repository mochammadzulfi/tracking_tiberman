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
        $request->validate([
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

        $shipment->update([
            'customer_name' => $request->customer_name,
            'origin_address' => $request->origin_address,
            'destination_address' => $request->destination_address,
            'weight' => $request->weight,
            'volume' => $request->volume,
            'scheduled_at' => $request->scheduled_at,
            'assigned_fleet_id' => $request->assigned_fleet_id,
            'assigned_driver_id' => $request->assigned_driver_id,
            'status' => $request->status,
        ]);

        return redirect()->route('shipments.index')->with('success', 'Surat jalan berhasil diupdate.');
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

        $tracking = TrackingPoint::create([
            'shipment_id' => $shipment->id,
            'fleet_id' => $shipment->assigned_fleet_id,
            'driver_id' => $driver->id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'source' => 'QR_SCAN',
            'ip_address' => $request->ip(),
            'device_info' => json_encode($request->header()),
            'is_ip_mismatch' => false,
        ]);

        return response()->json([
            'message' => 'Lokasi berhasil diupdate via scan QR',
            'tracking' => $tracking
        ]);
    }
}
