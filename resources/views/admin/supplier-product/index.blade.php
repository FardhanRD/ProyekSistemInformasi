@extends('layouts.admin')

@section('title', 'Supplier Product Link')

@section('content')
<div style="padding: 28px 28px 40px;">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 24px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 4px;">Inventori & Suplai</p>
            <h1 class="page-header-title" style="margin: 0;">Supplier Product Link</h1>
            <p class="page-header-sub" style="margin: 0; color: #94A3B8;">Hubungkan produk master dengan supplier rekanan untuk menetapkan besaran harga modal beli.</p>
        </div>
        <div>
            <button type="button" onclick="document.getElementById('addRelationModal').style.display = 'flex'" class="btn-primary" style="display: inline-flex; align-items: center; gap: 6px; height: 38px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Relation
            </button>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="panel" style="margin-bottom: 24px; padding: 18px 20px;">
        <form method="GET" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label" style="margin-bottom: 6px;">Filter Supplier</label>
                <select name="supplier_id" class="form-input" style="height: 38px; cursor: pointer;">
                    <option value="">All Suppliers (Semua)</option>
                    @foreach($supplier_list as $s)
                        <option value="{{ $s->supplier_id }}" {{ ($supplier_filter ?? '') == $s->supplier_id ? 'selected' : '' }}>
                            {{ $s->nama_toko }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label class="form-label" style="margin-bottom: 6px;">Filter Produk</label>
                <select name="produk_id" class="form-input" style="height: 38px; cursor: pointer;">
                    <option value="">All Products (Semua)</option>
                    @foreach($produk_list as $p)
                        <option value="{{ $p->produk_id }}" {{ ($produk_filter ?? '') == $p->produk_id ? 'selected' : '' }}>
                            {{ $p->nama_produk }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-primary" style="height: 38px; padding: 0 16px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('supplier_id') || request('produk_id'))
                    <a href="{{ route('admin.supplier-product.index') }}" class="btn-secondary" style="height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 14px;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Relations Table Panel --}}
    <div class="panel">
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Supplier / Toko</th>
                        <th>Master Produk</th>
                        <th style="text-align: right; width: 180px;">Harga Modal Beli</th>
                        <th>Catatan Keterangan</th>
                        <th style="text-align: center; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($relations as $rel)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0F172A;">{{ $rel->supplier->nama_toko ?? '-' }}</div>
                                <div style="font-size: 11px; color: #94A3B8;">Owner: {{ $rel->supplier->nama_owner ?? '-' }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #334155;">{{ $rel->produk->nama_produk ?? '-' }}</div>
                            </td>
                            <td style="text-align: right; font-weight: 800; color: #0F172A; font-family: monospace;">
                                Rp {{ number_format($rel->harga_modal ?? 0, 0, ',', '.') }}
                            </td>
                            <td style="color: #64748B; font-size: 12.5px;">{{ $rel->catatan ?: '-' }}</td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                    <button type="button" class="btn-secondary" style="padding: 6px 10px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" onclick="editRelation({{ $rel->produk_supplier_id }}, {{ $rel->harga_modal }}, '{{ $rel->catatan }}')">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        Edit
                                    </button>
                                    
                                    <form method="POST" action="{{ route('admin.supplier-product.destroy', $rel->produk_supplier_id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus relasi supplier produk ini?')" style="display: inline; margin:0;">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger" style="padding: 6px 10px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94A3B8; padding: 36px;">
                                Belum ada relasi harga beli produk dari supplier.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($relations ?? null, 'links') && $relations->hasPages())
            <div style="border-top: 1px solid #F1F5F9; padding: 16px 20px; display: flex; justify-content: center;">
                {{ $relations->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Add Relation Modal --}}
<div id="addRelationModal" x-data="{}" style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); width: 100%; max-width: 440px; overflow: hidden; border: 1px solid #E2E8F0;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <div style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;">Tambah Relasi Supplier-Produk</h3>
            <button type="button" style="background: none; border: none; cursor: pointer; color: #94A3B8; font-size: 16px;" onclick="document.getElementById('addRelationModal').style.display = 'none'">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.supplier-product.store') }}" style="padding: 20px 24px; margin: 0; display: flex; flex-direction: column; gap: 14px;">
            @csrf

            <div>
                <label class="form-label">Pilih Supplier Partner <span style="color: #EF4444;">*</span></label>
                <select name="supplier_id" required class="form-input" style="height: 38px; cursor: pointer;">
                    <option value="">Pilih supplier...</option>
                    @foreach($supplier_list as $s)
                        <option value="{{ $s->supplier_id }}">{{ $s->nama_toko }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Pilih Master Produk <span style="color: #EF4444;">*</span></label>
                <select name="produk_id" required class="form-input" style="height: 38px; cursor: pointer;">
                    <option value="">Pilih produk...</option>
                    @foreach($produk_list as $p)
                        <option value="{{ $p->produk_id }}">{{ $p->nama_produk }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Harga Modal Beli (Rupiah) <span style="color: #EF4444;">*</span></label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 14px; font-size: 13px; font-weight: 700; color: #64748B; pointer-events: none;">Rp</span>
                    <input type="number" name="harga_modal" required min="0" class="form-input" style="padding-left: 38px; height: 38px; font-weight: 700; font-family: monospace;">
                </div>
            </div>

            <div>
                <label class="form-label">Catatan Tambahan</label>
                <textarea name="catatan" rows="3" placeholder="Contoh: Ongkir luar kota di tanggung buyer..." class="form-input" style="height: auto; min-height: 80px; padding: 8px 12px; resize: vertical;"></textarea>
            </div>

            <div style="margin-top: 8px; display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #F1F5F9; padding-top: 14px;">
                <button type="button" class="btn-secondary" style="height: 38px; padding: 0 16px;" onclick="document.getElementById('addRelationModal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn-primary" style="height: 38px; padding: 0 20px;">Simpan Relasi</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Relation Modal --}}
<div id="editRelationModal" x-data="{}" style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px;" @click.self="document.getElementById('editRelationModal').style.display = 'none'">
    <div style="background: white; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); width: 100%; max-width: 400px; overflow: hidden; border: 1px solid #E2E8F0;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <div style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;">Ubah Detail Relasi</h3>
            <button type="button" style="background: none; border: none; cursor: pointer; color: #94A3B8; font-size: 16px;" onclick="document.getElementById('editRelationModal').style.display = 'none'">✕</button>
        </div>

        <form id="editRelationForm" method="POST" style="padding: 20px 24px; margin: 0; display: flex; flex-direction: column; gap: 14px;">
            @csrf 
            @method('PUT')

            <div>
                <label class="form-label">Harga Modal Beli Baru <span style="color: #EF4444;">*</span></label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 14px; font-size: 13px; font-weight: 700; color: #64748B; pointer-events: none;">Rp</span>
                    <input type="number" id="editHargaModal" name="harga_modal" required min="0" class="form-input" style="padding-left: 38px; height: 38px; font-weight: 700; font-family: monospace;">
                </div>
            </div>

            <div>
                <label class="form-label">Ubah Catatan</label>
                <textarea id="editCatatan" name="catatan" rows="3" placeholder="Ubah deskripsi..." class="form-input" style="height: auto; min-height: 80px; padding: 8px 12px; resize: vertical;"></textarea>
            </div>

            <div style="margin-top: 8px; display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #F1F5F9; padding-top: 14px;">
                <button type="button" class="btn-secondary" style="height: 38px; padding: 0 16px;" onclick="document.getElementById('editRelationModal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn-primary" style="height: 38px; padding: 0 20px;">Update Relasi</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function editRelation(relationId, hargaModal, catatan) {
    document.getElementById('editHargaModal').value = hargaModal;
    document.getElementById('editCatatan').value = catatan;
    document.getElementById('editRelationForm').action = `/admin/supplier-product/${relationId}`;
    document.getElementById('editRelationModal').style.display = 'flex';
}
</script>
@endsection
