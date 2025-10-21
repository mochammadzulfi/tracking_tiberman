@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Detail Surat Jalan: {{ $shipment->code }}</h2>

    <p><strong>Customer:</strong> {{ $shipment->customer_name }}</p>
    <p><strong>Asal:</strong> {{ $shipment->origin_address }}</p>
    <p><strong>Tujuan:</strong> {{ $shipment->destination_address }}</p>
    <p><strong>Status:</strong> {{ $shipment->status }}</p>

    <h3 class="mt-4 font-bold">QR Code</h3>
    @if($shipment->barcode_data)
    <img src="{{ asset('storage/'.$shipment->barcode_data) }}" alt="QR Code">
    @endif
</div>
@endsection