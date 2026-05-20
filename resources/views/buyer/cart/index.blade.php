@extends('layouts.buyer')
@section('title', 'Keranjang — MOVR')
@section('content')

@php
  // Pastikan hanya item yang masih memiliki relasi detail yang valid
  $items = $items->filter(function ($i) {
    return !empty($i->detail);
  })->values();

  $cartItems = $items->map(function ($i) {
    $detail = $i->detail;
    $produk = $detail->produk ?? null;
    $gambarUtama = $produk?->gambarUtama?->url_lengkap
      ?? $produk?->images?->first()?->url_lengkap
      ?? asset('images/placeholder.png');
    return [
      'id' => $i->keranjang_id,
      'checked' => true,
      'qty' => $i->jumlah ?? 1,
      'harga' => $detail->harga ?? ($produk->harga_dasar ?? 0),
      'stok' => $detail->stok ?? 0,
      'nama' => $produk->nama_produk ?? ($detail->nama_produk ?? '-'),
      'gambar' => $gambarUtama,
    ];
  })->filter()->values();
@endphp

<div class="space-y-6">
    <div>
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8"
     x-data="{
       items: @js($cartItems),
       
       get subtotal() {
         return this.items
           .filter(i => i.checked)
           .reduce((s,i) => s + i.harga * i.qty, 0);
       },
       get checkedCount() {
         return this.items.filter(i => i.checked).length;
       },
       get allChecked() {
         return this.items.length > 0 && 
           this.items.every(i => i.checked);
       },
       
       toggleAll(e) {
         this.items.forEach(i => i.checked = e.target.checked);
       },
       
       async updateQty(item, val) {
         const newQty = Math.min(Math.max(1, val), item.stok);
         const old = item.qty;
         item.qty = newQty;
         const res = await fetch('{{ route('cart.update') }}', {
           method: 'POST',
           headers: {
             'Content-Type': 'application/json',
             'X-CSRF-TOKEN': '{{ csrf_token() }}'
           },
           body: JSON.stringify({
             keranjang_id: item.id, jumlah: newQty
           })
         });
         const data = await res.json();
         if (!data.success) {
           item.qty = old;
           showToast(data.message, 'error');
         }
       },
       
       async removeItem(id) {
         const res = await fetch('/cart/remove/' + id, {
           method: 'DELETE',
           headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
         });
         const data = await res.json();
         if (data.success) {
           this.items = this.items.filter(i => i.id !== id);
           const badge = document.getElementById('cart-count');
           if (badge) badge.textContent = data.cart_count;
           showToast('Item dihapus dari keranjang');
         }
       },
       
       checkout() {
         const checked = this.items
           .filter(i => i.checked).map(i => i.id);
         if (!checked.length) {
           showToast('Pilih minimal 1 produk', 'warning');
           return;
         }
         const f = document.createElement('form');
         f.method = 'POST';
         f.action = '{{ route('checkout.store') }}';
         const csrf = document.createElement('input');
         csrf.type = 'hidden'; 
         csrf.name = '_token'; 
         csrf.value = '{{ csrf_token() }}';
         f.appendChild(csrf);
         const ids = document.createElement('input');
         ids.type = 'hidden'; 
         ids.name = 'keranjang_ids';
         ids.value = JSON.stringify(checked);
         f.appendChild(ids);
         document.body.appendChild(f);
         f.submit();
       },
       
       formatRp(n) {
         return 'Rp ' + n.toLocaleString('id-ID');
       }
     }">

  <h1 class="text-2xl font-black text-gray-900 mb-8">
    Keranjang Belanja
    <span class="text-gray-400 font-normal text-lg ml-2">
      ({{ $items->count() }} item)
    </span>
  </h1>

  @if($items->isEmpty())
  {{-- Empty State --}}
  <div class="flex flex-col items-center justify-center 
              py-24 text-center">
    <div class="w-24 h-24 bg-[#63A2BB]/10 rounded-full 
                flex items-center justify-center mb-6">
      <svg class="w-12 h-12 text-[#63A2BB]" 
           fill="none" stroke="currentColor" 
           viewBox="0 0 24 24">
        <path stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="1.5"
              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
      </svg>
    </div>
    <h2 class="text-xl font-bold text-gray-700 mb-2">
      Keranjang masih kosong
    </h2>
    <p class="text-gray-400 mb-8">
      Yuk tambahkan produk favoritmu!
    </p>
    <a href="/" class="btn-primary">
      Mulai Belanja
    </a>
  </div>
  
  @else
  
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    
    {{-- KIRI: List Item --}}
    <div class="lg:col-span-8 space-y-3">
      
      {{-- Header Pilih Semua --}}
      <div class="bg-white rounded-2xl px-5 py-4 
                  shadow-sm flex items-center gap-3">
        <input type="checkbox" 
               id="pilih-semua"
               :checked="allChecked"
               @change="toggleAll($event)"
               class="w-4 h-4 rounded accent-[#63A2BB] 
                      cursor-pointer">
        <label for="pilih-semua" 
               class="text-sm font-semibold text-gray-700 
                      cursor-pointer">
          Pilih Semua
        </label>
        <span class="text-sm text-gray-400 ml-auto">
          <span x-text="checkedCount"></span> 
          dari {{ $items->count() }} dipilih
        </span>
      </div>

      {{-- Cart Items --}}
      @foreach($items as $item)
      <div id="cart-item-{{ $item->keranjang_id }}"
           class="bg-white rounded-2xl p-4 md:p-5 
                  shadow-sm flex gap-4 transition-all">
        
        {{-- Checkbox --}}
        <input type="checkbox"
               x-model="items.find(
                 i => i.id === {{ $item->keranjang_id }})
                 .checked"
               class="w-4 h-4 mt-2 rounded 
                      accent-[#63A2BB] cursor-pointer 
                      flex-shrink-0">
        
        {{-- Info --}}
        <a href="{{ route('product.show', $item->detail->produk->slug) }}"
           class="flex-shrink-0">
          <img src="{{ $item->detail->produk->gambarUtama?->url_lengkap ?? $item->detail->produk->images->first()?->url_lengkap ?? asset('images/placeholder.png') }}"
               alt="{{ $item->detail->produk->nama_produk }}"
               class="w-20 h-20 md:w-24 md:h-24 rounded-2xl object-cover">
        </a>

        <div class="flex-1 min-w-0">
          <a href="{{ route('product.show', 
                      $item->detail->produk->slug) }}"
             class="font-semibold text-gray-800 
                    hover:text-[#63A2BB] line-clamp-2 
                    text-sm transition">
            {{ $item->detail->produk->nama_produk }}
          </a>
          
          <div class="flex flex-wrap gap-2 mt-2">
            @if($item->detail->ukuran)
            <span class="text-xs bg-gray-100 
                         text-gray-600 px-2.5 py-1 
                         rounded-full font-medium">
              Size: {{ $item->detail->ukuran }}
            </span>
            @endif
            @if($item->detail->warna)
            <span class="text-xs bg-gray-100 
                         text-gray-600 px-2.5 py-1 
                         rounded-full font-medium 
                         flex items-center gap-1.5">
              <span class="w-2.5 h-2.5 rounded-full"
                    style="background: {{ $item->detail->warna->kode_hex ?? '#ccc' }}">
              </span>
              {{ $item->detail->warna->nama_warna }}
            </span>
            @endif
          </div>

          <div class="flex items-center 
                      justify-between mt-3 gap-3">
            
            {{-- Harga --}}
            <span class="text-base font-black 
                         text-[#63A2BB]">
              Rp {{ number_format(
                $item->detail->harga,0,',','.') }}
            </span>

            {{-- Qty Control --}}
            <div class="flex items-center gap-1">
              <button @click="updateQty(
                        items.find(i => i.id === 
                          {{ $item->keranjang_id }}), 
                        items.find(i => i.id === 
                          {{ $item->keranjang_id }}).qty - 1)"
                      class="w-8 h-8 rounded-xl border-2 
                             border-gray-200 flex items-center 
                             justify-center text-gray-500 
                             hover:border-[#63A2BB] 
                             hover:text-[#63A2BB] 
                             font-bold transition text-sm">
                −
              </button>
              <span x-text="items.find(
                      i => i.id === {{ $item->keranjang_id }})
                      ?.qty ?? {{ $item->jumlah }}"
                    class="w-10 text-center font-bold 
                           text-sm text-gray-800">
              </span>
              <button @click="updateQty(
                        items.find(i => i.id === 
                          {{ $item->keranjang_id }}), 
                        items.find(i => i.id === 
                          {{ $item->keranjang_id }}).qty + 1)"
                      class="w-8 h-8 rounded-xl border-2 
                             border-gray-200 flex items-center 
                             justify-center text-gray-500 
                             hover:border-[#63A2BB] 
                             hover:text-[#63A2BB] 
                             font-bold transition text-sm">
                +
              </button>
            </div>

            {{-- Hapus --}}
            <button @click="removeItem(
                      {{ $item->keranjang_id }})"
                    class="p-2 text-gray-400 
                           hover:text-red-500 
                           hover:bg-red-50 rounded-xl 
                           transition">
              <svg class="w-4 h-4" fill="none" 
                   stroke="currentColor" 
                   viewBox="0 0 24 24">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 
                         21H7.862a2 2 0 01-1.995-1.858L5 
                         7m5 4v6m4-6v6m1-10V4a1 1 0 00-1
                         -1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    {{-- KANAN: Ringkasan --}}
    <div class="lg:col-span-4">
      <div class="bg-white rounded-3xl p-6 shadow-sm 
                  sticky top-20">
        <h3 class="font-bold text-gray-800 mb-5 text-base">
          Ringkasan Belanja
        </h3>
        
        <div class="space-y-3 text-sm">
          <div class="flex justify-between text-gray-600">
            <span>
              Subtotal 
              (<span x-text="checkedCount"></span> produk)
            </span>
            <span x-text="formatRp(subtotal)" 
                  class="font-semibold text-gray-800">
            </span>
          </div>
          <div class="flex justify-between text-gray-400">
            <span>Estimasi ongkir</span>
            <span>Dihitung saat checkout</span>
          </div>
        </div>

        <div class="border-t border-gray-100 my-5"></div>
        
        <div class="flex justify-between text-base mb-5">
          <span class="font-bold text-gray-800">Total</span>
          <span x-text="formatRp(subtotal)"
                class="font-black text-[#63A2BB] text-lg">
          </span>
        </div>

        <button @click="checkout()"
                :disabled="checkedCount === 0"
                :class="checkedCount === 0 
                  ? 'opacity-50 cursor-not-allowed' 
                  : 'hover:-translate-y-1 hover:shadow-lg'"
                class="w-full bg-[#63A2BB] text-white 
                       py-4 rounded-2xl font-bold text-sm 
                       flex items-center justify-center 
                       gap-2 transition-all duration-200">
          Checkout
          (<span x-text="checkedCount"></span> item)
          <svg class="w-4 h-4" fill="none" 
               stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>

        <a href="/"
           class="mt-3 w-full block text-center 
                  text-[#63A2BB] text-sm font-medium 
                  hover:underline">
          ← Lanjut Belanja
        </a>
      </div>
    </div>
  </div>
  @endif
</div>

@endsection
