<form action="{{ route('shipments.update', $shipment->id) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- customer_name -->
    <input type="text" name="customer_name" value="{{ old('customer_name', $shipment->customer_name) }}" required>

    <!-- origin_address -->
    <textarea name="origin_address" required>{{ old('origin_address', $shipment->origin_address) }}</textarea>

    <!-- destination_address -->
    <textarea name="destination_address" required>{{ old('destination_address', $shipment->destination_address) }}</textarea>

    <!-- weight -->
    <input type="number" step="0.01" name="weight" value="{{ old('weight', $shipment->weight) }}">

    <!-- volume -->
    <input type="number" step="0.01" name="volume" value="{{ old('volume', $shipment->volume) }}">

    <!-- scheduled_at -->
    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', $shipment->scheduled_at ? $shipment->scheduled_at->format('Y-m-d\TH:i') : '') }}">

    <!-- assigned_fleet_id -->
    <select name="assigned_fleet_id">
        <option value="">-- Pilih Armada --</option>
        @foreach($fleets as $fleet)
        <option value="{{ $fleet->id }}" {{ old('assigned_fleet_id', $shipment->assigned_fleet_id) == $fleet->id ? 'selected' : '' }}>
            {{ $fleet->plate_number }} ({{ $fleet->vehicle_type }})
        </option>
        @endforeach
    </select>

    <!-- assigned_driver_id -->
    <select name="assigned_driver_id">
        <option value="">-- Pilih Driver --</option>
        @foreach($drivers as $driver)
        <option value="{{ $driver->id }}" {{ old('assigned_driver_id', $shipment->assigned_driver_id) == $driver->id ? 'selected' : '' }}>
            {{ $driver->name }}
        </option>
        @endforeach
    </select>

    <!-- status -->
    <select name="status">
        @foreach(['draft','assigned','on_progress','delivered','cancelled'] as $status)
        <option value="{{ $status }}" {{ old('status', $shipment->status) == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
        @endforeach
    </select>

    <button type="submit">Update</button>
</form>