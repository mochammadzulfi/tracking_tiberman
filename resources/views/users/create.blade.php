@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Tambah User Baru</h2>

        @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label class="block font-medium text-sm text-gray-700" for="name">Nama</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block font-medium text-sm text-gray-700" for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block font-medium text-sm text-gray-700" for="password">Password</label>
                <input type="password" name="password" id="password"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label class="block font-medium text-sm text-gray-700" for="role">Role</label>
                <select name="role" id="role"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="creator" {{ old('role') === 'creator' ? 'selected' : '' }}>Creator</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="view_only" {{ old('role') === 'view_only' ? 'selected' : '' }}>View Only</option>
                    <option value="superuser" {{ old('role') === 'superuser' ? 'selected' : '' }}>Super User</option>
                    <option value="driver" {{ old('role') === 'driver' ? 'selected' : '' }}>Driver</option>
                </select>
            </div>

            <!-- Group -->
            <div class="mb-4">
                <label class="block font-medium text-sm text-gray-700" for="group_id">Group</label>
                <select name="group_id" id="group_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">-- Pilih Group --</option>
                    @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                        {{ $group->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end mt-6">
                <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Batal</a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection