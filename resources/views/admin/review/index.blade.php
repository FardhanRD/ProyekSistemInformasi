@extends('layouts.admin')

@section('title', 'Review & Rating Moderation')

@section('content')
<div style="padding: 28px 28px 40px;">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 24px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 4px;">Ulasan & Masukan</p>
            <h1 class="page-header-title" style="margin: 0;">Review & Rating Moderation</h1>
            <p class="page-header-sub" style="margin: 0; color: #94A3B8;">Kelola review produk dari pelanggan, berikan tanggapan balasan, atau hapus review tidak layak.</p>
        </div>
    </div>

    {{-- Statistics Cards Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px;">
        
        <div class="panel" style="padding: 18px 20px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; margin: 0 0 6px;">Rating Rata-Rata Toko</p>
                <p style="font-size: 26px; font-weight: 900; color: #63A2BB; margin: 0; display: flex; align-items: center; gap: 6px;">
                    {{ number_format($stats['avg_rating'] ?? 0, 1) }}
                    <span style="color:#F59E0B; font-size:18px;">★</span>
                </p>
            </div>
            <div style="width: 42px; height: 42px; border-radius: 12px; background: #FFFDF5; display: flex; align-items: center; justify-content: center; border: 1px solid #FEF3C7;">
                <svg width="20" height="20" fill="none" stroke="#D97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
        </div>

        <div class="panel" style="padding: 18px 20px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; margin: 0 0 6px;">Ulasan Bulan Ini</p>
                <p style="font-size: 26px; font-weight: 900; color: #1E293B; margin: 0;">{{ $stats['this_month_review'] ?? 0 }}</p>
            </div>
            <div style="width: 42px; height: 42px; border-radius: 12px; background: #EFF8FB; display: flex; align-items: center; justify-content: center; border: 1px solid #DBEAFE;">
                <svg width="20" height="20" fill="none" stroke="#63A2BB" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>

        <div class="panel" style="padding: 18px 20px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; margin: 0 0 6px;">Rating Rendah (1-2 ★)</p>
                <p style="font-size: 26px; font-weight: 900; color: #EF4444; margin: 0;">{{ $stats['low_ratings'] ?? 0 }}</p>
            </div>
            <div style="width: 42px; height: 42px; border-radius: 12px; background: #FFF5F5; display: flex; align-items: center; justify-content: center; border: 1px solid #FED7D7;">
                <svg width="20" height="20" fill="none" stroke="#EF4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>

    </div>

    {{-- Filter Panel --}}
    <div class="panel" style="margin-bottom: 24px; padding: 18px 20px;">
        <form method="GET" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
            <div style="width: 150px;">
                <label class="form-label" style="margin-bottom: 6px;">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $start_date ?? '' }}" class="form-input" style="height: 38px;">
            </div>

            <div style="width: 150px;">
                <label class="form-label" style="margin-bottom: 6px;">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $end_date ?? '' }}" class="form-input" style="height: 38px;">
            </div>

            <div style="width: 150px;">
                <label class="form-label" style="margin-bottom: 6px;">Filter Bintang</label>
                <select name="bintang" class="form-input" style="height: 38px; cursor: pointer;">
                    <option value="">All Stars</option>
                    <option value="1" {{ ($bintang_filter ?? '') === '1' ? 'selected' : '' }}>1 ★</option>
                    <option value="2" {{ ($bintang_filter ?? '') === '2' ? 'selected' : '' }}>2 ★★</option>
                    <option value="3" {{ ($bintang_filter ?? '') === '3' ? 'selected' : '' }}>3 ★★★</option>
                    <option value="4" {{ ($bintang_filter ?? '') === '4' ? 'selected' : '' }}>4 ★★★★</option>
                    <option value="5" {{ ($bintang_filter ?? '') === '5' ? 'selected' : '' }}>5 ★★★★★</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-primary" style="height: 38px; padding: 0 16px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('start_date') || request('end_date') || request('bintang'))
                    <a href="{{ route('admin.review.index') }}" class="btn-secondary" style="height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 14px;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Reviews Table Panel --}}
    <div class="panel">
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produk Terkait</th>
                        <th style="width: 140px;">Customer</th>
                        <th style="width: 110px; text-align: center;">Rating</th>
                        <th>Judul Ulasan</th>
                        <th style="width: 110px;">Tanggal</th>
                        <th style="width: 140px; text-align: center;">Status Balasan</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0F172A;">{{ $review->produk?->nama_produk ?? '-' }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #334155;">{{ $review->buyer?->pengguna?->nama_pengguna ?? '-' }}</div>
                            </td>
                            <td style="text-align: center;">
                                <span style="font-weight: 800; color: #D97706; background: #FFFBEB; border: 1px solid #FCD34D; padding: 3px 8px; border-radius: 6px; font-size: 12px; display: inline-flex; align-items: center; gap: 3px;">
                                    {{ $review->bintang }} <span style="font-size:10px;">★</span>
                                </span>
                            </td>
                            <td style="color: #475569; font-weight: 500;">{{ Str::limit($review->judul_ulasan, 40) }}</td>
                            <td style="color: #64748B; font-size: 12.5px;">{{ $review->created_at ? $review->created_at->format('d/m/Y') : '-' }}</td>
                            <td style="text-align: center;">
                                @if($review->balasan)
                                    <span class="badge badge-success">✓ Sudah Dibalas</span>
                                @else
                                    <span class="badge badge-warning">⏳ Menunggu</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; align-items: center; justify-content: center;">
                                    <button onclick="showReviewDetail({{ $review->rating_id }})" class="btn-secondary" style="padding: 6px; border-radius: 8px;" title="Lihat Detail">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    
                                    @if(!$review->balasan)
                                        <button onclick="showReplyForm({{ $review->rating_id }})" class="btn-primary" style="padding: 6px; border-radius: 8px;" title="Balas Ulasan">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        </button>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('admin.review.destroy', $review->rating_id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan dari buyer ini secara permanen?')" style="display:inline; margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger" style="padding: 6px; border-radius: 8px;" title="Hapus Ulasan">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94A3B8; padding: 36px;">
                                Tidak ada ulasan customer yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($reviews ?? null, 'links') && $reviews->hasPages())
            <div style="border-top: 1px solid #F1F5F9; padding: 16px 20px; display: flex; justify-content: center;">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Reply Modal --}}
<div id="replyModal" style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); width: 100%; max-width: 420px; overflow: hidden; border: 1px solid #E2E8F0;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <div style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;">Balas Ulasan Pelanggan</h3>
            <button type="button" style="background: none; border: none; cursor: pointer; color: #94A3B8; font-size: 16px;" onclick="closeReplyModal()">✕</button>
        </div>

        <form id="replyForm" method="POST" style="padding: 20px 24px; margin: 0; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            <div>
                <label class="form-label" style="margin-bottom: 6px;">Tulis Jawaban Tanggapan <span style="color: #EF4444;">*</span></label>
                <textarea name="balasan" placeholder="Tuliskan respon resmi dari toko di sini..." class="form-input" style="height: auto; min-height: 110px; padding: 10px 12px; resize: vertical;" rows="4" required></textarea>
            </div>

            <div style="margin-top: 8px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn-secondary" style="height: 38px; padding: 0 16px;" onclick="closeReplyModal()">Batal</button>
                <button type="submit" class="btn-primary" style="height: 38px; padding: 0 20px; background: #10B981; box-shadow: 0 2px 8px rgba(16,185,129,0.3);">Kirim Tanggapan</button>
            </div>
        </form>
    </div>
</div>

<script>
function showReplyForm(reviewId) {
    document.getElementById('replyForm').action = `{{ route('admin.review.reply', ':id') }}`.replace(':id', reviewId);
    document.getElementById('replyModal').style.display = 'flex';
}

function closeReplyModal() {
    document.getElementById('replyModal').style.display = 'none';
}

function showReviewDetail(reviewId) {
    window.location.href = `{{ route('admin.review.show', ':id') }}`.replace(':id', reviewId);
}
</script>
@endsection
