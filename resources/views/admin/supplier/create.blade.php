@extends('layouts.admin')

@section('title', 'Add New Supplier')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-4">
        <a href="{{ route('admin.supplier.index') }}" class="text-slate-600">← Back to Suppliers</a>
    </div>

    <h1 class="font-bold text-2xl text-slate-900">Add New Supplier</h1>
    <p class="text-sm text-slate-500 mt-1">Form minimal untuk membuat supplier.</p>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.supplier.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-semibold text-slate-700">Supplier Logo</label>
                    <input type="file" name="foto_toko" accept="image/*,.webp" class="mt-2 w-full">
                    <div class="mt-3 text-xs text-slate-500">Preview bisa menyesuaikan implementasi, untuk sementara tidak ditampilkan.</div>

                    <div class="mt-5">
                        <label class="text-sm font-semibold text-slate-700">Supplier Category</label>
                        <select name="kategori_supplier" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            <option value="">(optional)</option>
                            @foreach(($categories ?? collect()) as $c)
                                <option value="{{ $c->nama_kategori }}">{{ $c->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Store Name</label>
                            <input type="text" name="nama_toko" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Owner Name</label>
                            <input type="text" name="nama_owner" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Phone Number</label>
                            <input type="text" name="no_telepon" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="+62...">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Email Address</label>
                            <input type="email" name="email" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Full Store Address</label>
                            <textarea name="alamat_toko" required rows="4" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-2">
                            <label class="text-sm font-semibold text-slate-700">Map</label>
                            <div class="mt-2 h-56 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 text-sm">
                                Placeholder map
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <a href="{{ route('admin.supplier.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">← Back to Suppliers</a>
                <button type="submit" class="rounded-xl bg-teal-600 text-white px-4 py-2 text-sm font-semibold hover:bg-teal-700">Save Supplier</button>
            </div>
        </form>
    </div>
</div>
@endsection

