@extends('layouts.buyer')

@section('title', 'Pembayaran — MOVR')

@section('content')
<div class="section-shell py-8 sm:py-10" x-data="paymentCountdown('{{ $pembayaran->expired_at }}')">
    <div class="mb-6 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-[#63A2BB]/10 text-[#63A2BB]">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#63A2BB]">Pembayaran</p>
                    <h1 class="mt-2 text-3xl font-black text-slate-900">Selesaikan Pembayaran</h1>
                    <p class="mt-1 text-sm text-slate-500">Kode transaksi: {{ $transaksi->kode_transaksi }}</p>
                </div>
            </div>
            <a href="{{ route('orders.index') }}" class="btn-outline inline-flex items-center justify-center px-5 py-3 text-sm">Lihat Pesanan</a>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="mb-6 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700">{{ session('info') }}</div>
    @endif

    @if($isExpired)
        <div class="card-surface p-10 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-rose-50 text-rose-500">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M3.172 6.172A4 4 0 015 5h14a4 4 0 014 4v6a4 4 0 01-4 4H5a4 4 0 01-4-4V9a4 4 0 011.172-2.828z"/>
                </svg>
            </div>
            <h2 class="mt-5 text-2xl font-black text-slate-900">Pembayaran Kadaluarsa</h2>
            <p class="mt-2 text-sm text-slate-500">Waktu pembayaran untuk transaksi ini telah habis.</p>
            <a href="{{ route('home') }}" class="btn-primary mt-6 inline-flex px-6 py-3">Belanja Kembali</a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-8 space-y-6">
                <div class="card-surface overflow-hidden">
                    <div class="bg-[#63A2BB] px-6 py-7 text-white">
                        <p class="text-sm/relaxed text-white/75">Total yang harus dibayar</p>
                        <p class="mt-2 text-4xl font-black">Rp {{ number_format($pembayaran->jumlah_pembayaran, 0, ',', '.') }}</p>
                        @if($pembayaran->kode_unik)
                            <p class="mt-2 text-sm text-white/80">Sudah termasuk kode unik: <span class="font-bold text-white">{{ $pembayaran->kode_unik }}</span></p>
                        @endif
                    </div>

                    <div class="p-6">
                        <div class="mb-6 grid gap-4 rounded-3xl bg-[#F8FAFB] p-4 ring-1 ring-slate-200/70 sm:grid-cols-2">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kode Transaksi</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <p class="font-black text-slate-900" id="kode-transaksi">{{ $transaksi->kode_transaksi }}</p>
                                    <button type="button" onclick="copyToClipboard('{{ $transaksi->kode_transaksi }}')" class="text-sm font-semibold text-[#63A2BB] hover:underline">Salin</button>
                                </div>
                            </div>
                            <div class="sm:text-right">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Batas Waktu Pembayaran</p>
                                <p class="mt-2 text-xl font-black text-[#63A2BB]" x-text="timeLeft">00:00:00</p>
                                <p class="mt-1 text-xs text-slate-400">{{ \Carbon\Carbon::parse($pembayaran->expired_at)->isoFormat('D MMM YYYY, HH:mm') }} WIB</p>
                            </div>
                        </div>

                        @php $metode = $pembayaran->metode ?? $pembayaran->metodePembayaran ?? null; @endphp
                        <div class="mb-6">
                            <h3 class="mb-4 flex items-center gap-2 text-lg font-black text-slate-900">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#63A2BB]/10 text-xs font-black text-[#63A2BB]">i</span>
                                Instruksi Pembayaran — {{ $metode?->metode ?? 'Metode Pembayaran' }}
                            </h3>

                            <div class="space-y-4">
                                                                @if(str_contains(optional($metode)->jenis ?? '', 'transfer'))

                                                                        {{-- Header VA --}}
                                                                        <div class="bg-gradient-to-r from-[#63A2BB] to-[#4A8BA3] rounded-2xl p-5 mb-4">
                                                                            <div class="flex items-center justify-between mb-3">
                                                                                <div class="flex items-center gap-2">
                                                                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <span class="text-white font-bold text-sm">{{ $metode->metode ?? ($pembayaran->metodePembayaran->metode ?? 'Virtual Account') }}</span>
                                                                                </div>
                                                                                <span class="bg-white/20 text-white text-xs font-bold px-2.5 py-1 rounded-full">Virtual Account</span>
                                                                            </div>
                                      
                                                                            {{-- Nomor VA --}}
                                                                            <p class="text-white/70 text-xs mb-1">Nomor Virtual Account</p>
                                                                            <div class="flex items-center justify-between bg-white/10 rounded-xl px-4 py-3">
                                                                                <span class="text-white font-black text-xl font-mono tracking-widest" id="nomor-va">{{ $nomorVAFormatted ?? '----' }}</span>
                                                                                <button onclick="navigator.clipboard.writeText('{{ str_replace('-', '', $nomorVAFormatted ?? '') }}').then(() => showToast('✅ Nomor VA disalin!'))" class="bg-white text-[#63A2BB] text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-gray-100 transition flex items-center gap-1.5">
                                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                                                    </svg>
                                                                                    Salin
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                        {{-- Total Bayar --}}
                                                                        <div class="bg-[#63A2BB]/5 border border-[#63A2BB]/20 rounded-2xl p-4 mb-4">
                                                                            <p class="text-xs text-gray-400 mb-1">Total Transfer</p>
                                                                            <div class="flex items-center justify-between">
                                                                                <span class="font-black text-[#63A2BB] text-2xl">Rp {{ number_format($pembayaran->jumlah_pembayaran,0,',','.') }}</span>
                                                                                <button onclick="navigator.clipboard.writeText('{{ $pembayaran->jumlah_pembayaran }}').then(() => showToast('✅ Nominal disalin!'))" class="text-xs text-[#63A2BB] font-semibold flex items-center gap-1 hover:underline">
                                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                                                    </svg>
                                                                                    Salin
                                                                                </button>
                                                                            </div>
                                                                            <p class="text-xs text-gray-400 mt-1">Transfer tepat sesuai nominal agar terkonfirmasi otomatis</p>
                                                                        </div>

                                                                        {{-- Langkah Pembayaran --}}
                                                                        <div class="space-y-2">
                                                                            <p class="text-sm font-bold text-gray-700 mb-3">Cara Bayar:</p>
                                                                            @foreach([
                                                                                'Buka aplikasi/ATM '.($metode->metode ?? ($pembayaran->metodePembayaran->metode ?? 'bank')),
                                                                                'Pilih menu Transfer / Virtual Account',
                                                                                'Masukkan nomor VA: '.($nomorVAFormatted ?? '----'),
                                                                                'Masukkan nominal: Rp '.number_format($pembayaran->jumlah_pembayaran,0,',','.'),
                                                                                'Konfirmasi dan selesaikan pembayaran',
                                                                                'Pembayaran otomatis terkonfirmasi dalam 1-5 menit',
                                                                            ] as $i => $step)
                                                                            <div class="flex items-start gap-3">
                                                                                <div class="w-6 h-6 rounded-full bg-[#63A2BB]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                                                    <span class="text-[#63A2BB] font-bold text-xs">{{ $i + 1 }}</span>
                                                                                </div>
                                                                                <p class="text-sm text-gray-600 flex-1">{{ $step }}</p>
                                                                            </div>
                                                                            @endforeach
                                                                        </div>

                                                                @elseif(optional($metode)->jenis === 'ewallet')
                                @elseif(optional($metode)->jenis === 'ewallet')
                                    <div class="rounded-3xl border border-slate-200 bg-[#F8FAFB] p-5">
                                        <p class="text-sm text-slate-500">Silakan transfer menggunakan {{ optional($metode)->metode ?? 'metode pembayaran' }} ke nomor berikut:</p>
                                        <div class="mt-4 rounded-2xl bg-white p-4 ring-1 ring-slate-200/70">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Nomor Tujuan</p>
                                            <div class="mt-2 flex items-center justify-between gap-3">
                                                <p class="font-mono text-xl font-black text-slate-900">081234567890</p>
                                                <button type="button" onclick="navigator.clipboard.writeText('081234567890').then(() => showToast('Nomor disalin!'))" class="text-sm font-semibold text-[#63A2BB] hover:underline">Salin</button>
                                            </div>
                                            <p class="mt-3 text-xs text-slate-400">a.n. MOVR Indonesia</p>
                                        </div>
                                    </div>
                                @elseif(optional($metode)->jenis === 'qris')
                                    <div class="rounded-3xl border border-slate-200 bg-[#F8FAFB] p-5 text-center">
                                        <p class="text-sm text-slate-500">Scan QRIS menggunakan aplikasi pembayaran apapun yang mendukung QRIS.</p>
                                        @php
                                            $qrPayload = $akunPembayaran?->nomor_akun ?? $akunPembayaran?->nama_akun ?? ($pembayaran->kode_transaksi ?? optional($metode)->metode ?? 'MOVR QRIS');
                                            $qrSrc = $pembayaran->qr_image
                                                ?? ($akunPembayaran->qr_url ?? null)
                                                ?? optional($metode)->logo_url
                                                ?? ('https://api.qrserver.com/v1/create-qr-code/?size=320x320&format=png&margin=10&data=' . urlencode($qrPayload));
                                        @endphp
                                        @if($qrSrc)
                                            <div class="mx-auto mt-5">
                                                <img src="{{ $qrSrc }}" alt="QR Code" class="mx-auto h-56 w-56 object-contain rounded-2xl border" />
                                            </div>
                                            @if($akunPembayaran?->nomor_akun || $akunPembayaran?->nama_akun)
                                                <p class="mt-3 text-xs text-slate-500">{{ $akunPembayaran->nama_akun }} · {{ $akunPembayaran->nomor_akun }}</p>
                                            @endif
                                        @else
                                            <div class="mx-auto mt-5 flex h-56 w-56 items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-white text-sm text-slate-400">QR Code</div>
                                        @endif
                                    </div>
                                @elseif(optional($metode)->jenis === 'cod')
                                    <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-5 text-center text-emerald-800">
                                        <div class="text-4xl mb-3">🏠</div>
                                        <p class="font-black">Bayar saat paket tiba!</p>
                                        <p class="mt-2 text-sm">Siapkan uang tunai sebesar <strong>Rp {{ number_format($pembayaran->jumlah_pembayaran, 0, ',', '.') }}</strong> saat kurir datang.</p>
                                    </div>
                                @else
                                    <div class="rounded-3xl border border-slate-200 bg-[#F8FAFB] p-5 text-sm text-slate-600">
                                        Instruksi pembayaran untuk {{ optional($metode)->metode ?? 'metode pembayaran' }} ({{ optional($metode)->jenis ?? '—' }}).
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($pembayaran->status_pembayaran === 'menunggu_konfirmasi')
                            @if(!in_array(optional($metode)->jenis, ['cod', 'qris']))
                                <div class="rounded-3xl border border-slate-200 bg-[#F8FAFB] p-5">
                                    <h3 class="text-lg font-black text-slate-900">Upload Bukti Pembayaran</h3>
                                    <p class="mt-2 text-sm text-slate-500">Silakan upload bukti pembayaran Anda untuk verifikasi.</p>
                                    <form action="{{ route('payment.upload_proof', $transaksi->kode_transaksi) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                                        @csrf
                                        <label class="block cursor-pointer rounded-3xl border-2 border-dashed border-slate-200 bg-white p-6 text-center transition-all duration-200 hover:border-[#63A2BB] hover:shadow-lg hover:shadow-[#63A2BB]/10">
                                            <input type="file" name="bukti_pembayaran" class="hidden" accept="image/*" required>
                                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#63A2BB]/10 text-[#63A2BB]">
                                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <p class="mt-3 text-sm font-semibold text-slate-700">Klik untuk memilih file bukti transfer</p>
                                            <p class="mt-1 text-xs text-slate-400">Format JPG, JPEG, PNG. Maksimal 5MB.</p>
                                        </label>
                                        <button type="submit" class="btn-primary mt-4 w-full justify-center px-6 py-3 text-sm">Konfirmasi Pembayaran</button>
                                    </form>
                                </div>
                            @endif
                        @elseif($pembayaran->status_pembayaran === 'berhasil')
                            <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-800">
                                <p class="font-black">Pembayaran Berhasil</p>
                                <p class="mt-2 text-sm">Pembayaran Anda telah dikonfirmasi. Pesanan akan segera diproses dan dikirimkan.</p>
                            </div>
                        @elseif($pembayaran->status_pembayaran === 'gagal' || $pembayaran->status_pembayaran === 'ditolak')
                            <div class="rounded-3xl border border-rose-200 bg-rose-50 p-5 text-rose-800">
                                <p class="font-black">Pembayaran {{ ucfirst(str_replace('_', ' ', $pembayaran->status_pembayaran)) }}</p>
                                <p class="mt-2 text-sm">Pembayaran Anda tidak dapat diproses. Silakan coba lagi atau hubungi dukungan pelanggan.</p>
                                <a href="{{ route('checkout.index') }}" class="btn-primary mt-4 inline-flex px-5 py-3 text-sm">Coba Pembayaran Lagi</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="lg:col-span-4">
                <div class="card-surface p-6">
                    <h3 class="mb-4 text-lg font-black text-slate-900">Ringkasan Pesanan</h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                        @php $detailItems = $transaksi->transaksiDetail ?? [];
                        @endphp
                        @foreach($detailItems as $detail)
                            @php
                                $img = $detail->detailProduk->produk->images->first() ?? null;
                                $imgPath = $img ? ($img->url_lengkap ?? asset('storage/' . $img->url_gambar)) : asset('images/placeholder.png');
                            @endphp
                            <div class="flex gap-3 rounded-2xl border border-slate-200 bg-[#F8FAFB] p-3">
                                <img src="{{ $imgPath }}" alt="Produk" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-slate-200">
                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-2 text-sm font-semibold text-slate-800">{{ $detail->nama_produk_snap }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $detail->ukuran_snap }} · x{{ $detail->quantity }}</p>
                                </div>
                                <p class="text-sm font-black text-[#63A2BB]">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 space-y-2 border-t border-slate-200 pt-5 text-sm">
                        <div class="flex justify-between text-slate-600"><span>Subtotal Produk</span><span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between text-slate-600"><span>Ongkos Kirim</span><span>Rp {{ number_format($transaksi->ongkos_kirim, 0, ',', '.') }}</span></div>
                        @if($transaksi->diskon_voucher > 0)
                            <div class="flex justify-between text-emerald-600"><span>Diskon Voucher</span><span>- Rp {{ number_format($transaksi->diskon_voucher, 0, ',', '.') }}</span></div>
                        @endif
                        <div class="flex justify-between border-t border-slate-200 pt-2 text-lg font-black text-slate-900"><span>Total Belanja</span><span class="text-[#63A2BB]">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span></div>
                    </div>

                    @if(!$isExpired && in_array($pembayaran->status_pembayaran, ['menunggu', 'menunggu_konfirmasi']))
                        <form action="{{ route('payment.confirm', $transaksi->kode_transaksi) }}" method="POST" class="mt-5">
                            @csrf
                            <button type="submit" class="btn-primary inline-flex w-full justify-center px-5 py-3 text-sm">Konfirmasi Pembayaran</button>
                        </form>
                        <p class="mt-2 text-center text-xs text-slate-400">Klik untuk menyelesaikan pembayaran dan lanjut ke pesanan Anda.</p>
                    @elseif($pembayaran->status_pembayaran === 'berhasil')
                        <a href="{{ route('orders.index') }}" class="btn-primary mt-5 inline-flex w-full justify-center px-5 py-3 text-sm">Lihat Pesanan</a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            if (typeof showToast === 'function') {
                showToast('Kode transaksi disalin');
            } else {
                alert('Kode transaksi disalin: ' + text);
            }
        }, function(err) {
            console.error('Async: Could not copy text: ', err);
        });
    }

    function paymentCountdown(expiredAt) {
        return {
            expiredTime: new Date(expiredAt).getTime(),
            timeLeft: '00:00:00',
            tick: null,

            init() {
                this.updateTime();
                this.tick = setInterval(() => this.updateTime(), 1000);
            },

            updateTime() {
                const distance = this.expiredTime - new Date().getTime();

                if (distance <= 0) {
                    this.timeLeft = '00:00:00';
                    if (this.tick) clearInterval(this.tick);
                    return;
                }

                const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((distance % (1000 * 60)) / 1000);

                this.timeLeft = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
            }
        };
    }
</script>
@endsection
