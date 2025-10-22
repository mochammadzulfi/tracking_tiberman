@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Detail Surat Jalan: {{ $shipment->code }}</h2>

    <p><strong>Customer:</strong> {{ $shipment->customer_name }}</p>
    <p><strong>Asal:</strong> {{ $shipment->origin_address }}</p>
    <p><strong>Tujuan:</strong> {{ $shipment->destination_address }}</p>
    <p><strong>Status:</strong> {{ $shipment->status }}</p>

    <!-- QR Code -->
    <h3 class="mt-4 font-bold">QR Code</h3>
    @if($shipment->barcode_data)
    <img src="{{ asset('storage/'.$shipment->barcode_data) }}" alt="QR Code" class="border p-2 rounded">
    @endif

    <!-- Scan Lokasi: hanya admin & superuser -->
    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'superuser')
    <h3 class="mt-4 font-bold">Scan Lokasi</h3>
    <form action="{{ route('shipments.scan', $shipment->id) }}" method="POST" id="scanForm">
        @csrf
        <input type="hidden" name="lat" id="lat">
        <input type="hidden" name="lng" id="lng">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Scan & Update Lokasi</button>
    </form>
    @else
    <p class="text-gray-500 mt-4">Hanya admin atau superuser yang dapat melakukan scan lokasi.</p>
    @endif
</div>

<script>
    navigator.geolocation.getCurrentPosition(function(position) {
        document.getElementById('lat').value = position.coords.latitude;
        document.getElementById('lng').value = position.coords.longitude;
    });
</script>
@endsection