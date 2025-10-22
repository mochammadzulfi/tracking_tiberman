@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Daftar Surat Jalan</h2>

    <!-- Tombol Buat Surat Jalan: hanya creator & superuser -->
    @if(auth()->user()->role == 'creator' || auth()->user()->role == 'superuser')
    <a href="{{ route('shipments.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Buat Surat Jalan Baru</a>
    @endif

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ session('success') }}
    </div>
    @endif

    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1">Kode</th>
                <th class="border px-2 py-1">Customer</th>
                <th class="border px-2 py-1">Driver</th>
                <th class="border px-2 py-1">Fleet</th>
                <th class="border px-2 py-1">Status</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipments as $shipment)
            <tr>
                <td class="border px-2 py-1">{{ $shipment->code }}</td>
                <td class="border px-2 py-1">{{ $shipment->customer_name }}</td>
                <td class="border px-2 py-1">{{ $shipment->driver->name ?? '-' }}</td>
                <td class="border px-2 py-1">{{ $shipment->fleet->plate_number ?? '-' }}</td>
                <td class="border px-2 py-1">{{ $shipment->status }}</td>
                <td class="border px-2 py-1 space-x-1">

                    <!-- Detail: semua role bisa lihat -->
                    <a href="{{ route('shipments.show', $shipment->id) }}" class="text-blue-600">Detail</a>

                    <!-- Edit: creator & superuser -->
                    @if(auth()->user()->role == 'creator' || auth()->user()->role == 'superuser')
                    | <a href="{{ route('shipments.edit', $shipment->id) }}" class="text-yellow-600">Edit</a>
                    @endif

                    <!-- Hapus: creator & superuser -->
                    @if(auth()->user()->role == 'creator' || auth()->user()->role == 'superuser')
                    | <form action="{{ route('shipments.destroy', $shipment->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin hapus?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600">Hapus</button>
                    </form>
                    @endif

                    <!-- Scan QR: admin & superuser -->
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'superuser')
                    | <form action="{{ route('shipments.scan', $shipment->id) }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="text-green-600">Scan QR</button>
                    </form>
                    @endif

                    <!-- View only -->
                    @if(auth()->user()->role == 'view_only')
                    <span class="text-gray-500">Hanya bisa melihat</span>
                    @endif

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $shipments->links() }}
    </div>
</div>
@endsection