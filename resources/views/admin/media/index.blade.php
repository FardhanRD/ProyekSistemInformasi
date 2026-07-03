@extends('layouts.admin')

@section('title', 'Media Management')

@section('content')
<div style="padding: 28px 28px 40px;">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 24px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 4px;">Inventori</p>
            <h1 class="page-header-title" style="margin: 0;">Media Management</h1>
            <p class="page-header-sub" style="margin: 0; color: #94A3B8;">Kelola gambar produk, set thumbnail utama, dan kelola galeri media.</p>
        </div>
        <div>
            <button type="button" onclick="document.getElementById('uploadModal').style.display = 'flex'" class="btn-primary" style="display: inline-flex; align-items: center; gap: 6px; height: 38px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Upload Media
            </button>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="panel" style="margin-bottom: 24px; padding: 18px 20px;">
        <form method="GET" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <label class="form-label" style="margin-bottom: 6px;">Filter per Produk</label>
                <select name="produk_id" class="form-input" style="height: 38px; cursor: pointer;">
                    <option value="">All Products (Semua Produk)</option>
                    @foreach($produk_list as $p)
                        <option value="{{ $p->produk_id }}" {{ ($produk_filter ?? '') == $p->produk_id ? 'selected' : '' }}>
                            {{ $p->nama_produk }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-primary" style="height: 38px; padding: 0 18px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('produk_id'))
                    <a href="{{ route('admin.media.index') }}" class="btn-secondary" style="height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 14px;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Grid Media --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px;">
        @forelse($media as $item)
            <div class="panel" style="display: flex; flex-direction: column; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease; border: 1px solid #E2E8F0;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.06)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.04)';">
                
                {{-- Image Area --}}
                <div style="position: relative; width: 100%; aspect-ratio: 1/1; background: #F8FAFC; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    @if($item->url_lengkap)
                        <img src="{{ $item->url_lengkap }}" alt="{{ $item->alt_text }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="text-align: center; color: #CBD5E1;">
                            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="margin: 0 auto 8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span style="font-size: 11px; font-weight: 500;">No Image</span>
                        </div>
                    @endif

                    @if($item->urutan == 0)
                        <div style="position: absolute; top: 10px; right: 10px; background: #10B981; color: white; font-size: 9px; font-weight: 800; padding: 4px 8px; border-radius: 6px; letter-spacing: 0.05em; box-shadow: 0 2px 8px rgba(16,185,129,0.4);">
                            THUMBNAIL UTAMA
                        </div>
                    @endif
                </div>

                {{-- Description & Action Area --}}
                <div style="padding: 14px; display: flex; flex-direction: column; flex: 1; justify-content: space-between;">
                    <div style="margin-bottom: 12px;">
                        <span style="font-size: 10px; font-weight: 700; color: #63A2BB; text-transform: uppercase;">Produk</span>
                        <h3 style="font-size: 13px; font-weight: 700; color: #1E293B; margin: 2px 0 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item->produk->nama_produk ?? '-' }}">
                            {{ $item->produk->nama_produk ?? '-' }}
                        </h3>
                        @if($item->alt_text)
                            <p style="font-size: 11px; color: #94A3B8; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item->alt_text }}">
                                Alt: {{ $item->alt_text }}
                            </p>
                        @endif
                    </div>

                    <div style="display: flex; gap: 6px;">
                        @if($item->urutan != 0)
                            <form method="POST" action="{{ route('admin.media.set-thumbnail', $item->gambar_id) }}" style="flex: 1; margin: 0;">
                                @csrf 
                                @method('PUT')
                                <button type="submit" class="btn-secondary" style="width: 100%; justify-content: center; padding: 6px 0; font-size: 11.5px; border-radius: 8px;">
                                    Set Thumbnail
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.media.destroy', $item->gambar_id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus media ini dari produk?')" style="{{ $item->urutan == 0 ? 'width: 100%' : 'flex: 1' }}; margin: 0;">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="btn-danger" style="width: 100%; justify-content: center; padding: 6px 0; font-size: 11.5px; border-radius: 8px;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        @empty
            <div class="panel" style="grid-column: 1 / -1; padding: 48px; text-align: center;">
                <svg width="48" height="48" fill="none" stroke="#CBD5E1" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p style="color: #64748B; font-size: 14px; font-weight: 500; margin: 0 0 16px;">Belum ada data media / foto produk.</p>
                <button type="button" onclick="document.getElementById('uploadModal').style.display = 'flex'" class="btn-primary" style="margin: 0 auto;">Upload Gambar Pertama</button>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if(method_exists($media ?? null, 'links') && $media->hasPages())
        <div style="display: flex; justify-content: center; margin-top: 32px;">
            {{ $media->links() }}
        </div>
    @endif

</div>

{{-- Upload Modal --}}
<div id="uploadModal" x-data="{}" style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); width: 100%; max-width: 440px; overflow: hidden; border: 1px solid #E2E8F0;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <div style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;">Upload Media Baru</h3>
            <button type="button" style="background: none; border: none; cursor: pointer; color: #94A3B8; font-size: 16px;" onclick="document.getElementById('uploadModal').style.display = 'none'">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.media.upload') }}" enctype="multipart/form-data" style="padding: 20px 24px; margin: 0; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div>
                <label class="form-label" style="margin-bottom: 6px;">Pilih Produk <span style="color: #EF4444;">*</span></label>
                <select name="produk_id" required class="form-input" style="height: 38px; cursor: pointer;">
                    <option value="">Pilih produk...</option>
                    @foreach($produk_list as $p)
                        <option value="{{ $p->produk_id }}">{{ $p->nama_produk }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="margin-bottom: 6px;">Pilih File Gambar (Max 10MB) <span style="color: #EF4444;">*</span></label>
                <input type="file" name="gambar" accept="image/*" required class="form-input" style="padding: 6px 12px; height: 38px;">
                <p style="font-size: 11px; color: #94A3B8; margin: 4px 0 0;">Format support: JPG, JPEG, PNG, WEBP.</p>
            </div>

            <div>
                <label class="form-label" style="margin-bottom: 6px;">Deskripsi Alt (Optional)</label>
                <input type="text" name="alt_text" placeholder="Contoh: Tampak depan sepatu" class="form-input" style="height: 38px;">
            </div>

            <div style="margin-top: 8px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn-secondary" style="height: 38px; padding: 0 16px;" onclick="document.getElementById('uploadModal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn-primary" style="height: 38px; padding: 0 20px;">Upload Gambar</button>
            </div>
        </form>
    </div>
</div>

@endsection
