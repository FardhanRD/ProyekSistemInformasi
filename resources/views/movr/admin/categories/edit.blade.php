@extends('movr.layouts.admin')

@section('content')
<section class="py-8">
    <div class="max-w-2xl mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Kategori</h1>
            <a href="{{ route('admin.kategori.index') }}" class="text-accent-green hover:underline">&larr; Kembali</a>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <form action="{{ route('admin.kategori.update', $kategori->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
                    <input type="text" name="name" value="{{ $kategori->name }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-gray-900 focus:outline-none focus:border-accent-green" required>
                </div>
                <button type="submit" class="bg-accent-green text-black font-bold py-3 px-6 rounded-xl w-full hover:opacity-90">
                    Update Kategori
                </button>
            </form>
        </div>
    </div>
</section>
@endsection