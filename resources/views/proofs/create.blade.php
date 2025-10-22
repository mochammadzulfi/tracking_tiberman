@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Upload Bukti Serah Terima: {{ $shipment->code }}</h2>

    <form action="{{ route('proofs.store', $shipment->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block mb-1 font-medium">Foto Barang</label>
            <input type="file" name="photo" class="border rounded p-2 w-full">
            @error('photo') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-medium">Nama Penerima</label>
            <input type="text" name="receiver_name" class="border rounded p-2 w-full" value="{{ old('receiver_name') }}">
            @error('receiver_name') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-medium">Tanggal & Waktu Terima</label>
            <input type="datetime-local" name="received_at" class="border rounded p-2 w-full" value="{{ old('received_at') }}">
            @error('received_at') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Upload Bukti</button>
    </form>
</div>
@endsection