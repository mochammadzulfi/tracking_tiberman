@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Edit Group: {{ $group->name }}</h2>

    <form action="{{ route('groups.update', $group->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label class="block mb-2">Nama Group</label>
        <input type="text" name="name" value="{{ old('name', $group->name) }}" class="border p-2 w-full mb-4">

        <label class="block mb-2">Deskripsi</label>
        <textarea name="description" class="border p-2 w-full mb-4">{{ old('description', $group->description) }}</textarea>

        <label class="block mb-2">Users</label>
        <select name="user_ids[]" multiple class="border p-2 w-full mb-4">
            @foreach($users as $user)
            <option value="{{ $user->id }}" @if($group->users->contains($user->id)) selected @endif>{{ $user->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
    </form>
</div>
@endsection