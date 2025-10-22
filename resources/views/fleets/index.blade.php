@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Daftar Armada</h2>
        <a href="{{ route('fleets.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Tambah Armada</a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ session('success') }}
    </div>
    @endif

    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1">Plat Nomor</th>
                <th class="border px-2 py-1">Tipe Kendaraan</th>
                <th class="border px-2 py-1">Kapasitas</th>
                <th class="border px-2 py-1">Driver</th>
                <th class="border px-2 py-1">Status</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fleets as $fleet)
            <tr>
                <td class="border px-2 py-1">{{ $fleet->plate_number }}</td>
                <td class="border px-2 py-1">{{ $fleet->vehicle_type }}</td>
                <td class="border px-2 py-1">{{ $fleet->capacity }}</td>
                <td class="border px-2 py-1">{{ $fleet->driver->name ?? '-' }}</td>
                <td class="border px-2 py-1">{{ $fleet->status }}</td>
                <td class="border px-2 py-1">
                    <a href="{{ route('fleets.edit', $fleet->id) }}" class="text-yellow-600">Edit</a> |
                    <form action="{{ route('fleets.destroy', $fleet->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin hapus?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $fleets->links() }}
    </div>
</div>
@endsection