<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\DeliveryProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\AuditHelper;

class DeliveryProofController extends Controller
{
    public function create(Shipment $shipment)
    {
        return view('proofs.create', compact('shipment'));
    }

    public function store(Request $request, Shipment $shipment)
    {
        $request->validate([
            'photo' => 'required|image|max:2048', // max 2MB
            'receiver_name' => 'required|string|max:255',
            'received_at' => 'required|date',
        ]);

        // ===== Upload langsung ke public/delivery_proofs =====
        $file = $request->file('photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $uploadPath = public_path('delivery_proofs');

        // Pastikan folder ada
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Pindahkan file ke folder public/delivery_proofs
        $file->move($uploadPath, $filename);

        // Simpan path relatif untuk ditampilkan (bisa diakses via URL)
        $photoPath = 'delivery_proofs/' . $filename;

        // ===== Simpan ke database =====
        DeliveryProof::create([
            'shipment_id' => $shipment->id,
            'photo_path' => $photoPath,
            'receiver_name' => $request->receiver_name,
            'received_at' => $request->received_at,
            'uploaded_by_user_id' => Auth::id(),
            'created_at' => now(),
        ]);

        // ===== Audit log =====
        AuditHelper::log($shipment->id, 'upload_delivery_proof', [
            'receiver_name' => $request->receiver_name,
            'photo_path' => $photoPath
        ]);

        // ===== Update status shipment =====
        $shipment->update(['status' => 'delivered']);

        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', 'Bukti serah terima berhasil diupload.');
    }
}
