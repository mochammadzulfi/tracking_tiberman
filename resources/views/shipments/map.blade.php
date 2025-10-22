@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Tracking Map</h2>
    <div id="map" class="w-full h-[600px] rounded border" data-shipments='@json($shipments)'></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@verbatim
<script>
    const map = L.map('map').setView([-6.200000, 106.816666], 11); // Default Jakarta

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const shipments = JSON.parse(document.getElementById('map').dataset.shipments);

    shipments.forEach(shipment => {
        const lastPoint = shipment.tracking_points[0]; // ambil tracking terakhir
        if (lastPoint) {
            L.marker([lastPoint.lat, lastPoint.lng])
                .addTo(map)
                .bindPopup(`
            <strong>${shipment.code}</strong><br>
            Customer: ${shipment.customer_name}<br>
            Status: ${shipment.status}<br>
            Last updated: ${new Date(lastPoint.created_at).toLocaleString()}
         `);
        }
    });
</script>
@endverbatim
@endsection