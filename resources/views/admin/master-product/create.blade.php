@extends('layouts.admin')

@section('title','Add Product — Step 1')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">

    <div class="flex items-center gap-4 mb-8">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-[#2B9BAF] text-white flex items-center justify-center text-sm font-bold">1</div>
            <span class="font-medium text-[#2B9BAF]">General Info</span>
        </div>
        <div class="flex-1 h-px bg-gray-200"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-sm">2</div>
            <span class="text-gray-400">Variants</span>
        </div>
        <div class="flex-1 h-px bg-gray-200"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-sm">3</div>
            <span class="text-gray-400">Media</span>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.master-product.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="md:col-span-2 space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-bold text-slate-900 mb-4">General Information</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Product Name *</label>
                            <input type="text" name="nama_produk" required value="{{ old('nama_produk') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Description *</label>
                            <textarea name="deskripsi" required rows="5" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Category *</label>
                                <select name="kategori_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]">
                                    @foreach($kategoris as $root)
                                        @if($root->children->count() > 0)
                                            <optgroup label="{{ $root->nama_kategori }}">
                                                @foreach($root->children as $sub)
                                                    @if($sub->children->count() > 0)
                                                        <optgroup label="&nbsp;&nbsp;{{ $sub->nama_kategori }}">
                                                            @foreach($sub->children as $leaf)
                                                                <option value="{{ $leaf->kategori_id }}" {{ old('kategori_id') == $leaf->kategori_id ? 'selected' : '' }}>
                                                                    &nbsp;&nbsp;&nbsp;{{ $leaf->nama_kategori }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @else
                                                        <option value="{{ $sub->kategori_id }}" {{ old('kategori_id') == $sub->kategori_id ? 'selected' : '' }}>
                                                            &nbsp;&nbsp;{{ $sub->nama_kategori }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Supplier *</label>
                                <select name="supplier_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]">
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->supplier_id }}" {{ old('supplier_id') == $s->supplier_id ? 'selected' : '' }}>{{ $s->nama_toko }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-bold text-slate-900 mb-4">Product Specifications</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Material & Build</label>
                            <input type="text" name="spesifikasi" value="{{ old('spesifikasi') }}" placeholder="Material & Build" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Gender *</label>
                            <div class="flex items-center gap-4 flex-wrap">
                                @foreach(['men'=>'Men','women'=>'Women','unisex'=>'Unisex','kids'=>'Kids'] as $k => $label)
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="gender" value="{{ $k }}" required {{ old('gender') === $k ? 'checked' : '' }} />
                                        <span class="text-sm text-slate-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Sport Type</label>
                            <select name="tipe_olahraga" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]">
                                <option value="">—</option>
                                @foreach($sport_types as $sport)
                                    <option value="{{ $sport }}" {{ old('tipe_olahraga') === $sport ? 'selected' : '' }}>{{ $sport }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-bold text-slate-900 mb-4">Pricing & Stock</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Base Price *</label>
                            <input type="number" name="harga_dasar" required min="0" value="{{ old('harga_dasar') }}" placeholder="Harga dasar produk" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Min Stock Alert</label>
                            <input type="number" name="stok_minimum" min="0" value="{{ old('stok_minimum', 5) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-bold text-slate-900 mb-4">Visibility</h2>

                    <div x-data="{ status: '{{ old('status_publish','draft') }}' }">
                        <div class="space-y-3">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="status_publish" value="publish" x-model="status" checked>
                                <span class="text-sm text-slate-700">Publish</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="status_publish" value="draft" x-model="status">
                                <span class="text-sm text-slate-700">Draft</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="status_publish" value="scheduled" x-model="status">
                                <span class="text-sm text-slate-700">Schedule</span>
                            </label>

                            <div x-show="status === 'scheduled'" x-cloak>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Scheduled At</label>
                                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]" />
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tags</label>

                            <div x-data="{
                                tags: [],
                                input: '',
                                addTag() {
                                    if(this.input.trim()) {
                                        this.tags.push(this.input.trim());
                                        this.input = '';
                                    }
                                },
                                removeTag(i) { this.tags.splice(i,1); }
                            }">
                                <div class="flex items-center gap-2">
                                    <input type="text" x-model="input" @keydown.enter.prevent="addTag()" placeholder="Type tag and press Enter" class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]" />
                                    <button type="button" class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white" @click="addTag()">Add</button>
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <template x-for="(t, i) in tags" :key="i">
                                        <span class="inline-flex items-center gap-2 bg-slate-100 text-slate-700 rounded-full px-3 py-1 text-xs font-semibold">
                                            <span x-text="t"></span>
                                            <button type="button" class="text-slate-500 hover:text-slate-700" @click="removeTag(i)">×</button>
                                            <template>
                                                <input type="hidden" name="tags[]" :value="t">
                                            </template>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <span class="text-sm text-slate-700">Produk Unggulan di Homepage</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('admin.master-product.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white hover:bg-[#237f88]">Next →</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

