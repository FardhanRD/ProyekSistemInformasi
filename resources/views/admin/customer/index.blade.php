@extends('layouts.admin')

@section('title', 'Customer Management')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Customer Management</h1>
                <p class="text-slate-600">Kelola data pelanggan, view riwayat pembelian, dan blokir/buka akun.</p>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
            <input type="text" name="search" value="{{ $search_filter ?? '' }}" placeholder="Cari nama, email, atau no HP..." class="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-sm">
            
            <select name="status" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Status</option>
                <option value="1" {{ ($status_filter ?? '') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ ($status_filter ?? '') === '0' ? 'selected' : '' }}>Diblokir</option>
            </select>

            <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-5 py-2 text-sm font-semibold hover:bg-[#237f88]">Filter</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr class="text-left text-xs font-semibold text-slate-700 uppercase">
                        <th class="px-4 py-3">Foto</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">No HP</th>
                        <th class="px-4 py-3">Terdaftar</th>
                        <th class="px-4 py-3">Total Order</th>
                        <th class="px-4 py-3">Total Belanja</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $cust)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 text-xs">
                            <td class="px-4 py-3">
                                @if($cust->foto_profil)
                                    <img src="{{ Storage::url($cust->foto_profil) }}" alt="{{ $cust->nama_pengguna }}" class="h-10 w-10 rounded-full object-cover">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center">👤</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $cust->nama_pengguna }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $cust->email }}</td>
                            <td class="px-4 py-3">{{ $cust->no_telepon }}</td>
                            <td class="px-4 py-3">{{ $cust->created_at?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $cust->total_order }}</td>
                            <td class="px-4 py-3 font-semibold">Rp {{ number_format($cust->total_belanja ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @if($cust->is_active)
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Aktif</span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Diblokir</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 space-x-2 flex">
                                <form method="POST" action="{{ route('admin.customer.block', $cust->pengguna_id) }}" onsubmit="return confirm('Yakin?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-slate-600 hover:text-slate-900">
                                        {{ $cust->is_active ? '🚫' : '✓' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-6 px-4 text-center text-slate-600">
                                Tidak ada customer.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($customers ?? null, 'links'))
            <div class="border-t border-slate-100 p-4">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
