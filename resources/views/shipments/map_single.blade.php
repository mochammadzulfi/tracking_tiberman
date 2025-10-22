@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Map Surat Jalan: {{ $shipment->code }}</h2>

    <div id="map" style="height: 500px;" class="rounded border" data-shipment='@json($shipment)'></div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shipment = JSON.parse(document.getElementById('map').dataset.shipment);

        // Default koordinat jika tracking kosong
        const defaultLat = -7.28;
        const defaultLng = 112.7;

        const map = L.map('map').setView([defaultLat, defaultLng], 13);

        // Tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const points = shipment.tracking_points;

        if (points.length > 0) {
            // Polyline untuk rute
            const latlngs = points.map(p => [p.lat, p.lng]);
            const polyline = L.polyline(latlngs, {
                color: 'blue',
                weight: 4
            }).addTo(map);

            // Marker untuk setiap titik
            points.forEach((point, index) => {
                let markerIcon;

                if (index === 0) {
                    // Start point - hijau
                    markerIcon = L.icon({
                        iconUrl: 'https://cdn-icons-png.flaticon.com/512/190/190411.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [0, -41]
                    });
                } else if (index === points.length - 1) {
                    // End point - merah
                    markerIcon = L.icon({
                        iconUrl: 'https://cdn-icons-png.flaticon.com/512/190/190422.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [0, -41]
                    });
                } else {
                    // Titik biasa
                    markerIcon = L.icon({
                        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [0, -41]
                    });
                }

                const marker = L.marker([point.lat, point.lng], {
                    icon: markerIcon
                }).addTo(map);
                marker.bindPopup(`
                <strong>${shipment.code}</strong><br>
                Driver: ${shipment.driver?.name ?? '-'}<br>
                Fleet: ${shipment.fleet?.plate_number ?? '-'}<br>
                Status: ${shipment.status}<br>
                Waktu: ${point.created_at}
            `);
            });

            // Fit map ke semua marker
            map.fitBounds(points.map(p => [p.lat, p.lng]), {
                padding: [50, 50]
            });
        } else {
            // Default view jika kosong
            map.setView([defaultLat, defaultLng], 13);
        }
    });
</script>
@endsection