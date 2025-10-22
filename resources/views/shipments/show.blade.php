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
    <img src="{{ $shipment->barcode_data }}" alt="QR Code" class="border p-2 rounded">
    @endif

    <!-- Scan Lokasi -->
    @if(auth()->user()->role != 'view_only')
    <h3 class="mt-4 font-bold">Scan Lokasi</h3>
    <form id="scanForm">
        @csrf
        <input type="hidden" name="lat" id="lat">
        <input type="hidden" name="lng" id="lng">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Scan & Update Lokasi</button>
    </form>
    @endif

    <h3 class="mt-6 font-bold">Bukti Serah Terima</h3>

    @if($shipment->deliveryProofs->count())
    <ul>
        @foreach($shipment->deliveryProofs as $proof)
        <li>
            <a href="{{ Storage::url($proof->photo_path) }}" target="_blank">
                Lihat Bukti #{{ $loop->iteration }}
            </a>
        </li>
        @endforeach
    </ul>
    @else
    <p>Belum ada bukti serah terima.</p>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('scanForm');

        // Ambil posisi GPS
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('lat').value = position.coords.latitude;
            document.getElementById('lng').value = position.coords.longitude;
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const lat = document.getElementById('lat').value;
            const lng = document.getElementById('lng').value;
            const token = form.querySelector('input[name="_token"]').value;

            fetch("{{ route('shipments.scan', $shipment->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        lat: lat,
                        lng: lng
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message); // <-- Notifikasi sukses
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal update lokasi');
                });
        });
    });
</script>
@endsection