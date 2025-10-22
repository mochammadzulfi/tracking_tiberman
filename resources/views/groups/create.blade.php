@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Buat Group Baru</h2>

    <form action="{{ route('groups.store') }}" method="POST">
        @csrf

        <label class="block mb-2">Nama Group</label>
        <input type="text" name="name" value="{{ old('name') }}" class="border p-2 w-full mb-4">

        <label class="block mb-2">Deskripsi</label>
        <textarea name="description" class="border p-2 w-full mb-4">{{ old('description') }}</textarea>

        <label class="block mb-2">Users</label>
        <select name="user_ids[]" multiple class="border p-2 w-full mb-4">
            @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
    </form>
</div>
@endsection