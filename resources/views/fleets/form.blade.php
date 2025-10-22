@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">{{ isset($fleet) ? 'Edit Armada' : 'Tambah Armada' }}</h2>

    <form action="{{ isset($fleet) ? route('fleets.update', $fleet->id) : route('fleets.store') }}" method="POST">
        @csrf
        @if(isset($fleet)) @method('PUT') @endif

        <div class="mb-2">
            <label class="block font-medium">Plat Nomor</label>
            <input type="text" name="plate_number" value="{{ old('plate_number', $fleet->plate_number ?? '') }}" class="border p-2 w-full">
        </div>

        <div class="mb-2">
            <label class="block font-medium">Tipe Kendaraan</label>
            <input type="text" name="vehicle_type" value="{{ old('vehicle_type', $fleet->vehicle_type ?? '') }}" class="border p-2 w-full">
        </div>

        <div class="mb-2">
            <label class="block font-medium">Kapasitas</label>
            <input type="number" name="capacity" value="{{ old('capacity', $fleet->capacity ?? '') }}" class="border p-2 w-full">
        </div>

        <div class="mb-2">
            <label class="block font-medium">Driver</label>
            <select name="driver_user_id" class="border p-2 w-full">
                <option value="">-- Pilih Driver --</option>
                @foreach($drivers as $driver)
                <option value="{{ $driver->id }}" @if(old('driver_user_id', $fleet->driver_user_id ?? '') == $driver->id) selected @endif>{{ $driver->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-2">
            <label class="block font-medium">Status</label>
            <select name="status" class="border p-2 w-full">
                @php
                $statuses = ['active','inactive','maintenance'];
                @endphp
                @foreach($statuses as $status)
                <option value="{{ $status }}" @if(old('status', $fleet->status ?? '') == $status) selected @endif>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-2">{{ isset($fleet) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection