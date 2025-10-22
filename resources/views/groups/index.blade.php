@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Daftar Groups</h2>
        @if(auth()->user()->role == 'superuser')
        <a href="{{ route('groups.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Buat Group Baru</a>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ session('success') }}
    </div>
    @endif

    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1">Nama</th>
                <th class="border px-2 py-1">Deskripsi</th>
                <th class="border px-2 py-1">Jumlah User</th>
                @if(auth()->user()->role == 'superuser')
                <th class="border px-2 py-1">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($groups as $group)
            <tr>
                <td class="border px-2 py-1">{{ $group->name }}</td>
                <td class="border px-2 py-1">{{ $group->description }}</td>
                <td class="border px-2 py-1">{{ $group->users->count() }}</td>
                @if(auth()->user()->role == 'superuser')
                <td class="border px-2 py-1">
                    <a href="{{ route('groups.edit', $group->id) }}" class="text-yellow-600">Edit</a>
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $groups->links() }}
    </div>
</div>
@endsection