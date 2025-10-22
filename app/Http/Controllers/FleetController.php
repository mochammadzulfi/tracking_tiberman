<?php

namespace App\Http\Controllers;

use App\Models\Fleet;
use App\Models\User;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    public function index()
    {
        $fleets = Fleet::with('driver')->paginate(10); // pagination untuk blade
        return view('fleets.index', compact('fleets'));
    }

    public function create()
    {
        $drivers = User::where('role', 'driver')->get(); // daftar driver
        return view('fleets.form', ['fleet' => null, 'drivers' => $drivers]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'capacity' => 'required|numeric',
            'driver_user_id' => 'nullable|exists:users,id',
            'status' => 'required|string|max:50',
        ]);

        Fleet::create($validated);

        return redirect()->route('fleets.index')->with('success', 'Fleet berhasil ditambahkan.');
    }

    public function edit(Fleet $fleet)
    {
        $drivers = User::where('role', 'driver')->get();
        return view('fleets.form', compact('fleet', 'drivers'));
    }

    public function update(Request $request, Fleet $fleet)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'capacity' => 'required|numeric',
            'driver_user_id' => 'nullable|exists:users,id',
            'status' => 'required|string|max:50',
        ]);

        $fleet->update($validated);

        return redirect()->route('fleets.index')->with('success', 'Fleet berhasil diupdate.');
    }

    public function destroy(Fleet $fleet)
    {
        $fleet->delete();
        return redirect()->route('fleets.index')->with('success', 'Fleet berhasil dihapus.');
    }
}
