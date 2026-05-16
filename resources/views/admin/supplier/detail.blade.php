@extends('layouts.admin')

@section('title', 'Supplier Detail')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-4">
        <a href="{{ route('admin.supplier.index') }}" class="text-slate-600">← Back to Suppliers</a>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm mb-6">
        <div class="flex items-start justify-between gap-6">
            <div>
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr(($supplier->nama_owner ?? $supplier->nama_toko ?? '-'),0,1)) }}
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900">{{ $supplier->nama_toko ?? '-' }}</div>
                        <div class="text-sm text-slate-500">{{ $supplier->kategori_supplier ?? '-' }} • {{ $supplier->alamat_toko ?? '-' }}</div>
                        <div class="mt-2 flex items-center gap-2">
                            @if(($supplier->is_verified ?? 0) == 1)
                                <span class="bg-green-50 text-green-700 text-xs px-3 py-1 rounded-full font-bold">Primary Supplier</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full font-bold">Secondary Supplier</span>
                            @endif
                            <span class="text-xs text-slate-500">Last updated -</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs text-slate-500 font-semibold">Total Orders</div>
                        <div class="text-xl font-bold text-slate-900 mt-2">-</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs text-slate-500 font-semibold">Joining Date</div>
                        <div class="text-xl font-bold text-slate-900 mt-2">-</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs text-slate-500 font-semibold">Global Rank</div>
                        <div class="text-xl font-bold text-slate-900 mt-2">-</div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <a href="#" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Edit Profile</a>
                <a href="#" class="rounded-xl bg-teal-600 text-white px-4 py-2 text-sm font-semibold hover:bg-teal-700">Contact Supplier</a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-1">
                <div class="text-sm font-bold text-slate-900 mb-2">Store Information</div>
                <ul class="text-sm text-slate-700 space-y-2">
                    <li><span class="text-slate-500">Owner:</span> {{ $supplier->nama_owner ?? '-' }}</li>
                    <li><span class="text-slate-500">Email:</span> {{ $supplier->email ?? '-' }}</li>
                    <li><span class="text-slate-500">Phone:</span> {{ $supplier->no_telepon ?? '-' }}</li>
                    <li><span class="text-slate-500">Registered Address:</span> {{ $supplier->alamat_toko ?? '-' }}</li>
                </ul>
            </div>

            <div class="lg:col-span-2">
                <div class="text-sm font-bold text-slate-900 mb-2">Registered Address</div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                    Map placeholder
                </div>
            </div>
        </div>

    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4 mb-3">
            <div>
                <div class="text-lg font-bold text-slate-900">Product List</div>
                <div class="text-sm text-slate-500">Total item: {{ isset($produkList) ? count($produkList) : 0 }}</div>
            </div>
            <form method="POST" action="{{ route('admin.supplier.destroy', $supplier->supplier_id) }}" onsubmit="return confirm('Delete supplier?')">
                @csrf @method('DELETE')
                <button type="submit" class="rounded-xl bg-red-600 text-white px-4 py-2 text-sm font-semibold hover:bg-red-700">Delete Supplier</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-2">Thumbnail</th>
                        <th class="py-3 px-2">Product</th>
                        <th class="py-3 px-2">SKU</th>
                        <th class="py-3 px-2">Stock</th>
                        <th class="py-3 px-2">Min Stock</th>
                        <th class="py-3 px-2">Harga</th>
                        <th class="py-3 px-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produkList ?? collect() as $p)
                        <tr class="border-t border-slate-100">
                            <td class="py-3 px-2">-</td>
                            <td class="py-3 px-2">
                                <div class="font-medium text-slate-900">{{ $p->nama_produk }}</div>
                                <div class="text-xs text-slate-500">{{ $p->slug ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-2">-</td>
                            <td class="py-3 px-2">-</td>
                            <td class="py-3 px-2">{{ $p->stok_minimum ?? '-' }}</td>
                            <td class="py-3 px-2">Rp {{ number_format($p->harga_dasar ?? 0,0,',','.') }}</td>
                            <td class="py-3 px-2">
                                @if(($p->status_stok ?? 'available') === 'available')
                                    <span class="bg-green-50 text-green-700 text-xs px-2 py-1 rounded-full font-semibold">ACTIVE</span>
                                @else
                                    <span class="bg-yellow-50 text-yellow-700 text-xs px-2 py-1 rounded-full font-semibold">INACTIVE</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-slate-100">
                            <td colspan="7" class="py-3 px-2 text-slate-600">No products linked.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.supplier.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Close</a>
        </div>
    </div>
</div>
@endsection

