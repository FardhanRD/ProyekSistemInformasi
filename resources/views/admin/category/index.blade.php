@extends('layouts.admin')

@section('title', 'Category Management')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6" x-data="categoryMgmt()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-xs font-bold text-admin uppercase tracking-widest mb-1">MOVR ADMIN</p>
            <h1 class="text-2xl font-black text-gray-900">Category Management</h1>
            <p class="text-sm text-gray-400 mt-1">Kelola struktur kategori produk secara hierarki — Level 1 (Utama), Level 2 (Sub).</p>
        </div>

        <button @click="openAddModal()"
                class="inline-flex items-center gap-2 px-5 py-3 bg-admin text-white rounded-xl font-bold text-sm shadow-lg shadow-admin/25 hover:bg-admin-dark hover:-translate-y-0.5 hover:shadow-xl hover:shadow-admin/30 transition-all duration-200 flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kategori
        </button>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-2xl font-black text-admin">{{ $total_categories_active }}</p>
            <p class="text-xs text-gray-400 mt-1 font-medium">Total Kategori Aktif</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-2xl font-black text-blue-600">{{ $categories->count() }}</p>
            <p class="text-xs text-gray-400 mt-1 font-medium">Kategori Level 1</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-2xl font-black text-purple-600">{{ $categories->sum(fn($c) => $c->children?->count() ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1 font-medium">Sub-Kategori Level 2</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-2xl font-black text-green-600">{{ $active_products }}</p>
            <p class="text-xs text-gray-400 mt-1 font-medium">Produk Aktif</p>
        </div>
    </div>

    <!-- Category Tree -->
    <div class="space-y-4">
        @forelse($categories as $level1)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <!-- Level 1 Header -->
                <div class="flex items-center gap-4 px-5 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                    <div class="w-10 h-10 bg-admin rounded-xl flex items-center justify-center text-white font-black text-sm flex-shrink-0">
                        {{ strtoupper(substr($level1->nama_kategori, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-black text-gray-900 text-base">{{ strtoupper($level1->nama_kategori) }}</p>
                            <span class="text-[10px] bg-admin/10 text-admin px-2 py-0.5 rounded-full font-bold">LEVEL 1</span>
                            <span class="text-xs text-gray-400 font-mono">/{{ $level1->slug }}</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $level1->children?->count() ?? 0 }} sub-kategori · Root Category</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button @click="openAddSubModal({{ $level1->kategori_id }}, '{{ addslashes($level1->nama_kategori) }}')"
                                class="flex items-center gap-1.5 px-3 py-2 bg-admin/10 text-admin rounded-xl text-xs font-bold hover:bg-admin hover:text-white transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Sub
                        </button>
                        <button @click="openEditModal({{ $level1->kategori_id }}, '{{ addslashes($level1->nama_kategori) }}', '{{ $level1->slug }}', null)"
                                class="w-8 h-8 bg-amber-50 rounded-xl flex items-center justify-center hover:bg-amber-100 transition" title="Edit Kategori">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form action="{{ route('admin.category.destroy', $level1->kategori_id) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ addslashes($level1->nama_kategori) }}? Sub-kategori di dalamnya juga akan terpengaruh.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 bg-red-50 rounded-xl flex items-center justify-center hover:bg-red-100 transition" title="Hapus Kategori">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Level 2 Children -->
                @if($level1->children && $level1->children->isNotEmpty())
                    <div class="divide-y divide-gray-50">
                        @foreach($level1->children as $level2)
                            <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition group">
                                <!-- Indent indicator -->
                                <div class="flex items-center gap-2 pl-4 flex-shrink-0">
                                    <div class="w-px h-4 bg-gray-200"></div>
                                    <div class="w-3 h-px bg-gray-200"></div>
                                </div>
                                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="font-semibold text-gray-700 text-sm">{{ $level2->nama_kategori }}</p>
                                        <span class="text-[10px] bg-blue-50 text-blue-500 px-2 py-0.5 rounded-full font-bold">Sub</span>
                                    </div>
                                    <p class="text-xs text-gray-400 font-mono">/{{ $level2->slug }}
                                        <span class="font-sans ml-2">· {{ $level2->produk_count ?? $level2->produk()->count() }} produk</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0 opacity-0 group-hover:opacity-100 transition">
                                    <button @click="openEditModal({{ $level2->kategori_id }}, '{{ addslashes($level2->nama_kategori) }}', '{{ $level2->slug }}', {{ $level1->kategori_id }})"
                                            class="w-7 h-7 bg-amber-50 rounded-lg flex items-center justify-center hover:bg-amber-100 transition" title="Edit Sub-Kategori">
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.category.destroy', $level2->kategori_id) }}" method="POST" onsubmit="return confirm('Hapus sub-kategori {{ addslashes($level2->nama_kategori) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-7 h-7 bg-red-50 rounded-lg flex items-center justify-center hover:bg-red-100 transition">
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-5 py-4 text-sm text-gray-400 italic flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Belum ada sub-kategori.
                        <button @click="openAddSubModal({{ $level1->kategori_id }}, '{{ addslashes($level1->nama_kategori) }}')" class="text-admin font-semibold hover:underline ml-1">Tambah sekarang →</button>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <p class="text-gray-500 font-semibold mb-1">Belum ada kategori</p>
                <p class="text-gray-400 text-sm">Mulai tambahkan kategori utama untuk produk Anda</p>
            </div>
        @endforelse
    </div>

    <!-- ======================= ADD/EDIT MODAL ======================= -->
    <div x-show="modalOpen"
         x-cloak
         @click.self="modalOpen = false"
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <!-- Modal Header -->
            <div class="px-5 py-4 flex items-center justify-between"
                 :class="editMode ? 'bg-amber-500' : 'bg-admin'">
                <div>
                    <h3 class="font-bold text-white" x-text="modalTitle"></h3>
                    <p class="text-xs text-white/70 mt-0.5" x-text="modalSubtitle"></p>
                </div>
                <button @click="modalOpen = false" class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="formAction" method="POST" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" x-bind:value="editMode ? 'PUT' : 'POST'">
                <input type="hidden" name="parent_id" x-bind:value="form.parent_id">
                <input type="hidden" name="urutan" value="0">
                <input type="hidden" name="is_active" value="1">

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nama Kategori *</label>
                    <input type="text" name="nama_kategori" required
                           x-model="form.nama"
                           @input="if (!editMode || !slugManuallyEdited) { form.slug = form.nama.toLowerCase().replace(/\s+/g,'-').replace(/[^a-z0-9-]/g,'') }"
                           placeholder="Contoh: Clothing, Running Shoes..."
                           class="w-full px-4 py-3 border-2 border-gray-200 focus:border-admin rounded-xl text-sm focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Slug (Auto)</label>
                    <input type="text" name="slug"
                           x-model="form.slug"
                           @input="slugManuallyEdited = true"
                           placeholder="auto-generated"
                           class="w-full px-4 py-3 border-2 border-gray-200 focus:border-admin rounded-xl text-sm font-mono focus:outline-none transition">
                    <p class="text-xs text-gray-400 mt-1">URL-friendly identifier, digenerate otomatis dari nama.</p>
                </div>

                <div x-show="form.parent_id" class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-sm text-blue-700 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Sub-kategori dari: <strong x-text="parentName" class="ml-1"></strong>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="modalOpen = false" class="flex-1 py-3 border-2 border-gray-200 text-gray-500 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                            :class="editMode ? 'bg-amber-500 hover:bg-amber-600' : 'bg-admin hover:bg-admin-dark'"
                            class="flex-[2] py-3 text-white rounded-xl font-bold text-sm transition"
                            x-text="editMode ? '💾 Update Kategori' : '+ Simpan Kategori'">
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function categoryMgmt() {
    return {
        modalOpen: false,
        editMode: false,
        editId: null,
        modalTitle: '',
        modalSubtitle: '',
        formAction: '{{ route('admin.category.store') }}',
        parentName: '',
        slugManuallyEdited: false,
        form: {
            nama: '',
            slug: '',
            parent_id: '',
        },

        openAddModal() {
            this.editMode = false;
            this.editId = null;
            this.slugManuallyEdited = false;
            this.modalTitle = 'Tambah Kategori Utama';
            this.modalSubtitle = 'Buat kategori level 1 baru (contoh: MAN, WOMEN, KIDS)';
            this.formAction = '{{ route('admin.category.store') }}';
            this.parentName = '';
            this.form = { nama: '', slug: '', parent_id: '' };
            this.modalOpen = true;
        },

        openAddSubModal(parentId, parentNameStr) {
            this.editMode = false;
            this.editId = null;
            this.slugManuallyEdited = false;
            this.modalTitle = 'Tambah Sub-Kategori';
            this.modalSubtitle = 'Sub-kategori di bawah ' + parentNameStr;
            this.formAction = '{{ route('admin.category.store') }}';
            this.parentName = parentNameStr;
            this.form = { nama: '', slug: '', parent_id: parentId };
            this.modalOpen = true;
        },

        openEditModal(id, nama, slug, parentId) {
            this.editMode = true;
            this.editId = id;
            this.slugManuallyEdited = false;
            this.modalTitle = 'Edit Kategori';
            this.modalSubtitle = 'Ubah nama atau slug kategori ini';
            this.formAction = `{{ url('/admin/category') }}/${id}`;
            this.parentName = '';
            this.form = { nama: nama, slug: slug, parent_id: parentId || '' };
            this.modalOpen = true;
        },
    };
}
</script>
@endsection
