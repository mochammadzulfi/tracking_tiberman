@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Edit Surat Jalan: {{ $shipment->code }}</h2>

    @if ($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('shipments.update', $shipment->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Customer Name -->
        <div>
            <label class="block font-medium text-gray-700">Customer Name</label>
            <input type="text" name="customer_name" value="{{ old('customer_name', $shipment->customer_name) }}" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <!-- Origin Address -->
        <div>
            <label class="block font-medium text-gray-700">Origin Address</label>
            <textarea name="origin_address" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('origin_address', $shipment->origin_address) }}</textarea>
        </div>

        <!-- Destination Address -->
        <div>
            <label class="block font-medium text-gray-700">Destination Address</label>
            <textarea name="destination_address" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('destination_address', $shipment->destination_address) }}</textarea>
        </div>

        <!-- Weight & Volume -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-gray-700">Weight</label>
                <input type="number" step="0.01" name="weight" value="{{ old('weight', $shipment->weight) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block font-medium text-gray-700">Volume</label>
                <input type="number" step="0.01" name="volume" value="{{ old('volume', $shipment->volume) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <!-- Scheduled At -->
        <div>
            <label class="block font-medium text-gray-700">Scheduled At</label>
            <input type="datetime-local" name="scheduled_at"
                value="{{ old('scheduled_at', $shipment->scheduled_at ? $shipment->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <!-- Assigned Fleet -->
        <div>
            <label class="block font-medium text-gray-700">Assigned Fleet</label>
            <select name="assigned_fleet_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">-- Pilih Armada --</option>
                @foreach($fleets as $fleet)
                <option value="{{ $fleet->id }}"
                    {{ old('assigned_fleet_id', $shipment->assigned_fleet_id) == $fleet->id ? 'selected' : '' }}>
                    {{ $fleet->plate_number }} ({{ $fleet->vehicle_type }})
                </option>
                @endforeach
            </select>
        </div>

        <!-- Assigned Driver -->
        <div>
            <label class="block font-medium text-gray-700">Assigned Driver</label>
            <select name="assigned_driver_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">-- Pilih Driver --</option>
                @foreach($drivers as $driver)
                <option value="{{ $driver->id }}"
                    {{ old('assigned_driver_id', $shipment->assigned_driver_id) == $driver->id ? 'selected' : '' }}>
                    {{ $driver->name }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Status -->
        <div>
            <label class="block font-medium text-gray-700">Status</label>
            <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @foreach(['draft','assigned','on_progress','delivered','cancelled'] as $status)
                <option value="{{ $status }}" {{ old('status', $shipment->status) == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
        </div>
    </form>
</div>
@endsection