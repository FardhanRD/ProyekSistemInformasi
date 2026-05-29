@extends('layouts.buyer')

@section('title', 'Notifikasi Saya — MOVR')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex items-center justify-between mb-5">
    <h1 class="text-xl font-black text-gray-800">Notifikasi Saya</h1>
    <a href="{{ route('profile.index', ['tab' => 'profil']) }}" class="text-sm font-semibold text-[#63A2BB] hover:underline">
      Kembali ke Profil
    </a>
  </div>

  <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-gray-100">
    @forelse($notifikasis as $n)
      <div class="px-5 py-4 border-b border-gray-50 last:border-b-0 flex gap-3">
        <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center mt-0.5 {{ $n->is_read ? 'bg-gray-100' : 'bg-[#63A2BB]/10' }}">
          <span>
            @switch($n->jenis)
              @case('transaksi') 🛍️ @break
              @case('pengiriman') 📦 @break
              @case('promo') 🎁 @break
              @case('sistem') ⚙️ @break
              @default 🔔
            @endswitch
          </span>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2">
            <p class="text-sm font-bold text-gray-800 line-clamp-1">{{ $n->judul }}</p>
            @if(! $n->is_read)
              <span class="w-2 h-2 bg-[#63A2BB] rounded-full"></span>
            @endif
          </div>
          <p class="text-sm text-gray-600 mt-0.5 line-clamp-2">{{ $n->pesan }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ optional($n->created_at)->diffForHumans() ?? '-' }}</p>
          @if($n->url_redirect)
            <a href="{{ $n->url_redirect }}" class="inline-flex mt-2 text-xs font-semibold text-[#63A2BB] hover:underline">
              Buka detail
            </a>
          @endif
        </div>
      </div>
    @empty
      <div class="px-6 py-12 text-center">
        <p class="text-sm text-gray-500">Belum ada notifikasi.</p>
      </div>
    @endforelse
  </div>

  @if(method_exists($notifikasis, 'links'))
    <div class="mt-5">
      {{ $notifikasis->links() }}
    </div>
  @endif
</div>
@endsection
