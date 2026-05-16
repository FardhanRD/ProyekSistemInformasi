@extends('layouts.admin')

@section('title', 'Review & Rating Moderation')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Review & Rating Moderation</h1>
                <p class="text-slate-600">Kelola review produk dari pelanggan, balas review, dan hapus review yang tidak layak.</p>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-slate-600 text-sm font-semibold mb-1">Rating Rata-Rata</p>
            <p class="text-3xl font-bold text-[#2B9BAF]">{{ $stats['avg_rating'] ?? 0 }}/5 ⭐</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-slate-600 text-sm font-semibold mb-1">Review Bulan Ini</p>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['this_month_review'] ?? 0 }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-slate-600 text-sm font-semibold mb-1">Rating Rendah (1-2 ⭐)</p>
            <p class="text-3xl font-bold text-red-600">{{ $stats['low_ratings'] ?? 0 }} ⚠️</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" class="space-y-3 sm:space-y-0 sm:flex gap-3 items-end flex-wrap">
            <input type="date" name="start_date" value="{{ $start_date ?? '' }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
            <input type="date" name="end_date" value="{{ $end_date ?? '' }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
            
            <select name="bintang" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Stars</option>
                <option value="1" {{ ($bintang_filter ?? '') === '1' ? 'selected' : '' }}>1 ⭐</option>
                <option value="2" {{ ($bintang_filter ?? '') === '2' ? 'selected' : '' }}>2 ⭐⭐</option>
                <option value="3" {{ ($bintang_filter ?? '') === '3' ? 'selected' : '' }}>3 ⭐⭐⭐</option>
                <option value="4" {{ ($bintang_filter ?? '') === '4' ? 'selected' : '' }}>4 ⭐⭐⭐⭐</option>
                <option value="5" {{ ($bintang_filter ?? '') === '5' ? 'selected' : '' }}>5 ⭐⭐⭐⭐⭐</option>
            </select>

            <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-5 py-2 text-sm font-semibold hover:bg-[#237f88]">Filter</button>
        </form>
    </div>

    {{-- Reviews Table --}}
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr class="text-left text-xs font-semibold text-slate-700 uppercase">
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Pemberi Rating</th>
                        <th class="px-4 py-3">Rating</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Status Balasan</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 text-xs">
                            <td class="px-4 py-3 font-medium">{{ $review->produk?->nama_produk ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $review->buyer?->pengguna?->nama_pengguna ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="font-semibold">{{ $review->bintang }} ⭐</span>
                            </td>
                            <td class="px-4 py-3">{{ Str::limit($review->judul_ulasan, 30) }}</td>
                            <td class="px-4 py-3">{{ $review->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($review->balasan)
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">✓ Sudah Dijawab</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">⏳ Belum Dijawab</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 space-x-2">
                                <button onclick="showReviewDetail({{ $review->rating_id }})" class="text-blue-600 hover:underline">👁️</button>
                                
                                @if(!$review->balasan)
                                    <button onclick="showReplyForm({{ $review->rating_id }})" class="text-green-600 hover:underline">💬</button>
                                @endif
                                
                                <form method="POST" action="{{ route('admin.review.destroy', $review->rating_id) }}" style="display:inline;" onsubmit="return confirm('Yakin hapus review ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 px-4 text-center text-slate-600">
                                Tidak ada review.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($reviews ?? null, 'links'))
            <div class="border-t border-slate-100 p-4">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Reply Modal --}}
<div id="replyModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-6">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Balas Review</h2>
        
        <form id="replyForm" method="POST">
            @csrf
            <textarea name="balasan" placeholder="Tuliskan balasan Anda..." class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:border-[#2B9BAF]" rows="4" required></textarea>
            
            <div class="flex gap-3 mt-4">
                <button type="button" onclick="closeReplyModal()" class="flex-1 px-4 py-2 border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-[#2B9BAF] text-white rounded-xl hover:bg-[#237f88]">Kirim</button>
            </div>
        </form>
    </div>
</div>

<script>
function showReplyForm(reviewId) {
    document.getElementById('replyForm').action = `{{ route('admin.review.reply', ':id') }}`.replace(':id', reviewId);
    document.getElementById('replyModal').classList.remove('hidden');
}

function closeReplyModal() {
    document.getElementById('replyModal').classList.add('hidden');
}

function showReviewDetail(reviewId) {
    window.location.href = `{{ route('admin.review.show', ':id') }}`.replace(':id', reviewId);
}
</script>
@endsection
