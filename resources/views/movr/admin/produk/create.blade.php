@extends('movr.layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold">Tambah Produk</h2>
        <div class="flex gap-3">
            <button id="save-draft-btn-top" type="button" class="px-6 py-2 rounded border text-gray-700 bg-white font-medium">Simpan Sebagai Draft</button>
            <button id="publish-btn-top" type="button" class="px-6 py-2 rounded text-white font-medium" style="background:#63a2bb">Publish Product</button>
        </div>
    </div>

    <form id="product-form" action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-12 gap-6">
            <!-- Left column: details -->
            <div class="col-span-8">
                <div class="bg-white rounded shadow p-6 mb-6">
                    <h3 class="text-lg font-medium mb-4" style="color:#63a2bb">Detail Produk</h3>
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12">
                            <label class="block text-sm font-medium mb-1">Nama Produk</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded p-2" required />
                        </div>

                        <div class="col-span-12">
                            <label class="block text-sm font-medium mb-1">Deskripsi</label>
                            <textarea name="description" class="w-full border rounded p-2 h-32">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-span-6">
                            <label class="block text-sm font-medium mb-1">Supplier</label>
                            <select name="supplier_id" class="w-full border rounded p-2">
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->store_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-6">
                            <label class="block text-sm font-medium mb-1">Harga</label>
                            <input type="number" name="price" value="{{ old('price') }}" class="w-full border rounded p-2" />
                        </div>

                        <div class="col-span-6">
                            <label class="block text-sm font-medium mb-1">Stok</label>
                            <input type="number" name="stock" value="{{ old('stock') }}" class="w-full border rounded p-2" />
                        </div>

                        <div class="col-span-4">
                            <label class="block text-sm font-medium mb-1">Material</label>
                            <input type="text" name="material_build" value="{{ old('material_build') }}" class="w-full border rounded p-2" />
                        </div>

                        <div class="col-span-8">
                            <label class="block text-sm font-medium mb-1">Tipe Olahraga</label>
                            <input type="text" name="sport_type" value="{{ old('sport_type') }}" class="w-full border rounded p-2" />
                        </div>

                        <div class="col-span-6">
                            <label class="block text-sm font-medium mb-1">Min Stock Alert</label>
                            <input type="number" name="min_stock_alert" value="{{ old('min_stock_alert', 0) }}" class="w-full border rounded p-2" />
                        </div>

                        <div class="col-span-6">
                            <label class="block text-sm font-medium mb-1">Tags (pisahkan dengan koma)</label>
                            <input type="text" name="tags" value="{{ old('tags') }}" class="w-full border rounded p-2" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded shadow p-6">
                    <h3 class="text-lg font-medium mb-4" style="color:#63a2bb">SEO & Lainnya</h3>
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12">
                            <label class="block text-sm font-medium mb-1">SKU (opsional)</label>
                            <input type="text" name="sku" value="{{ old('sku') }}" class="w-full border rounded p-2" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button id="save-btn" type="submit" class="px-6 py-2 rounded text-white font-medium" style="background:#2563eb">Simpan</button>
                </div>
            </div>

            <!-- Right column: media & publish -->
            <div class="col-span-4">
                <div class="bg-white rounded shadow p-6 mb-6">
                    <h3 class="text-md font-medium mb-4" style="color:#63a2bb">Media</h3>
                    <div id="dropzone" class="border-dashed border-2 border-gray-200 rounded p-4 text-center">
                        <p class="text-sm text-gray-600">Tarik dan lepas gambar di sini atau klik untuk memilih</p>
                        <input id="images-input" type="file" name="images[]" multiple accept="image/*" class="mt-3 w-full" />
                        <div id="preview" class="mt-3 grid grid-cols-2 gap-2"></div>
                    </div>
                </div>

                <div class="bg-white rounded shadow p-6 mb-6">
                    <h3 class="text-md font-medium mb-4" style="color:#63a2bb">Kategori & Gender</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Kategori</label>
                            <select name="category_id" class="w-full border rounded p-2">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Gender</label>
                            <select name="gender" class="w-full border rounded p-2">
                                <option value="">Pilih</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                                <option value="unisex" {{ old('gender') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded shadow p-6 mb-6">
                    <h3 class="text-md font-medium mb-3" style="color:#63a2bb">Status & Visibilitas</h3>
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Visibility</span>
                        <select name="visibility" class="border rounded p-2" id="visibility-select">
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                            <option value="hidden">Hidden</option>
                        </select>
                    </div>
                </div>

                <div class="bg-white rounded shadow p-4 text-sm text-gray-600">
                    <strong>Tips:</strong>
                    <p class="mt-2">Gunakan gambar dengan background putih untuk hasil terbaik. Warna aksen utama: <span class="font-medium" style="color:#63a2bb">#63a2bb</span></p>
                </div>
            </div>
        </div>

        <input type="hidden" name="_action" id="form-action" value="publish">
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const imagesInput = document.getElementById('images-input');
    const preview = document.getElementById('preview');
    const form = document.getElementById('product-form');
    const saveBtn = document.getElementById('save-btn');
    const saveDraftBtn = document.getElementById('save-draft-btn-top');
    const publishBtn = document.getElementById('publish-btn-top');
    const actionInput = document.getElementById('form-action');

    function renderPreviews(files){
        preview.innerHTML = '';
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            const wrapper = document.createElement('div');
            wrapper.className = 'relative rounded overflow-hidden border';
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-full h-28 object-cover';
                wrapper.appendChild(img);
                preview.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    imagesInput && imagesInput.addEventListener('change', (e) => renderPreviews(e.target.files));

    // Handle form submission
    form.addEventListener('submit', (e) => {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            alert('Harap isi semua field yang wajib!');
            return false;
        }
    });

    // Save button - sets action to 'save'
    if (saveBtn) {
        saveBtn.addEventListener('click', (e) => {
            actionInput.value = 'save';
            if (form.checkValidity()) {
                form.submit();
            } else {
                form.reportValidity();
            }
        });
    }

    // Draft button
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', () => {
            actionInput.value = 'draft';
            form.submit();
        });
    }

    // Publish button
    if (publishBtn) {
        publishBtn.addEventListener('click', () => {
            actionInput.value = 'publish';
            form.submit();
        });
    }
});
</script>
@endpush

@endsection