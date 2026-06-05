@extends('layouts.admin')

@section('title', 'Detail Review')

@section('content')
<div style="padding: 28px 28px 40px;">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 24px; max-width: 800px; margin-left: auto; margin-right: auto;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 4px;">Detail Moderasi</p>
            <h1 class="page-header-title" style="margin: 0;">Review Ulasan Produk</h1>
            <p class="page-header-sub" style="margin: 0; color: #94A3B8;">
                Dikirim pada: {{ $review->created_at ? $review->created_at->format('d F Y H:i') : '-' }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.review.index') }}" class="btn-secondary" style="height: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0 14px; font-size: 13px;">← Kembali</a>
        </div>
    </div>

    <div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">

        {{-- Review Content Panel --}}
        <div class="panel">
            <div class="panel-header" style="background: #F8FAFC;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 22px; font-weight: 900; color: #D97706; background: #FFFBEB; border: 1.5px solid #FCD34D; padding: 4px 14px; border-radius: 10px; display: inline-flex; align-items: center; gap: 4px;">
                        {{ $review->bintang }} <span style="font-size:16px;">★</span>
                    </span>
                    <div>
                        <h2 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0;">{{ $review->judul_ulasan ?: 'Ulasan Tanpa Judul' }}</h2>
                    </div>
                </div>
                
                @if($review->is_verified)
                    <span class="badge badge-success" style="font-size: 11px; padding: 4px 12px;">✓ Verified Purchase</span>
                @else
                    <span class="badge" style="background:#F1F5F9; color:#64748B; font-size: 11px; padding: 4px 12px;">Unverified Purchase</span>
                @endif
            </div>

            <div class="panel-body" style="display: flex; flex-direction: column; gap: 16px;">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; border-bottom: 1px solid #F1F5F9; padding-bottom: 16px;">
                    <div>
                        <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Produk yang Diulas</span>
                        <p style="margin: 3px 0 0; font-weight: 700; color: #0F172A; font-size: 13.5px;">{{ $review->produk?->nama_produk ?? '-' }}</p>
                    </div>
                    <div>
                        <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Pengirim / Customer</span>
                        <p style="margin: 3px 0 0; font-weight: 700; color: #1E293B; font-size: 13.5px;">{{ $review->buyer?->pengguna?->nama_pengguna ?? '-' }}</p>
                        <p style="margin: 1px 0 0; font-size: 11.5px; color: #64748B;">{{ $review->buyer?->pengguna?->email ?? '-' }}</p>
                    </div>
                </div>

                <div>
                    <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Isi Pesan Review</span>
                    <p style="margin: 6px 0 0; font-size: 14px; color: #334155; line-height: 1.6; font-weight: 500; background: #FAFCFE; border: 1.5px solid rgba(99,162,187,0.1); padding: 16px; border-radius: 12px;">
                        {!! nl2br(e($review->isi_ulasan)) !!}
                    </p>
                </div>

                {{-- Attached Review Photos --}}
                @if($review->foto_ulasan && is_array($review->foto_ulasan) && count($review->foto_ulasan) > 0)
                    <div style="border-top: 1px solid #F1F5F9; padding-top: 16px;">
                        <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Foto Terlampir</span>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 8px;">
                            @foreach($review->foto_ulasan as $foto)
                                <a href="{{ Storage::url($foto) }}" target="_blank" style="border: 2.5px solid #F1F5F9; border-radius: 10px; overflow: hidden; display: inline-block; transition: border-color 0.15s ease;" onmouseover="this.style.borderColor='#63A2BB'" onmouseout="this.style.borderColor='#F1F5F9'">
                                    <img src="{{ Storage::url($foto) }}" alt="Foto Review" style="height: 80px; width: 80px; object-fit: cover;">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- Reply / Response Panel --}}
        <div class="panel">
            <div class="panel-header">
                <p class="panel-title">Tanggapan & Balasan Admin</p>
            </div>
            <div class="panel-body">
                @if($review->balasan)
                    <div style="background: rgba(99,162,187,0.06); border: 1.5px solid rgba(99,162,187,0.15); border-radius: 12px; padding: 14px 18px; margin-bottom: 20px;">
                        <p style="margin: 0 0 10px; font-size: 13.5px; color: #0F172A; line-height: 1.5; font-weight: 500;">{{ $review->balasan }}</p>
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: #64748B; border-top: 1px solid rgba(99,162,187,0.12); padding-top: 8px;">
                            <span>Penjawab: <strong>{{ $review->penjawab?->pengguna?->nama_pengguna ?? 'Admin' }}</strong></span>
                            <span>Tanggal: <strong>{{ $review->balas_tanggal ? $review->balas_tanggal->format('d/m/Y H:i') : '-' }}</strong></span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.review.reply', $review->rating_id) }}" style="margin: 0;">
                        @csrf
                        <div style="margin-bottom: 12px;">
                            <label class="form-label">Ubah Tanggapan Balasan</label>
                            <textarea name="balasan" class="form-input" style="min-height: 100px; padding: 10px 12px; resize: vertical;" rows="3" required>{{ $review->balasan }}</textarea>
                        </div>
                        <button type="submit" class="btn-secondary" style="height: 38px; padding: 0 18px; font-weight: 700;">
                            Update Tanggapan
                        </button>
                    </form>
                @else
                    <div style="margin-bottom: 16px; padding: 12px 14px; background: #FFFBEB; border: 1px solid #FCD34D; border-radius: 10px; font-size: 12.5px; color: #D97706; font-weight: 500;">
                        ⚠️ Review ini belum memiliki balasan tanggapan resmi dari pihak admin toko.
                    </div>

                    <form method="POST" action="{{ route('admin.review.reply', $review->rating_id) }}" style="margin: 0;">
                        @csrf
                        <div style="margin-bottom: 12px;">
                            <label class="form-label">Tulis Tanggapan Balasan</label>
                            <textarea name="balasan" placeholder="Tuliskan respon resmi yang ramah dan membantu..." class="form-input" style="min-height: 100px; padding: 10px 12px; resize: vertical;" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn-primary" style="height: 38px; padding: 0 20px; background: #10B981; box-shadow: 0 2px 8px rgba(16,185,129,0.3);">
                            Kirim Tanggapan
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Danger Actions Panel --}}
        <div class="panel" style="border-color: #FCA5A5; background: #FFF5F5;">
            <div class="panel-body" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; padding: 16px 20px;">
                <div>
                    <h3 style="font-size: 13.5px; font-weight: 700; color: #C53030; margin: 0 0 2px;">Tindakan Penghapusan Permanen</h3>
                    <p style="font-size: 11.5px; color: #E53E3E; margin: 0;">Menghapus review ini akan menghilangkan ulasan dan rating produk tersebut secara permanen dari display website buyer.</p>
                </div>
                <form method="POST" action="{{ route('admin.review.destroy', $review->rating_id) }}" onsubmit="return confirm('Apakah Anda benar-benar yakin ingin menghapus review ini? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.')" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger" style="padding: 9px 18px; font-weight: 700; background: #EF4444; border-color: #EF4444; color: white; box-shadow: 0 2px 8px rgba(239,68,68,0.3);" onmouseover="this.style.background='#DC2626'" onmouseout="this.style.background='#EF4444'">
                        🗑️ Hapus Ulasan Customer
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
