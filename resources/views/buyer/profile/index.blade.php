@extends('layouts.buyer')

@section('title', __('ui.profile_my') . ' — MOVR')

@section('content')
@php
    use App\Models\Wishlist;

    $user = auth()->user();
    $userName = $user->nama_pengguna ?? $user->name ?? $user->username ?? 'User';
    $userEmail = $user->email ?? '';
    $userPhoto = $user->foto_profil ?? null;
    $orderCount = isset($orderCount) ? $orderCount : count($orders ?? []);
    $wishlistOwnerColumn = Wishlist::ownerColumn();
    $wishlistOwnerId = Wishlist::resolveOwnerId($user);
    $wishlistCount = $wishlistOwnerId ? Wishlist::where($wishlistOwnerColumn, $wishlistOwnerId)->count() : 0;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="{ activeTab: '{{ request('tab', 'profil') }}' }"
     x-init="if (window.location.hash) activeTab = window.location.hash.substring(1)"
     @hashchange.window="activeTab = window.location.hash.substring(1) || activeTab">
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <div class="lg:col-span-3 space-y-4 lg:sticky lg:top-8 self-start">
      <div class="bg-white rounded-3xl p-5 shadow-sm text-center">
        <div class="relative w-20 h-20 mx-auto mb-3">
          @if($userPhoto)
            <img src="{{ Storage::url($userPhoto) }}"
                 alt="Profile"
                 class="w-20 h-20 rounded-full object-cover ring-4 ring-[#63A2BB]/20">
          @else
            <div class="w-20 h-20 rounded-full bg-[#63A2BB] flex items-center justify-center text-white text-2xl font-black">
              {{ strtoupper(substr($userName, 0, 2)) }}
            </div>
          @endif
          <div class="absolute bottom-0 right-0 w-6 h-6 bg-green-400 rounded-full border-2 border-white"></div>
        </div>

        <p class="font-bold text-gray-800 text-base">{{ $userName }}</p>
        <p class="text-xs text-gray-400 mt-0.5">{{ $user->username ?? '' }}</p>
        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $userEmail }}</p>

        <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-gray-100">
          <div class="text-center">
            <p class="font-black text-[#63A2BB] text-lg">{{ $orderCount }}</p>
            <p class="text-[11px] text-gray-400">{{ __('ui.orders_total') }}</p>
          </div>
          <div class="text-center">
            <p class="font-black text-[#63A2BB] text-lg">{{ $wishlistCount }}</p>
            <p class="text-[11px] text-gray-400">Wishlist</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-3 shadow-sm">
        <button @click="activeTab = 'profil'; history.replaceState({}, '', '?tab=profil')"
                :class="activeTab === 'profil' ? 'bg-[#63A2BB]/10 text-[#63A2BB]' : 'text-gray-500 hover:bg-gray-50'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all text-left mb-1 last:mb-0">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          {{ __('ui.data_diri') }}
        </button>
        <button @click="activeTab = 'pesanan'; history.replaceState({}, '', '?tab=pesanan')"
                :class="activeTab === 'pesanan' ? 'bg-[#63A2BB]/10 text-[#63A2BB]' : 'text-gray-500 hover:bg-gray-50'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all text-left mb-1 last:mb-0">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7l1 13h12l1-13M9 11h6"/>
          </svg>
          My Orders
        </button>
        <button @click="activeTab = 'alamat'; history.replaceState({}, '', '?tab=alamat')"
                :class="activeTab === 'alamat' ? 'bg-[#63A2BB]/10 text-[#63A2BB]' : 'text-gray-500 hover:bg-gray-50'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all text-left mb-1 last:mb-0">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          </svg>
          {{ __('ui.alamat') }}
        </button>
        <button @click="activeTab = 'pembayaran'; history.replaceState({}, '', '?tab=pembayaran')"
                :class="activeTab === 'pembayaran' ? 'bg-[#63A2BB]/10 text-[#63A2BB]' : 'text-gray-500 hover:bg-gray-50'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all text-left mb-1 last:mb-0">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
          {{ __('ui.payment_method') }}
        </button>
        <button @click="activeTab = 'keamanan'; history.replaceState({}, '', '?tab=keamanan')"
                :class="activeTab === 'keamanan' ? 'bg-[#63A2BB]/10 text-[#63A2BB]' : 'text-gray-500 hover:bg-gray-50'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all text-left mb-1 last:mb-0">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          {{ __('ui.security') }}
        </button>

        <div class="mt-3 pt-3 border-t border-gray-100">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold text-red-500 hover:bg-red-50 transition-all text-left">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              {{ __('ui.logout') }}
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="lg:col-span-9 space-y-6">
      @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
          {{ session('success') }}
        </div>
      @endif
      @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div x-show="activeTab === 'profil'"
           x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 translate-y-2"
           x-transition:enter-end="opacity-100 translate-y-0">
        <div class="bg-white rounded-3xl p-6 shadow-sm">
          <div class="flex items-center justify-between mb-6">
            <h2 class="font-bold text-gray-800 text-lg">{{ __('ui.data_diri') }}</h2>
          </div>

          <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ui.full_name') }}</label>
                <input type="text" name="name"
                       value="{{ old('name', $user->nama_pengguna ?? $user->name ?? '') }}"
                       placeholder="{{ __('ui.full_name_placeholder') }}"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition @error('name') border-red-300 @enderror">
                @error('name')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ui.username') }}</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">@</span>
                  <input type="text" name="username"
                         value="{{ old('username', $user->username ?? '') }}"
                         placeholder="{{ __('ui.username') }}"
                         class="w-full pl-8 pr-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition @error('username') border-red-300 @enderror">
                </div>
                @error('username')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ui.email') }}</label>
                <input type="email" name="email"
                       value="{{ old('email', $userEmail) }}"
                       placeholder="email@contoh.com"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition @error('email') border-red-300 @enderror">
                @error('email')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ui.phone_number') }}</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">+62</span>
                  <input type="tel" name="no_telepon"
                         value="{{ old('no_telepon', ltrim($user->no_telepon ?? '', '+62')) }}"
                         placeholder="{{ __('ui.phone_placeholder') }}"
                         class="w-full pl-12 pr-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition @error('no_telepon') border-red-300 @enderror">
                </div>
                @error('no_telepon')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ui.birth_date') }}</label>
                <input type="date" name="tanggal_lahir"
                       value="{{ old('tanggal_lahir', $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('Y-m-d') : '') }}"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:outline-none text-sm transition @error('tanggal_lahir') border-red-300 @enderror">
                @error('tanggal_lahir')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ui.gender') }}</label>
                <select name="jenis_kelamin"
                        class="w-full px-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:outline-none text-sm transition bg-white @error('jenis_kelamin') border-red-300 @enderror">
                  <option value="">{{ __('ui.select_gender') }}</option>
                  <option value="L" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'L' ? 'selected' : '' }}>{{ __('ui.male') }}</option>
                  <option value="P" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'P' ? 'selected' : '' }}>{{ __('ui.female') }}</option>
                </select>
                @error('jenis_kelamin')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="border-t border-gray-100 pt-5 mb-5"
                 x-data="{
                   preview: '{{ $userPhoto ? Storage::url($userPhoto) : '' }}',
                   hasFile: false,
                   posX: '{{ trim(explode(' ', $user->foto_profil_position ?? '50% 50%')[0] ?? '50%') }}'.replace('%', ''),
                   posY: '{{ trim(explode(' ', $user->foto_profil_position ?? '50% 50%')[1] ?? '50%') }}'.replace('%', ''),
                   handleFile(e) {
                     const f = e.target.files[0];
                     if (!f) return;
                     this.hasFile = true;
                     const r = new FileReader();
                     r.onload = ev => this.preview = ev.target.result;
                     r.readAsDataURL(f);
                   }
                 }">
              <label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('ui.photo_profile') }}</label>
              <div class="rounded-3xl border border-gray-100 bg-[#F8FAFB] p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row items-center gap-5 sm:gap-6">
                  <div class="relative w-28 h-28 sm:w-32 sm:h-32 rounded-full overflow-hidden bg-[#D9D9D9] flex-shrink-0 ring-4 ring-white shadow-sm">
                    <div x-show="preview"
                         x-cloak
                         class="w-full h-full bg-no-repeat bg-cover"
                         :style="`background-image: url('${preview}'); background-position: ${posX}% ${posY}%;`">
                    </div>
                    <div x-show="!preview" class="w-full h-full flex items-center justify-center text-[#1F2937] text-3xl sm:text-4xl font-light tracking-[0.05em]">
                      {{ strtoupper(substr($userName, 0, 2)) }}
                    </div>
                  </div>

                  <div class="flex-1 w-full space-y-4">
                    <div class="space-y-1.5">
                      <div class="flex items-center justify-between gap-3 text-xs font-semibold text-gray-500">
                        <span>{{ __('ui.drag_horizontal') }}</span>
                        <span x-text="posX + '%'">50%</span>
                      </div>
                      <input type="range" min="0" max="100" x-model="posX" class="w-full accent-[#63A2BB] h-2 rounded-full cursor-pointer">
                    </div>

                    <div class="space-y-1.5">
                      <div class="flex items-center justify-between gap-3 text-xs font-semibold text-gray-500">
                        <span>{{ __('ui.drag_vertical') }}</span>
                        <span x-text="posY + '%'">50%</span>
                      </div>
                      <input type="range" min="0" max="100" x-model="posY" class="w-full accent-[#63A2BB] h-2 rounded-full cursor-pointer">
                    </div>

                    <label class="flex items-center gap-3 px-5 py-3 bg-white border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-[#63A2BB] hover:bg-[#63A2BB]/5 transition group">
                      <svg class="w-5 h-5 text-gray-400 group-hover:text-[#63A2BB] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                      </svg>
                      <div>
                        <p class="text-sm font-semibold text-gray-600 group-hover:text-[#63A2BB] transition">
                          <span x-text="hasFile ? 'Ganti foto' : 'Upload foto baru'"></span>
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">JPG, PNG, GIF · Maks 10MB</p>
                      </div>
                      <input type="file" name="foto_profil" accept="image/*" class="hidden" @change="handleFile($event)">
                    </label>
                  </div>
                </div>

                <input type="hidden" name="foto_profil_position" :value="`${posX}% ${posY}%`">
              </div>
            </div>

            <div class="flex justify-end">
              <button type="submit" id="btn-submit-profile"
                      class="px-8 py-3.5 bg-[#63A2BB] text-white rounded-2xl font-bold text-sm hover:-translate-y-0.5 hover:bg-[#4A8BA3] hover:shadow-lg hover:shadow-[#63A2BB]/30 transition-all duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('ui.save_changes') }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <div x-show="activeTab === 'pesanan'" x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 translate-y-2"
           x-transition:enter-end="opacity-100 translate-y-0"
           x-data="{
             activeStatus: '',
             selectedOrder: null,
             loadingDetail: false,

             async openDetail(kode) {
               this.loadingDetail = true;
               try {
                 const res = await fetch(
                   '/orders/' + kode + '/json',
                   { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
                 );
                 const data = await res.json();
                 this.selectedOrder = data;
               } catch (e) {
                 showToast('Gagal memuat detail', 'error');
               } finally {
                 this.loadingDetail = false;
               }
             },

             closeDetail() {
               this.selectedOrder = null;
             }
           }">

        <div class="flex items-center justify-between mb-4">
          <h2 class="font-bold text-gray-800 text-lg">
            My Orders
          </h2>
          <p class="text-xs text-gray-400">
            Klik Detail untuk melihat pesanan
          </p>
        </div>

        <div class="flex gap-2 overflow-x-auto pb-2 mb-5 scrollbar-hide -mx-1 px-1">
          @php
            $statusTabs = [
              '' => 'Semua',
              'menunggu_pembayaran' => 'Belum Bayar',
              'pembayaran_dikonfirmasi' => 'Dikonfirmasi',
              'dikirim' => 'Dikirim',
              'selesai' => 'Selesai',
              'dibatalkan' => 'Dibatalkan',
            ];
          @endphp
          @foreach($statusTabs as $val => $label)
            @php
              $count = isset($orderCounts[$val]) ? $orderCounts[$val] : 0;
            @endphp
            <button @click="activeStatus = '{{ $val }}'; selectedOrder = null"
                    :class="activeStatus === '{{ $val }}'
                      ? 'bg-[#63A2BB] text-white shadow-sm'
                      : 'bg-white text-gray-500 border border-gray-200 hover:border-[#63A2BB] hover:text-[#63A2BB]'"
                    class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold transition-all whitespace-nowrap">
              {{ $label }}
              @if($val === 'menunggu_pembayaran' && $count > 0)
                <span class="bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                  {{ $count }}
                </span>
              @endif
            </button>
          @endforeach
        </div>

        <div class="grid grid-cols-1 gap-4"
             :class="selectedOrder ? 'lg:grid-cols-2' : 'lg:grid-cols-1'">

          <div class="space-y-3">
            @forelse($semuaPesanan ?? [] as $t)
              @php
                $statusConfig = [
                  'menunggu_pembayaran' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'Belum Bayar'],
                  'pembayaran_dikonfirmasi' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'Dikonfirmasi'],
                  'diproses' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'label' => 'Diproses'],
                  'dikirim' => ['bg' => 'bg-[#63A2BB]/10', 'text' => 'text-[#63A2BB]', 'label' => 'Dikirim'],
                  'selesai' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'label' => 'Selesai'],
                  'dibatalkan' => ['bg' => 'bg-red-50', 'text' => 'text-red-500', 'label' => 'Dibatalkan'],
                ];
                $sc = $statusConfig[$t->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-500', 'label' => ucfirst($t->status)];
              @endphp
              <div x-show="activeStatus === '' || activeStatus === '{{ $t->status }}'"
                   class="bg-white rounded-2xl p-4 shadow-sm border-2 transition-all cursor-pointer hover:border-[#63A2BB]/30"
                   :class="selectedOrder?.kode_transaksi === '{{ $t->kode_transaksi }}'
                     ? 'border-[#63A2BB]'
                     : 'border-transparent'">

                <div class="flex items-center justify-between mb-3">
                  <div>
                    <p class="text-xs text-gray-400">
                      {{ is_string($t->tanggal) ? \Carbon\Carbon::parse($t->tanggal)->format('d M Y, H:i') : $t->tanggal->format('d M Y, H:i') }}
                    </p>
                    <p class="font-bold text-gray-700 text-sm mt-0.5">
                      {{ $t->kode_transaksi }}
                    </p>
                  </div>
                  <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
                    {{ $sc['label'] }}
                  </span>
                </div>

                @foreach($t->transaksiDetail->take(1) as $d)
                  <div class="flex items-center gap-3 mb-3">
                    <img src="{{ $d->detailProduk->produk->gambarUtama?->url_safe ?? asset('images/placeholder.png') }}"
                         class="w-12 h-12 rounded-xl object-cover flex-shrink-0 bg-gray-50"
                         alt="Produk">
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-semibold text-gray-700 line-clamp-1">
                        {{ $d->nama_produk_snap }}
                      </p>
                      <p class="text-xs text-gray-400 mt-0.5">
                        {{ $d->ukuran_snap }} · {{ $d->warna_snap }} · x{{ $d->quantity }}
                      </p>
                    </div>
                    <p class="text-sm font-bold text-gray-700 flex-shrink-0">
                      Rp {{ number_format($d->subtotal,0,',','.') }}
                    </p>
                  </div>
                @endforeach
                @if($t->transaksiDetail->count() > 1)
                  <p class="text-xs text-gray-400 mb-3">
                    +{{ $t->transaksiDetail->count()-1 }} produk lagi
                  </p>
                @endif

                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                  <div>
                    <p class="text-xs text-gray-400">Total</p>
                    <p class="font-black text-[#63A2BB] text-sm">
                      Rp {{ number_format($t->total_harga,0,',','.') }}
                    </p>
                  </div>
                  <div class="flex gap-2">
                    @if($t->status === 'menunggu_pembayaran')
                      <a href="{{ route('payment.show', $t->kode_transaksi) }}"
                         class="px-3 py-1.5 bg-[#63A2BB] text-white text-xs font-bold rounded-full hover:bg-[#4A8BA3] transition">
                        Bayar
                      </a>
                    @endif
                    <button @click="openDetail('{{ $t->kode_transaksi }}')"
                            class="px-4 py-1.5 border-2 border-gray-200 text-gray-500 text-xs font-semibold rounded-full hover:border-[#63A2BB] hover:text-[#63A2BB] transition">
                      Detail
                    </button>
                  </div>
                </div>
              </div>
            @empty
              <div class="bg-white rounded-2xl p-10 shadow-sm text-center">
                <div class="w-14 h-14 bg-[#63A2BB]/10 rounded-full flex items-center justify-center mx-auto mb-3">
                  <svg class="w-7 h-7 text-[#63A2BB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                  </svg>
                </div>
                <p class="text-gray-500 font-semibold text-sm">
                  Belum ada pesanan
                </p>
                <a href="/" class="mt-3 inline-flex text-xs text-[#63A2BB] font-semibold hover:underline">
                  Mulai Belanja ->
                </a>
              </div>
            @endforelse
          </div>

          <div x-show="selectedOrder !== null" x-cloak
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="opacity-0 translate-x-4"
               x-transition:enter-end="opacity-100 translate-x-0"
               class="lg:sticky lg:top-24 lg:self-start">

            <div x-show="loadingDetail"
                 class="bg-white rounded-2xl p-8 shadow-sm text-center">
              <svg class="animate-spin w-8 h-8 text-[#63A2BB] mx-auto"
                   fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              <p class="text-sm text-gray-400 mt-3">Memuat detail...</p>
            </div>

            <div x-show="selectedOrder && !loadingDetail" x-cloak
                 class="bg-white rounded-2xl shadow-sm overflow-hidden">

              <div class="bg-[#63A2BB] p-4 flex items-center justify-between">
                <div>
                  <p class="text-white/70 text-xs">Detail Pesanan</p>
                  <p class="text-white font-bold text-sm mt-0.5"
                     x-text="selectedOrder?.kode_transaksi">
                  </p>
                </div>
                <button @click="closeDetail()"
                        class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition">
                  <svg class="w-4 h-4 text-white" fill="none"
                       stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>

              <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="flex items-center gap-2">
                  <span class="text-xs font-bold px-3 py-1.5 rounded-full"
                        :class="{
                          'bg-amber-50 text-amber-600': selectedOrder?.status === 'menunggu_pembayaran',
                          'bg-blue-50 text-blue-600': selectedOrder?.status === 'pembayaran_dikonfirmasi',
                          'bg-[#63A2BB]/10 text-[#63A2BB]': selectedOrder?.status === 'dikirim',
                          'bg-green-50 text-green-600': selectedOrder?.status === 'selesai',
                          'bg-red-50 text-red-500': selectedOrder?.status === 'dibatalkan',
                          'bg-gray-50 text-gray-500': !['menunggu_pembayaran','pembayaran_dikonfirmasi','dikirim','selesai','dibatalkan'].includes(selectedOrder?.status),
                        }"
                        x-text="selectedOrder?.status_label">
                  </span>
                </div>

                <div>
                  <p class="text-xs font-bold text-gray-400 uppercase mb-2">
                    Produk
                  </p>
                  <template x-for="item in selectedOrder?.items ?? []" :key="item.id">
                    <div class="flex items-center gap-3 mb-2 last:mb-0">
                      <img :src="item.gambar" :alt="item.nama"
                           class="w-12 h-12 rounded-xl object-cover flex-shrink-0 bg-gray-50"
                           onerror="this.src='/images/placeholder.png'">
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-700 line-clamp-1"
                           x-text="item.nama">
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5"
                           x-text="item.ukuran + ' · ' + item.warna + ' · x' + item.qty">
                        </p>
                      </div>
                      <p class="text-sm font-bold text-gray-700 flex-shrink-0"
                         x-text="'Rp ' + item.subtotal.toLocaleString('id-ID')">
                      </p>
                    </div>
                  </template>
                </div>

                <div class="bg-gray-50 rounded-xl p-3">
                  <p class="text-xs font-bold text-gray-400 uppercase mb-1.5">
                    Alamat Kirim
                  </p>
                  <p class="font-semibold text-gray-700 text-sm"
                     x-text="selectedOrder?.penerima">
                  </p>
                  <p class="text-xs text-gray-500 mt-1 leading-relaxed"
                     x-text="selectedOrder?.alamat">
                  </p>
                  <p class="text-xs text-gray-400 mt-1"
                     x-text="selectedOrder?.telepon">
                  </p>
                </div>

                <div class="bg-gray-50 rounded-xl p-3">
                  <p class="text-xs font-bold text-gray-400 uppercase mb-1.5">
                    Pengiriman
                  </p>
                  <p class="text-sm font-semibold text-gray-700"
                     x-text="selectedOrder?.ekspedisi">
                  </p>
                  <p class="text-xs text-[#63A2BB] font-mono mt-1"
                     x-show="selectedOrder?.resi"
                     x-text="'Resi: ' + selectedOrder?.resi">
                  </p>
                  <p class="text-xs text-gray-400 mt-1"
                     x-show="selectedOrder?.estimasi"
                     x-text="'Estimasi: ' + selectedOrder?.estimasi">
                  </p>
                </div>

                <div>
                  <p class="text-xs font-bold text-gray-400 uppercase mb-2">
                    Rincian Pembayaran
                  </p>
                  <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-600">
                      <span>Subtotal Produk</span>
                      <span x-text="'Rp ' + (selectedOrder?.subtotal ?? 0).toLocaleString('id-ID')">
                      </span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                      <span>Ongkos Kirim</span>
                      <span x-text="'Rp ' + (selectedOrder?.ongkir ?? 0).toLocaleString('id-ID')">
                      </span>
                    </div>
                    <div x-show="selectedOrder?.diskon > 0"
                         class="flex justify-between text-green-600">
                      <span>Diskon Voucher</span>
                      <span x-text="'-Rp ' + (selectedOrder?.diskon ?? 0).toLocaleString('id-ID')">
                      </span>
                    </div>
                    <div class="border-t border-gray-200 pt-2 flex justify-between font-bold">
                      <span class="text-gray-800">Total</span>
                      <span class="text-[#63A2BB]"
                            x-text="'Rp ' + (selectedOrder?.total ?? 0).toLocaleString('id-ID')">
                      </span>
                    </div>
                  </div>
                </div>

                <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2.5">
                  <svg class="w-4 h-4 text-[#63A2BB]" fill="none"
                       stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                  </svg>
                  <span class="text-sm font-medium text-gray-600"
                        x-text="selectedOrder?.metode_bayar ?? '-'"></span>
                </div>

                <div class="space-y-2 pt-1">
                  <template x-if="selectedOrder?.status === 'menunggu_pembayaran'">
                    <a :href="'/pay/' + selectedOrder?.kode_transaksi"
                       class="w-full flex items-center justify-center gap-2 bg-[#63A2BB] text-white py-3 rounded-xl font-bold text-sm hover:bg-[#4A8BA3] transition">
                      Bayar Sekarang
                    </a>
                  </template>
                  <template x-if="selectedOrder?.status === 'dikirim'">
                    <a :href="'/tracking/' + selectedOrder?.kode_transaksi"
                       class="w-full flex items-center justify-center gap-2 border-2 border-[#63A2BB] text-[#63A2BB] py-3 rounded-xl font-bold text-sm hover:bg-[#63A2BB] hover:text-white transition">
                      Lacak Paket
                    </a>
                  </template>
                  <template x-if="selectedOrder?.status === 'selesai' && !selectedOrder?.sudah_rating">
                    <a :href="'/orders/' + selectedOrder?.kode_transaksi + '/rating'"
                       class="w-full flex items-center justify-center bg-amber-500 text-white py-3 rounded-xl font-bold text-sm hover:bg-amber-600 transition">
                      Beri Rating
                    </a>
                  </template>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div x-show="activeTab === 'alamat'"
           x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 translate-y-2"
           x-transition:enter-end="opacity-100 translate-y-0"
           class="space-y-4">
        <div class="flex items-center justify-between mb-2">
          <h2 class="font-bold text-gray-800 text-lg">{{ __('ui.alamat') }}</h2>
          <button type="button" @click="$dispatch('open-modal', 'add-address-modal')"
                  class="flex items-center gap-2 px-4 py-2.5 bg-[#63A2BB] text-white rounded-2xl text-sm font-bold hover:bg-[#4A8BA3] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Alamat
          </button>
        </div>

        @forelse($addresses ?? [] as $address)
          <div class="bg-white rounded-3xl p-5 shadow-sm border-2 transition-all {{ $address->is_utama ? 'border-[#63A2BB]/30' : 'border-transparent hover:border-gray-200' }}">
            <div class="flex items-start justify-between gap-4">
              <div class="flex items-start gap-4 flex-1">
                <div class="w-10 h-10 rounded-2xl flex-shrink-0 {{ $address->is_utama ? 'bg-[#63A2BB]' : 'bg-gray-100' }} flex items-center justify-center">
                  <svg class="w-5 h-5 {{ $address->is_utama ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="font-bold text-gray-800 text-sm">{{ $address->label }}</span>
                    @if($address->is_utama)
                      <span class="bg-[#63A2BB]/10 text-[#63A2BB] text-[10px] font-bold px-2 py-0.5 rounded-full">★ Utama</span>
                    @endif
                  </div>
                  <p class="font-semibold text-gray-700 text-sm">{{ $address->nama_penerima }}</p>
                  <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                    {{ $address->alamat_lengkap }},
                    Kel. {{ $address->kelurahan }},
                    Kec. {{ $address->kecamatan }},
                    {{ $address->kota }},
                    {{ $address->provinsi }}
                    {{ $address->kode_pos }}
                  </p>
                  <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ $address->no_telepon }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-1 flex-shrink-0">
                <a href="{{ route('profile.address.edit', $address->alamat_id) }}" class="w-8 h-8 rounded-xl flex items-center justify-center text-gray-400 hover:text-[#63A2BB] hover:bg-[#63A2BB]/10 transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </a>
                @if(!$address->is_utama)
                  <form action="{{ route('profile.address.delete', $address->alamat_id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus alamat ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-8 h-8 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </form>
                @endif
              </div>
            </div>
            @if(!$address->is_utama)
              <div class="mt-4 pt-4 border-t border-gray-100">
                <form action="{{ route('profile.address.set-primary', $address->alamat_id) }}" method="POST" class="inline">
                  @csrf
                  @method('PUT')
                  <button type="submit" class="text-xs text-[#63A2BB] font-semibold hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Jadikan Alamat Utama
                  </button>
                </form>
              </div>
            @endif
          </div>
        @empty
          <div class="bg-white rounded-3xl p-12 shadow-sm text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              </svg>
            </div>
            <p class="text-gray-500 font-semibold mb-4">Belum ada alamat tersimpan</p>
            <button type="button" @click="$dispatch('open-modal', 'add-address-modal')"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-[#63A2BB] text-white rounded-2xl text-sm font-bold hover:bg-[#4A8BA3] transition">
              + Tambah Alamat Pertama
            </button>
          </div>
        @endforelse
      </div>

      <div x-show="activeTab === 'pembayaran'"
           x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 translate-y-2"
           x-transition:enter-end="opacity-100 translate-y-0"
           class="space-y-4">
        <div class="flex items-center justify-between mb-2">
          <h2 class="font-bold text-gray-800 text-lg">{{ __('ui.payment_method') }}</h2>
          <button type="button" @click="$dispatch('open-modal', 'add-payment-modal')"
                  class="flex items-center gap-2 px-4 py-2.5 bg-[#63A2BB] text-white rounded-2xl text-sm font-bold hover:bg-[#4A8BA3] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Metode
          </button>
        </div>

        @forelse($paymentMethods ?? [] as $method)
          <div class="bg-white rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#63A2BB]/10 flex items-center justify-center flex-shrink-0">
              @if($method->metodePembayaran?->logo_url)
                <img src="{{ $method->metodePembayaran->logo_url }}" class="h-6 object-contain" alt="{{ $method->metodePembayaran->metode ?? 'Metode' }}">
              @else
                <svg class="w-6 h-6 text-[#63A2BB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
              @endif
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <p class="font-bold text-gray-800 text-sm">{{ $method->metodePembayaran->metode ?? '-' }}</p>
                @if($method->is_utama)
                  <span class="bg-[#63A2BB]/10 text-[#63A2BB] text-[10px] font-bold px-2 py-0.5 rounded-full">Utama</span>
                @endif
              </div>
              <p class="text-sm text-gray-500 mt-0.5">{{ substr($method->nomor_akun, 0, 4) }}****{{ substr($method->nomor_akun, -4) }}</p>
              @if($method->nama_akun)
                <p class="text-xs text-gray-400 mt-0.5">a.n. {{ $method->nama_akun }}</p>
              @endif
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full font-medium">{{ ucfirst($method->metodePembayaran->jenis ?? '-') }}</span>
              <form action="{{ route('profile.payment-methods.delete', $method->akun_pembayaran_id) }}" method="POST" onsubmit="return confirm('Hapus metode ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-8 h-8 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </form>
            </div>
          </div>
        @empty
          <div class="bg-white rounded-3xl p-12 shadow-sm text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
              </svg>
            </div>
            <p class="text-gray-500 font-semibold">{{ __('ui.no_payment_methods') }}</p>
          </div>
        @endforelse
      </div>

      <div x-show="activeTab === 'keamanan'"
           x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 translate-y-2"
           x-transition:enter-end="opacity-100 translate-y-0">
        <div class="bg-white rounded-3xl p-6 shadow-sm" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
          <h2 class="font-bold text-gray-800 text-lg mb-6">Ubah Password</h2>
          <form action="{{ route('profile.change-password') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4 max-w-md">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Saat Ini</label>
                <div class="relative">
                  <input :type="showCurrent ? 'text' : 'password'" name="current_password" placeholder="••••••••" class="w-full px-4 py-3 pr-12 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition">
                  <button type="button" @click="showCurrent = !showCurrent" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                </div>
                @error('current_password')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                <div class="relative">
                  <input :type="showNew ? 'text' : 'password'" name="password" placeholder="••••••••" class="w-full px-4 py-3 pr-12 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition">
                  <button type="button" @click="showNew = !showNew" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                </div>
                @error('password')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                <div class="relative">
                  <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" placeholder="••••••••" class="w-full px-4 py-3 pr-12 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition">
                  <button type="button" @click="showConfirm = !showConfirm" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                </div>
                @error('password_confirmation')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div class="bg-[#63A2BB]/5 rounded-2xl p-4 text-xs text-gray-500 space-y-1">
                <p class="font-semibold text-gray-600 mb-2">Syarat password:</p>
                <p>• Minimal 8 karakter</p>
                <p>• Kombinasi huruf dan angka</p>
                <p>• Tidak sama dengan password lama</p>
              </div>
            </div>

            <div class="flex justify-start mt-6">
              <button type="submit" class="px-8 py-3.5 bg-[#63A2BB] text-white rounded-2xl font-bold text-sm hover:-translate-y-0.5 hover:bg-[#4A8BA3] hover:shadow-lg transition-all duration-200">
                Update Password
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div x-data="{ show: false }"
     x-show="show"
     @open-modal.window="if ($event.detail === 'add-address-modal') show = true"
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false" aria-hidden="true"></div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div x-show="show" x-transition class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
      <form action="{{ route('profile.address.store') }}" method="POST">
        @csrf
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Tambah Alamat Baru</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700">Label (Rumah/Kantor)</label>
              <input type="text" name="label" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm" placeholder="Contoh: Rumah">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nama Penerima</label>
              <input type="text" name="nama_penerima" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
              <input type="text" name="no_telepon" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Provinsi</label>
              <input type="text" name="provinsi" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Kota/Kabupaten</label>
              <input type="text" name="kota" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
              <input type="text" name="kecamatan" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Kelurahan/Desa</label>
              <input type="text" name="kelurahan" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Kode Pos</label>
              <input type="text" name="kode_pos" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
              <textarea name="alamat_lengkap" rows="3" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm" placeholder="Nama jalan, gedung, no. rumah/unit"></textarea>
            </div>
            <div class="sm:col-span-2">
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input id="is_utama" name="is_utama" type="checkbox" value="1" class="focus:ring-[#63A2BB] h-4 w-4 text-[#63A2BB] border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                  <label for="is_utama" class="font-medium text-gray-700">Jadikan alamat utama</label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button type="submit" class="w-full inline-flex justify-center rounded-2xl border border-transparent shadow-sm px-4 py-2 bg-[#63A2BB] text-base font-medium text-white hover:bg-[#4A8BA3] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#63A2BB] sm:ml-3 sm:w-auto sm:text-sm">
            Simpan Alamat
          </button>
          <button type="button" @click="show = false" class="mt-3 w-full inline-flex justify-center rounded-2xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#63A2BB] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Batal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div x-data="{ show: false }"
     x-show="show"
     @open-modal.window="if ($event.detail === 'add-payment-modal') show = true"
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false" aria-hidden="true"></div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div x-show="show" x-transition class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <form action="{{ route('profile.payment-methods.store') }}" method="POST">
        @csrf
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Tambah Metode Pembayaran</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Pilih Bank / E-Wallet</label>
              <select name="metode_id" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm bg-white">
                <option value="">Pilih...</option>
                @foreach($availableMethods ?? [] as $method)
                  <option value="{{ $method->metode_id }}">{{ $method->metode }} ({{ ucfirst($method->jenis ?? '') }})</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nomor Rekening / No. Handphone</label>
              <input type="text" name="no_akun" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nama Pemilik Rekening</label>
              <input type="text" name="nama_akun" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button type="submit" class="w-full inline-flex justify-center rounded-2xl border border-transparent shadow-sm px-4 py-2 bg-[#63A2BB] text-base font-medium text-white hover:bg-[#4A8BA3] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#63A2BB] sm:ml-3 sm:w-auto sm:text-sm">
            Simpan Metode
          </button>
          <button type="button" @click="show = false" class="mt-3 w-full inline-flex justify-center rounded-2xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#63A2BB] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Batal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection