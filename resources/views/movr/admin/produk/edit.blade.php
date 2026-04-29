@extends('movr.layouts.admin')

@section('content')
<section class="py-8">
    <div class="max-w-4xl mx-auto px-4">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Produk</h1>
                {{-- Perbaikan: Menggunakan $product->name (Inggris) --}}
                <p class="text-gray-500">Edit informasi produk "{{ $product->name }}"</p>
            </div>
            <a href="{{ route('admin.produk.index') }}" class="text-accent-green hover:underline">&larr; Kembali ke Daftar</a>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
            
            {{-- Form Update --}}
            <form action="{{ route('admin.produk.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
                    {{-- Perbaikan: value mengambil dari $product->name --}}
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-gray-900 focus:outline-none focus:border-accent-green" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                    <textarea name="description" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-gray-900 focus:outline-none focus:border-accent-green">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp)</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-gray-900 focus:outline-none focus:border-accent-green" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Stok</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-gray-900 focus:outline-none focus:border-accent-green" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                        <select name="category_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-gray-900 focus:outline-none focus:border-accent-green" required>
                            <option value="" disabled>Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{-- Logika: Jika ID kategori sama dengan kategori produk saat ini, maka pilih (selected) --}}
                                    {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Toko Supplier</label>
                        <select name="supplier_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-gray-900 focus:outline-none focus:border-accent-green" required>
                            <option value="" disabled>Pilih Toko Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $product->supplier_id == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->store_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Ubah Gambar (Opsional, Bisa Lebih dari 1)</label>
                        
                        {{-- Tampilkan galeri gambar lama jika ada --}}
                        @if($product->images->count() > 0)
                            <div class="mb-3 flex flex-wrap gap-2">
                                @foreach($product->images as $img)
                                    <img src="{{ asset('storage/' . $img->image_path) }}" class="h-16 w-16 object-cover rounded border border-gray-200">
                                @endforeach
                            </div>
                        @elseif($product->image)
                            <div class="mb-3 flex items-center">
                                <img src="{{ asset('storage/' . $product->image) }}" class="h-16 w-16 object-cover rounded border border-gray-200 mr-3">
                                <span class="text-xs text-gray-500">Gambar saat ini</span>
                            </div>
                        @endif

                        <input type="file" name="images[]" multiple class="w-full bg-gray-50 border border-gray-200 rounded-xl py-2 px-4 text-gray-500 focus:outline-none focus:border-accent-green">
                        <p class="text-xs text-gray-500 mt-1">Upload file baru untuk mengganti seluruh galeri gambar.</p>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="bg-accent-green text-black font-bold py-3 px-8 rounded-xl hover:opacity-90 transition">
                        <i class="fas fa-save mr-2"></i> Update Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection