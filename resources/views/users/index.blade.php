@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Daftar User</h2>
        <!-- Tombol Tambah User hanya untuk Superuser -->
        @if(Auth::user()->role === 'superuser')
        <a href="{{ route('users.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Tambah User Baru</a>
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
                <th class="border px-2 py-1">Email</th>
                <th class="border px-2 py-1">Role</th>
                <th class="border px-2 py-1">Group</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td class="border px-2 py-1">{{ $user->name }}</td>
                <td class="border px-2 py-1">{{ $user->email }}</td>
                <td class="border px-2 py-1">{{ ucfirst($user->role) }}</td>
                <td class="border px-2 py-1">{{ $user->group->name ?? '-' }}</td>
                <td class="border px-2 py-1">
                    <!-- Edit hanya untuk Superuser atau Creator sendiri -->
                    @if(Auth::user()->role === 'superuser')
                    <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-600">Edit</a> |
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin hapus?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600">Hapus</button>
                    </form>
                    @else
                    <span class="text-gray-400">Tidak ada akses</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection