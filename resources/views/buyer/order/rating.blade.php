@extends('layouts.buyer')
@section('title', 'Beri Rating — MOVR')
@section('content')

<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8" x-data="ratingData()">

  {{-- Header --}}
  <div class="text-center mb-8">
    <div class="w-16 h-16 bg-amber-50 rounded-full 
                flex items-center justify-center mx-auto mb-3">
      <span class="text-3xl">⭐</span>
    </div>
    <h1 class="text-xl font-black text-gray-900">
      Beri Penilaian
    </h1>
    <p class="text-sm text-gray-400 mt-1">
      Bagaimana pengalaman belanja kamu?
    </p>
  </div>

  {{-- Step Indicator --}}
  <div class="flex items-center justify-center gap-2 mb-8">
    <div :class="step >= 1 
           ? 'bg-[#63A2BB] text-white' 
           : 'bg-gray-200 text-gray-400'"
         class="w-8 h-8 rounded-full flex items-center 
                justify-center text-sm font-bold transition">
      <span x-show="step > 1">✓</span>
      <span x-show="step <= 1">1</span>
    </div>
    <div class="w-16 h-0.5"
         :class="step > 1 ? 'bg-[#63A2BB]' : 'bg-gray-200'">
    </div>
    <div :class="step >= 2 
           ? 'bg-[#63A2BB] text-white' 
           : 'bg-gray-200 text-gray-400'"
         class="w-8 h-8 rounded-full flex items-center 
                justify-center text-sm font-bold transition">
      2
    </div>
    <p class="text-xs text-gray-400 ml-2">
      <span x-text="step === 1 
        ? 'Rating Produk' 
        : 'Rating Toko'">
      </span>
    </p>
  </div>

  {{-- STEP 1: Rating Produk --}}
  <div x-show="step === 1" class="space-y-4">
    
    <template x-for="(item, idx) in produkRatings" 
              :key="idx">
      <div class="bg-white rounded-3xl p-5 shadow-sm">
        
        {{-- Info Produk --}}
        <div class="flex items-center gap-3 mb-4 
                    pb-4 border-b border-gray-100">
          <img :src="item.gambar || '/images/placeholder.png'"
               :alt="item.nama"
               class="w-14 h-14 rounded-2xl object-cover 
                      flex-shrink-0 bg-gray-50">
          <p class="font-semibold text-sm text-gray-800 
                     line-clamp-2" 
             x-text="item.nama">
          </p>
        </div>
        
        {{-- Bintang --}}
        <div class="mb-4">
          <p class="text-sm font-semibold text-gray-700 mb-3">
            Rating Produk
          </p>
          <div class="flex gap-2">
            <template x-for="star in [1,2,3,4,5]" :key="star">
              <button type="button"
                      @click="setBintang(idx, star)"
                      class="text-3xl transition-transform hover:scale-110"
                      :class="star <= item.bintang ? 'opacity-100' : 'opacity-40'">
                ★
              </button>
            </template>
          </div>
          {{-- Label bintang --}}
          <p class="text-xs text-gray-400 mt-2 h-4"
             x-text="['','Sangat Buruk','Kurang Baik','Cukup','Bagus','Sangat Bagus'][item.bintang]">
          </p>
        </div>

        {{-- Judul Ulasan --}}
        <div class="mb-3">
          <input type="text"
                 x-model="item.judul"
                 placeholder="Judul ulasan (opsional)"
                 class="w-full px-4 py-3 rounded-2xl 
                        border-2 border-gray-200 
                        focus:border-[#63A2BB] 
                        focus:outline-none text-sm 
                        transition">
        </div>
        
        {{-- Isi Ulasan --}}
        <textarea x-model="item.isi"
                  rows="3"
                  placeholder="Ceritakan pengalaman kamu dengan produk ini..."
                  class="w-full px-4 py-3 rounded-2xl 
                         border-2 border-gray-200 
                         focus:border-[#63A2BB] 
                         focus:outline-none text-sm 
                         transition resize-none">
        </textarea>
      </div>
    </template>

    {{-- Tombol Lanjut ke Step 2 --}}
    <button @click="validateAndNext()"
            type="button"
            class="w-full py-3 rounded-2xl bg-[#63A2BB] 
                   text-white font-bold hover:bg-[#4A8BA3] 
                   transition mt-6">
      Lanjut ke Rating Toko →
    </button>
  </div>

  {{-- STEP 2: Rating Toko --}}
  <div x-show="step === 2" class="space-y-6">
    
    {{-- Pelayanan --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm">
      <p class="text-sm font-semibold text-gray-700 mb-4">
        Pelayanan Toko
      </p>
      <div class="flex gap-2 mb-3">
        <template x-for="star in [1,2,3,4,5]" :key="star">
          <button type="button"
                  @click="setTokoRating('pelayanan', star)"
                  class="text-3xl transition-transform hover:scale-110"
                  :class="star <= tokoRating.pelayanan ? 'opacity-100' : 'opacity-40'">
            ★
          </button>
        </template>
      </div>
      <p class="text-xs text-gray-400 h-4"
         x-text="['','Sangat Buruk','Kurang Baik','Cukup','Bagus','Sangat Bagus'][tokoRating.pelayanan]">
      </p>
    </div>

    {{-- Aplikasi MOVR --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm">
      <p class="text-sm font-semibold text-gray-700 mb-4">
        Aplikasi MOVR
      </p>
      <div class="flex gap-2 mb-3">
        <template x-for="star in [1,2,3,4,5]" :key="star">
          <button type="button"
                  @click="setTokoRating('aplikasi', star)"
                  class="text-3xl transition-transform hover:scale-110"
                  :class="star <= tokoRating.aplikasi ? 'opacity-100' : 'opacity-40'">
            ★
          </button>
        </template>
      </div>
      <p class="text-xs text-gray-400 h-4"
         x-text="['','Sangat Buruk','Kurang Baik','Cukup','Bagus','Sangat Bagus'][tokoRating.aplikasi]">
      </p>
    </div>

    {{-- Komentar --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm">
      <label class="text-sm font-semibold text-gray-700 mb-3 block">
        Komentar Tambahan (Opsional)
      </label>
      <textarea x-model="tokoRating.komentar"
                rows="4"
                placeholder="Ceritakan saran dan masukan kamu..."
                class="w-full px-4 py-3 rounded-2xl 
                       border-2 border-gray-200 
                       focus:border-[#63A2BB] 
                       focus:outline-none text-sm 
                       transition resize-none">
      </textarea>
    </div>

    {{-- Tombol --}}
    <div class="flex gap-3 mt-6">
      <button @click="step = 1"
              type="button"
              class="flex-1 py-3 rounded-2xl bg-gray-200 
                     text-gray-700 font-bold hover:bg-gray-300 
                     transition">
        ← Kembali
      </button>
      <button @click="submitAll()"
              type="button"
              class="flex-1 py-3 rounded-2xl bg-[#63A2BB] 
                     text-white font-bold hover:bg-[#4A8BA3] 
                     transition">
        ✓ Kirim Rating
      </button>
    </div>
  </div>
</div>

<script>
function ratingData() {
  return {
    step: 1,
    produkRatings: @json($produkRatings),
    tokoRating: {
      pelayanan: 0,
      aplikasi: 0,
      komentar: ''
    },
    
    setBintang(idx, val) {
      this.produkRatings[idx].bintang = val;
    },
    
    setTokoRating(key, val) {
      this.tokoRating[key] = val;
    },

    validateAndNext() {
      const allRated = this.produkRatings.every(p => p.bintang > 0);
      if (!allRated) {
        showToast('Mohon berikan rating untuk semua produk', 'error');
        return;
      }
      this.step = 2;
    },
    
    async submitAll() {
      if (!this.tokoRating.pelayanan || !this.tokoRating.aplikasi) {
        showToast('Mohon berikan rating untuk pelayanan dan aplikasi', 'error');
        return;
      }

      try {
        const res = await fetch(
          '{{ route('orders.rating.store', $transaksi->kode_transaksi) }}',
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
              produk_ratings: this.produkRatings,
              toko_rating: this.tokoRating
            })
          }
        );
        
        const data = await res.json();
        if (data.success) {
          showToast('✅ Rating berhasil dikirim! Terima kasih');
          setTimeout(() => {
            window.location.href = '{{ route('orders.index') }}';
          }, 1500);
        } else {
          showToast(data.message ?? 'Gagal submit rating', 'error');
        }
      } catch (error) {
        showToast('Gagal submit rating', 'error');
        console.error(error);
      }
    }
  };
}
</script>

@endsection
