@extends('layouts.admin')

@section('title', 'Create Supplier Order')

@section('content')
<div style="padding: 32px; max-width: 1000px; margin: 0 auto;">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 28px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.15em; margin: 0 0 6px;">Purchase Order</p>
            <h1 class="page-header-title" style="margin: 0; font-size: 28px; font-weight: 800; tracking: -0.5px; color: #0F172A;">Buat Purchase Order Baru</h1>
            <p class="page-header-sub" style="margin: 4px 0 0; color: #64748B; font-size: 14px;">Pilih supplier, masukkan produk varian, tentukan kuantitas pesanan beserta harga beli modal.</p>
        </div>
        <div>
            <a href="{{ route('admin.supplier-order.index') }}" class="btn-secondary" style="height: 40px; display: inline-flex; align-items: center; justify-content: center; padding: 0 16px; font-size: 13px; font-weight: 600; border-radius: 10px;">← Batal</a>
        </div>
    </div>

    @if(session('error'))
        <div style="margin-bottom: 20px; padding: 14px 20px; background: #FEF2F2; border: 1.5px solid #FEE2E2; color: #DC2626; border-radius: 14px; font-size: 13.5px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.supplier-order.store') }}" x-data="orderForm()" style="display: flex; flex-direction: column; gap: 24px;">
        @csrf

        {{-- Supplier Selection --}}
        <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); background: white;">
            <div class="panel-header" style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9;">
                <p class="panel-title" style="font-size: 15px; font-weight: 800; color: #1E293B;">Pilih Supplier Rekanan</p>
            </div>
            <div class="panel-body" style="padding: 24px;">
                <div>
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Supplier Partner <span style="color: #EF4444;">*</span></label>
                    <select name="supplier_id" required @change="selectedSupplier = $el.value" class="form-input" style="height: 44px; cursor: pointer; font-size: 14px; border-radius: 10px; padding: 0 12px; background-color: #F8FAFC;">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supp)
                            <option value="{{ $supp->supplier_id }}">{{ $supp->nama_toko }} (Owner: {{ $supp->nama_owner }})</option>
                        @endforeach
                    </select>
                </div>
                @error('supplier_id')
                    <p style="color: #EF4444; font-size: 11px; font-weight: 700; margin: 6px 0 0;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Items Section --}}
        <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); background: white;">
            <div class="panel-header" style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                <p class="panel-title" style="font-size: 15px; font-weight: 800; color: #1E293B;">Daftar Item PO Produk</p>
                <button type="button" @click="addItem()" class="btn-primary" style="padding: 0 16px; font-size: 12.5px; height: 36px; border-radius: 10px; background: #10B981; font-weight: 700; border: none; box-shadow: 0 4px 12px rgba(16,185,129,0.25); cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Item PO
                </button>
            </div>

            <div class="panel-body" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                
                <div style="display: flex; flex-direction: column; gap: 16px;" x-show="items.length > 0">
                    <template x-for="(item, idx) in items" :key="idx">
                        <div style="border: 1.5px solid #E2E8F0; border-radius: 16px; padding: 20px; background: #F8FAFC; position: relative; transition: all 0.2s ease;">
                            
                            <div style="display: grid; grid-template-columns: 2fr 1fr 1.2fr 100px; gap: 16px; align-items: flex-end;">
                                {{-- Produk Select --}}
                                <div>
                                    <label class="form-label" style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase; margin-bottom: 6px;">Produk Varian <span style="color: #EF4444;">*</span></label>
                                    <select x-model="item.detail_produk_id" required class="form-input" style="height: 40px; cursor: pointer; font-size: 13px; border-radius: 10px; background-color: white;">
                                        <option value="">-- Pilih Produk Varian --</option>
                                        @foreach(($detailProducts ?? []) as $detail)
                                            <option value="{{ $detail->detail_produk_id }}">
                                                {{ $detail->produk?->nama_produk ?? '-' }}
                                                @if($detail->warna?->nama_warna)
                                                    - {{ $detail->warna->nama_warna }}
                                                @endif
                                                @if($detail->ukuran)
                                                    (Size: {{ $detail->ukuran }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Qty --}}
                                <div>
                                    <label class="form-label" style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase; margin-bottom: 6px;">Quantity (Unit) <span style="color: #EF4444;">*</span></label>
                                    <input type="number" x-model.number="item.qty" min="1" required class="form-input" style="height: 40px; font-weight: 800; font-family: monospace; border-radius: 10px; background-color: white; font-size: 13.5px; padding: 0 12px;">
                                </div>

                                {{-- Harga Beli --}}
                                <div>
                                    <label class="form-label" style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase; margin-bottom: 6px;">Harga Beli Modal (Rp) <span style="color: #EF4444;">*</span></label>
                                    <input type="number" x-model.number="item.harga_beli" min="0" step="500" required class="form-input" style="height: 40px; font-weight: 800; font-family: monospace; border-radius: 10px; background-color: white; font-size: 13.5px; padding: 0 12px;">
                                </div>

                                {{-- Remove Button --}}
                                <div>
                                    <button type="button" @click="removeItem(idx)" class="btn-danger" style="width: 100%; height: 40px; justify-content: center; font-weight: 700; border-radius: 10px; display: inline-flex; align-items: center; gap: 4px; border: 1.5px solid #FEE2E2; background-color: #FEF2F2; color: #EF4444; font-size: 12.5px; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.backgroundColor='#FEE2E2'; this.style.borderColor='#FECACA';" onmouseout="this.style.backgroundColor='#FEF2F2'; this.style.borderColor='#FEE2E2';">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>

                            {{-- Hidden inputs for form submit integration --}}
                            <input type="hidden" :name="`items[${idx}][detail_produk_id]`" :value="item.detail_produk_id">
                            <input type="hidden" :name="`items[${idx}][qty]`" :value="item.qty">
                            <input type="hidden" :name="`items[${idx}][harga_beli]`" :value="item.harga_beli">
                        </div>
                    </template>
                </div>

                <div x-show="items.length === 0" style="text-align: center; padding: 40px 20px; color: #94A3B8; border: 2px dashed #E2E8F0; border-radius: 16px; background: #F8FAFC;">
                    <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="margin: 0 auto 10px; opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <p style="font-size: 13.5px; font-weight: 600; margin: 0 0 14px; color: #64748B;">Belum ada item pesanan yang ditambahkan.</p>
                    <button type="button" @click="addItem()" class="btn-secondary" style="font-size: 12.5px; padding: 8px 16px; font-weight: 700; border-radius: 10px; cursor: pointer; border: 1.5px solid #E2E8F0; background: white; color: #475569;">+ Tambah Baris Baru</button>
                </div>

                @error('items')
                    <p style="color: #EF4444; font-size: 11px; font-weight: 700; margin: 6px 0 0;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Catatan --}}
        <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); background: white;">
            <div class="panel-header" style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9;">
                <p class="panel-title" style="font-size: 15px; font-weight: 800; color: #1E293B;">Catatan Tambahan PO</p>
            </div>
            <div class="panel-body" style="padding: 24px;">
                <textarea name="catatan" rows="3" class="form-input" style="height: auto; min-height: 90px; padding: 12px; resize: vertical; border-radius: 10px; font-size: 13.5px; background-color: #F8FAFC;" placeholder="Tuliskan catatan khusus perihal estimasi pengiriman, diskon supplier, termin pembayaran dll..."></textarea>
            </div>
        </div>

        {{-- Summary Cards (Grid 3 Columns) --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
            <div class="panel" style="padding: 20px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); background: white;">
                <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; margin: 0 0 8px; letter-spacing: 0.05em;">Total Kuantitas</p>
                <p style="font-size: 24px; font-weight: 900; color: #1E293B; margin: 0; font-family: monospace;" x-text="totalItems() + ' pcs'">0 pcs</p>
            </div>
            
            <div class="panel" style="padding: 20px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); background: white;">
                <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; margin: 0 0 8px; letter-spacing: 0.05em;">Rata-rata Harga Unit</p>
                <p style="font-size: 24px; font-weight: 900; color: #334155; margin: 0; font-family: monospace;" x-text="'Rp ' + formatCurrency(avgUnitPrice())">Rp 0</p>
            </div>

            <div class="panel" style="padding: 20px; border-radius: 16px; border: 1px solid rgba(99,162,187,0.25); box-shadow: 0 4px 20px -2px rgba(99, 162, 187, 0.05); background: rgba(99,162,187,0.03);">
                <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; margin: 0 0 8px; letter-spacing: 0.05em;">Total Nilai PO</p>
                <p style="font-size: 24px; font-weight: 900; color: #63A2BB; margin: 0; font-family: monospace;" x-text="'Rp ' + formatCurrency(totalPrice())">Rp 0</p>
            </div>
        </div>

        {{-- Actions --}}
        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 12px; border-top: 1.5px solid #E2E8F0; padding-top: 24px;">
            <a href="{{ route('admin.supplier-order.index') }}" class="btn-secondary" style="height: 44px; padding: 0 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 13.5px; font-weight: 600; border-radius: 10px;">Batal</a>
            <button type="submit" class="btn-primary" style="height: 44px; padding: 0 28px; font-size: 13.5px; font-weight: 700; border-radius: 10px; background: #63A2BB; border: none; box-shadow: 0 4px 12px rgba(99,162,187,0.3); cursor: pointer;">
                Simpan PO
            </button>
        </div>
    </form>
</div>

<script>
function orderForm() {
    return {
        items: [],
        selectedSupplier: null,

        addItem() {
            this.items.push({
                detail_produk_id: '',
                qty: 1,
                harga_beli: 0,
            });
        },

        removeItem(idx) {
            this.items.splice(idx, 1);
        },

        totalItems() {
            return this.items.reduce((sum, item) => sum + (item.qty || 0), 0);
        },

        totalPrice() {
            return this.items.reduce((sum, item) => sum + ((item.qty || 0) * (item.harga_beli || 0)), 0);
        },

        avgUnitPrice() {
            const total = this.totalPrice();
            const count = this.items.length;
            return count > 0 ? total / count : 0;
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID').format(Math.floor(value || 0));
        }
    }
}
</script>
@endsection
