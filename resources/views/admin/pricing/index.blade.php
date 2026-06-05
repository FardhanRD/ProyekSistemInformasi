@extends('layouts.admin')

@section('title', 'Pricing Management')

@section('content')
<div style="padding: 28px 28px 40px;">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 24px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 4px;">Keuangan & Bisnis</p>
            <h1 class="page-header-title" style="margin: 0;">Pricing Management</h1>
            <p class="page-header-sub" style="margin: 0; color: #94A3B8;">Kelola dan sesuaikan harga jual untuk setiap varian produk.</p>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="panel" style="margin-bottom: 24px; padding: 18px 20px;">
        <form method="GET" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <label class="form-label" style="margin-bottom: 6px;">Filter per Produk</label>
                <select name="produk_id" class="form-input" style="height: 38px; cursor: pointer;">
                    <option value="">All Products (Semua Produk)</option>
                    @foreach($produk_list as $p)
                        <option value="{{ $p->produk_id }}" {{ ($produk_filter ?? '') == $p->produk_id ? 'selected' : '' }}>
                            {{ $p->nama_produk }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-primary" style="height: 38px; padding: 0 18px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('produk_id'))
                    <a href="{{ route('admin.pricing.index') }}" class="btn-secondary" style="height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 14px;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Variants Price Table Panel --}}
    <div class="panel">
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th style="width: 150px;">SKU</th>
                        <th style="width: 200px;">Varian (Warna / Ukuran)</th>
                        <th style="text-align: right; width: 180px;">Harga Jual Saat Ini</th>
                        <th style="text-align: center; width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variants as $v)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0F172A;">{{ $v->produk->nama_produk ?? '-' }}</div>
                                <div style="font-size: 11px; color: #94A3B8;">Product ID: #{{ $v->produk_id }}</div>
                            </td>
                            <td style="font-family: monospace; font-weight: 600; color: #475569;">{{ $v->sku ?: '-' }}</td>
                            <td>
                                <div style="display: inline-flex; align-items: center; gap: 6px;">
                                    <span class="badge badge-admin" style="font-weight: 600;">{{ $v->warna->nama_warna ?? '-' }}</span>
                                    @if($v->ukuran)
                                        <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700;">Size: {{ $v->ukuran }}</span>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align: right; font-weight: 800; color: #0F172A; font-family: monospace;">
                                Rp {{ number_format($v->harga ?? 0, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-secondary" style="padding: 6px 12px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" onclick="editPrice({{ $v->detail_produk_id }}, {{ $v->harga ?? 0 }})">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Edit Harga
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94A3B8; padding: 36px;">
                                Belum ada data varian produk untuk pricing.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($variants ?? null, 'links') && $variants->hasPages())
            <div style="border-top: 1px solid #F1F5F9; padding: 16px 20px; display: flex; justify-content: center;">
                {{ $variants->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Edit Price Modal --}}
<div id="editPriceModal" x-data="{}" style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); width: 100%; max-width: 400px; overflow: hidden; border: 1px solid #E2E8F0;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <div style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;">Sesuaikan Harga Jual</h3>
            <button type="button" style="background: none; border: none; cursor: pointer; color: #94A3B8; font-size: 16px;" onclick="document.getElementById('editPriceModal').style.display = 'none'">✕</button>
        </div>

        <form id="editPriceForm" method="POST" style="padding: 20px 24px; margin: 0; display: flex; flex-direction: column; gap: 16px;">
            @csrf 
            @method('PUT')

            <div>
                <label class="form-label" style="margin-bottom: 6px;">Harga Baru (Rupiah) <span style="color: #EF4444;">*</span></label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 14px; font-size: 13px; font-weight: 700; color: #64748B; pointer-events: none;">Rp</span>
                    <input type="number" id="priceInput" name="harga" required min="0" class="form-input" style="padding-left: 38px; height: 38px; font-weight: 700; font-family: monospace;">
                </div>
                <p style="font-size: 11px; color: #94A3B8; margin: 6px 0 0;">Pastikan harga jual sudah memperhitungkan harga modal supplier.</p>
            </div>

            <div style="margin-top: 8px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn-secondary" style="height: 38px; padding: 0 16px;" onclick="document.getElementById('editPriceModal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn-primary" style="height: 38px; padding: 0 20px;">Simpan Harga</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function editPrice(variantId, currentPrice) {
    document.getElementById('priceInput').value = currentPrice;
    document.getElementById('editPriceForm').action = `/admin/pricing/${variantId}`;
    document.getElementById('editPriceModal').style.display = 'flex';
}
</script>
@endsection
