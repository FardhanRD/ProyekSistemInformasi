@extends('layouts.admin')

@section('title', 'Detail Review')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Review Produk</h1>
                <p class="text-slate-600 text-sm">{{ $review->created_at?->format('Y-m-d H:i') }}</p>
            </div>
            <div class="flex gap-2">
                <span class="text-3xl">{{ $review->bintang }} ⭐</span>
            </div>
        </div>
    </div>

    {{-- Review Info --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-4">{{ $review->judul_ulasan }}</h2>
        
        <div class="space-y-4">
            <div>
                <p class="text-slate-600 text-xs font-semibold mb-2">Produk</p>
                <p class="text-slate-900 font-medium">{{ $review->produk?->nama_produk ?? '-' }}</p>
            </div>

            <div>
                <p class="text-slate-600 text-xs font-semibold mb-2">Pemberi Rating</p>
                <p class="text-slate-900 font-medium">{{ $review->buyer?->pengguna?->nama_pengguna ?? '-' }}</p>
                <p class="text-xs text-slate-600">{{ $review->buyer?->pengguna?->email ?? '-' }}</p>
            </div>

            <div>
                <p class="text-slate-600 text-xs font-semibold mb-2">Isi Review</p>
                <p class="text-slate-900 leading-relaxed">{{ $review->isi_ulasan }}</p>
            </div>

            @if($review->foto_ulasan && is_array($review->foto_ulasan) && count($review->foto_ulasan) > 0)
                <div>
                    <p class="text-slate-600 text-xs font-semibold mb-2">Foto</p>
                    <div class="grid sm:grid-cols-4 gap-3">
                        @foreach($review->foto_ulasan as $foto)
                            <img src="{{ Storage::url($foto) }}" alt="Review" class="rounded-lg h-24 object-cover">
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="pt-4 border-t border-slate-200">
                @if($review->is_verified)
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">✓ Terverifikasi</span>
                @else
                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">⏳ Belum Terverifikasi</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Reply Section --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Balasan Admin</h2>
        
        @if($review->balasan)
            <div class="bg-blue-50 rounded-xl p-4 mb-4">
                <p class="text-slate-900 mb-2">{{ $review->balasan }}</p>
                <p class="text-xs text-slate-600">
                    Dijawab oleh: <strong>{{ $review->penjawab?->pengguna?->nama_pengguna ?? '-' }}</strong>
                    pada {{ $review->balas_tanggal?->format('Y-m-d H:i') ?? '-' }}
                </p>
            </div>

            <form method="POST" action="{{ route('admin.review.reply', $review->rating_id) }}" onsubmit="return confirm('Edit balasan ini?')">
                @csrf
                <textarea name="balasan" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:border-[#2B9BAF]" rows="4" required>{{ $review->balasan }}</textarea>
                <button type="submit" class="mt-3 rounded-xl bg-blue-500 text-white px-4 py-2 font-semibold hover:bg-blue-600">Edit Balasan</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.review.reply', $review->rating_id) }}">
                @csrf
                <textarea name="balasan" placeholder="Tuliskan balasan Anda..." class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:border-[#2B9BAF]" rows="4" required></textarea>
                <button type="submit" class="mt-3 rounded-xl bg-green-500 text-white px-4 py-2 font-semibold hover:bg-green-600">Kirim Balasan</button>
            </form>
        @endif
    </div>

    {{-- Delete Section --}}
    <div class="rounded-3xl border border-slate-200 bg-red-50 p-5">
        <p class="text-sm text-slate-700 mb-3">Jika review ini tidak layak atau melanggar kebijakan, Anda dapat menghapusnya.</p>
        <form method="POST" action="{{ route('admin.review.destroy', $review->rating_id) }}" onsubmit="return confirm('Yakin hapus review ini? Tindakan ini tidak bisa dibatalkan.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-xl bg-red-500 text-white px-4 py-2 font-semibold hover:bg-red-600">🗑️ Hapus Review</button>
        </form>
    </div>

    {{-- Back Button --}}
    <div>
        <a href="{{ route('admin.review.index') }}" class="rounded-xl border border-slate-200 text-slate-900 px-6 py-3 font-semibold hover:bg-slate-50">← Kembali</a>
    </div>
</div>
@endsection
