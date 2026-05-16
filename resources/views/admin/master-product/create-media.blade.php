@extends('layouts.admin')

@section('title','Add Product — Step 3 Media')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">

    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-[#2B9BAF] text-white flex items-center justify-center text-sm font-bold">✓</span>
            <span class="font-semibold text-slate-800">General Info</span>
        </div>
        <div class="flex-1 h-px bg-gray-200"></div>
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-[#2B9BAF] text-white flex items-center justify-center text-sm font-bold">✓</span>
            <span class="font-semibold text-slate-800">Variants</span>
        </div>
        <div class="flex-1 h-px bg-gray-200"></div>
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-[#2B9BAF] text-white flex items-center justify-center text-sm font-bold">3</span>
            <span class="font-semibold">Media</span>
        </div>
    </div>

    @if(session('error'))
      <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
        {{ session('error') }}
      </div>
    @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded mb-4">
                <div class="font-semibold mb-1">Ada kesalahan pada form:</div>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    <form method="POST" action="{{ route('admin.master-product.media.store') }}" enctype="multipart/form-data" x-data="fileUploadForm()" @submit="validateFiles($event)" id="mediaForm">
        @csrf


        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-3">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="font-bold text-slate-900 mb-3">Ringkasan Step 1+2</h3>
                    <div class="text-sm text-slate-700"><span class="font-semibold">Nama:</span> {{ $step1['nama_produk'] }}</div>
                    <div class="text-sm text-slate-700 mt-2"><span class="font-semibold">Jumlah variant:</span> {{ is_array($step2) ? count($step2) : 0 }}</div>
                </div>
            </div>

            <div class="lg:col-span-9">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="text-sm font-semibold text-slate-800 mb-3">Upload gambar utama produk</div>

                    <div>
                        <div class="mb-3">
                            <label class="text-sm font-semibold text-slate-700">Upload Gambar Produk (Max 3)</label>
                            <p class="text-xs text-slate-500 mt-1">Gambar pertama akan menjadi thumbnail utama</p>
                        </div>

                        <label class="block border-2 border-dashed border-gray-300 rounded-xl p-12 text-center cursor-pointer hover:border-[#2B9BAF] transition">
                            <input type="file" name="gambar[]" multiple accept="image/jpg,image/jpeg,image/png,image/webp,image/avif,.webp,.avif" class="hidden" @change="handleFiles($event)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.9A5 5 0 0119.9 6A4 4 0 0118 16H7z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12v6m3-3H9" />
                            </svg>
                            <p class="mt-2 text-sm font-semibold text-slate-700">Drop files here or click to upload</p>
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP, AVIF up to 10MB · Max 3 images</p>
                        </label>

                        <div class="mt-5" x-show="previews.length > 0">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm font-semibold text-slate-700">Preview (<span x-text="fileCount"></span>/3)</p>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <template x-for="(src,i) in previews" :key="i">
                                    <div class="relative group">
                                        <img :src="src" class="w-full h-40 object-cover rounded-xl border border-slate-200" />
                                        <div class="absolute inset-0 bg-black/50 rounded-xl opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                            <span :class="i===0 ? 'bg-[#2B9BAF]' : 'bg-slate-600'" class="text-white text-xs px-3 py-1 rounded-full">
                                                <span x-text="i===0 ? '📌 Thumbnail' : 'Gambar ' + (i+1)"></span>
                                            </span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div x-show="previews.length === 0" class="mt-5 text-center text-slate-500 text-sm">
                            <p>Upload gambar untuk melihat preview</p>
                        </div>
                    </div>

                    <div class="mt-3 text-xs text-slate-500">
                        Gambar pertama akan menjadi thumbnail utama produk.
                    </div>

                    <div class="flex items-center justify-between gap-4 mt-6">
                        <a href="{{ route('admin.master-product.variant.create') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">← Back</a>
                        <button type="submit" class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white hover:bg-[#237f88]">
                            💾 Save & Publish
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
function fileUploadForm() {
    return {
        fileCount: 0,
        previews: [],
        handleFiles(e) {
            const maxFiles = 3;
            const allFiles = Array.from(e.target.files);
            
            if (allFiles.length > maxFiles) {
                alert('Maksimal ' + maxFiles + ' gambar saja. Anda memilih ' + allFiles.length + ' gambar.');
                e.target.value = '';
                this.previews = [];
                this.fileCount = 0;
                return;
            }
            
            this.fileCount = allFiles.length;
            this.previews = [];
            allFiles.forEach(f => {
                const r = new FileReader();
                r.onload = (ev) => this.previews.push(ev.target.result);
                r.readAsDataURL(f);
            });
        },
        validateFiles(e) {
            const fileInput = document.querySelector('input[name="gambar[]"]');
            const files = fileInput.files;
            
            if (files.length > 3) {
                alert('Maksimal 3 gambar saja. Anda memilih ' + files.length + ' gambar.');
                e.preventDefault();
                return false;
            }
        }
    }
}
</script>
@endsection

