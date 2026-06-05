@extends('layouts.admin')

@section('title', 'Stock Management')

@section('content')
<div style="padding: 28px 28px 40px;">

    {{-- Page Header --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
        <div>
            <p style="font-size:11px; font-weight:700; color:#63A2BB; text-transform:uppercase; letter-spacing:0.1em; margin:0 0 4px;">Inventori</p>
            <h1 class="page-header-title" style="margin:0 0 4px;">Stock Management</h1>
            <p class="page-header-sub" style="margin:0; color:#94A3B8;">Kelola stok produk fisik dan pantau peringatan untuk sisa stok kritis.</p>
        </div>
        
        <div style="display: flex; gap: 8px;">
            <div style="display: inline-flex; align-items: center; gap: 8px; background: #FFF5F5; border: 1px solid #FEE2E2; color: #EF4444; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 700; box-shadow: 0 1px 3px rgba(239,68,68,0.03);">
                <span>⚠️ Low Stock Alert: <strong>{{ $low_stock_count }}</strong> Varian</span>
            </div>
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

            <div style="width: 180px;">
                <label class="form-label" style="margin-bottom: 6px;">Status Stok</label>
                <select name="status" class="form-input" style="height: 38px; cursor: pointer;">
                    <option value="">All Status (Semua)</option>
                    <option value="ok" {{ ($status_filter ?? '') === 'ok' ? 'selected' : '' }}>OK (Stok Cukup)</option>
                    <option value="low" {{ ($status_filter ?? '') === 'low' ? 'selected' : '' }}>Low Stock (Hampir Habis)</option>
                    <option value="out" {{ ($status_filter ?? '') === 'out' ? 'selected' : '' }}>Out of Stock (Habis)</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-primary" style="height: 38px; padding: 0 16px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('produk_id') || request('status'))
                    <a href="{{ route('admin.stock.index') }}" class="btn-secondary" style="height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 14px;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Variants Table Panel --}}
    <div class="panel">
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th style="width: 140px;">SKU</th>
                        <th style="width: 200px;">Varian</th>
                        <th style="text-align: center; width: 120px;">Stok Saat Ini</th>
                        <th style="text-align: center; width: 120px;">Minimum Stok</th>
                        <th style="text-align: center; width: 120px;">Status Buku</th>
                        <th style="text-align: center; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variants as $v)
                        @php
                            $stok_status = 'ok';
                            $min_stok = $v->produk->stok_minimum ?? 5;
                            if ($v->stok <= $min_stok) {
                                $stok_status = $v->stok == 0 ? 'out' : 'low';
                            }
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0F172A;">{{ $v->produk->nama_produk ?? '-' }}</div>
                            </td>
                            <td style="font-family: monospace; font-weight: 600; color: #475569;">{{ $v->sku ?: '-' }}</td>
                            <td>
                                <div style="display: inline-flex; align-items: center; gap: 6px;">
                                    <span class="badge badge-admin">{{ $v->warna->nama_warna ?? '-' }}</span>
                                    @if($v->ukuran)
                                        <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700;">Size: {{ $v->ukuran }}</span>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align: center; font-weight: 800; color: #0F172A; font-family: monospace;">
                                {{ number_format($v->stok ?? 0) }} pcs
                            </td>
                            <td style="text-align: center; color: #64748B; font-weight: 600;">
                                {{ number_format($min_stok) }} pcs
                            </td>
                            <td style="text-align: center;">
                                @if($stok_status === 'ok')
                                    <span class="badge badge-success">OK / Aman</span>
                                @elseif($stok_status === 'low')
                                    <span class="badge badge-warning">LOW / Tipis</span>
                                @else
                                    <span class="badge badge-danger">OUT / Habis</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-secondary" style="padding: 6px 12px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" onclick="adjustStock({{ $v->detail_produk_id }}, {{ $v->stok }})">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Adjust Stok
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94A3B8; padding: 36px;">
                                Belum ada data stok varian produk.
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

{{-- Adjust Stock Modal --}}
<div id="adjustStockModal" x-data="{}" style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); width: 100%; max-width: 400px; overflow: hidden; border: 1px solid #E2E8F0;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <div style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;">Adjust / Penyesuaian Stok</h3>
            <button type="button" style="background: none; border: none; cursor: pointer; color: #94A3B8; font-size: 16px;" onclick="document.getElementById('adjustStockModal').style.display = 'none'">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.stock.adjust') }}" style="padding: 20px 24px; margin: 0; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            <input type="hidden" name="detail_produk_id" id="variantId">

            <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Stok Buku Saat Ini</span>
                    <p id="currentStock" style="font-size: 22px; font-weight: 900; color: #0F172A; margin: 2px 0 0; font-family: monospace;">0 pcs</p>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #FFFDF5; display: flex; align-items: center; justify-content: center; border: 1px solid #FEF3C7;">
                    <svg width="20" height="20" fill="none" stroke="#D97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>

            <div>
                <label class="form-label" style="margin-bottom: 6px;">Penyesuaian Stok (+ atau -) <span style="color: #EF4444;">*</span></label>
                <input type="text" name="qty" required class="form-input" style="height: 38px; font-weight: 700; font-family: monospace;" placeholder="Contoh: +10 atau -5">
                <p style="font-size: 11px; color: #94A3B8; margin: 4px 0 0;">Gunakan tanda tambah (+) untuk menambah, tanda kurang (-) untuk mengurangi.</p>
            </div>

            <div>
                <label class="form-label" style="margin-bottom: 6px;">Catatan Penyesuaian</label>
                <textarea name="catatan" rows="3" placeholder="Contoh: Penyesuaian stok rusak / opname bulanan..." class="form-input" style="height: auto; min-height: 80px; padding: 8px 12px; resize: vertical;"></textarea>
            </div>

            <div style="margin-top: 8px; display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #F1F5F9; padding-top: 14px;">
                <button type="button" class="btn-secondary" style="height: 38px; padding: 0 16px;" onclick="document.getElementById('adjustStockModal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn-primary" style="height: 38px; padding: 0 20px;">Simpan Penyesuaian</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function adjustStock(variantId, currentStock) {
    document.getElementById('variantId').value = variantId;
    document.getElementById('currentStock').textContent = currentStock + ' pcs';
    document.getElementById('adjustStockModal').style.display = 'flex';
}
</script>
@endsection
