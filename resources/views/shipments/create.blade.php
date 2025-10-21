@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Buat Surat Jalan</h2>

    @if ($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('shipments.store') }}" method="POST">
        @csrf

        <!-- Customer Name -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Nama Customer</label>
            <input type="text" name="customer_name" class="w-full border rounded p-2" value="{{ old('customer_name') }}" required>
        </div>

        <!-- Origin Address -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Alamat Asal</label>
            <textarea name="origin_address" class="w-full border rounded p-2" required>{{ old('origin_address') }}</textarea>
        </div>

        <!-- Destination Address -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Alamat Tujuan</label>
            <textarea name="destination_address" class="w-full border rounded p-2" required>{{ old('destination_address') }}</textarea>
        </div>

        <!-- Weight -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Berat (kg)</label>
            <input type="number" step="0.01" name="weight" class="w-full border rounded p-2" value="{{ old('weight') }}">
        </div>

        <!-- Volume -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Volume (m³)</label>
            <input type="number" step="0.01" name="volume" class="w-full border rounded p-2" value="{{ old('volume') }}">
        </div>

        <!-- Scheduled At -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Jadwal Pengiriman</label>
            <input type="datetime-local" name="scheduled_at" class="w-full border rounded p-2" value="{{ old('scheduled_at') }}">
        </div>

        <!-- Assigned Fleet -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Armada</label>
            <select name="assigned_fleet_id" class="w-full border rounded p-2">
                <option value="">-- Pilih Armada --</option>
                @foreach($fleets as $fleet)
                <option value="{{ $fleet->id }}" {{ old('assigned_fleet_id') == $fleet->id ? 'selected' : '' }}>
                    {{ $fleet->plate_number }} ({{ $fleet->vehicle_type }})
                </option>
                @endforeach
            </select>
        </div>

        <!-- Assigned Driver -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Driver</label>
            <select name="assigned_driver_id" class="w-full border rounded p-2">
                <option value="">-- Pilih Driver --</option>
                @foreach($drivers as $driver)
                <option value="{{ $driver->id }}" {{ old('assigned_driver_id') == $driver->id ? 'selected' : '' }}>
                    {{ $driver->name }}
                </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white font-bold px-4 py-2 rounded">
            Buat Surat Jalan
        </button>
    </form>
</div>
@endsection