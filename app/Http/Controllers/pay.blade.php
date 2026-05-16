@extends('layouts.buyer')
@section('title', 'Instruksi Pembayaran')

@section('content')
<div class="container mx-auto my-10 max-w-4xl" x-data="paymentPage({{ optional($pembayaran->expired_at)->timestamp }})">

    {{-- State Kadaluarsa --}}
    @if($isExpired)
        <div class="rounded-3xl border border-rose-500/30 bg-rose-500/10 p-8 text-center">
            <h1 class="text-2xl font-bold text-rose-300">Pembayaran Kadaluarsa</h1>
            <p class="mt-2 text-slate-400">Waktu pembayaran untuk transaksi <strong class="text-white">{{ $transaksi->kode_transaksi }}</strong> telah berakhir.</p>
            <div class="mt-6">
                <a href="{{ route('cart.index') }}" class="rounded-xl bg-cyan-500 px-6 py-3 font-bold text-slate-950 hover:bg-cyan-400">
                    Buat Pesanan Ulang
                </a>
            </div>
        </div>
    @else
        {{-- State Normal (Menunggu Pembayaran) --}}
        <div class="space-y-8">
            <div class="text-center">
                <h1 class="text-3xl font-black">Selesaikan Pembayaran</h1>
                <p class="mt-2 text-slate-400">Selesaikan pembayaran Anda sebelum waktu berakhir.</p>
            </div>

            {{-- Countdown Timer --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-center">
                <div class="text-sm text-slate-400">Batas Waktu Pembayaran</div>
                <div class="mt-2 text-3xl font-bold tracking-widest text-cyan-400" x-text="countdown">
                    23:59:59
                </div>
            </div>

            {{-- Detail Pembayaran --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <div class="space-y-4">
                    {{-- Kode Transaksi --}}
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Kode Transaksi</span>
                        <div class="flex items-center gap-2">
                            <strong class="text-white">{{ $transaksi->kode_transaksi }}</strong>
                            <button @click="navigator.clipboard.writeText('{{ $transaksi->kode_transaksi }}'); alert('Kode disalin!')" class="text-cyan-400 hover:text-cyan-300" title="Salin Kode Transaksi">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM5 11a1 1 0 100 2h4a1 1 0 100-2H5z"></path><path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm2-1a1 1 0 00-1 1v10a1 1 0 001 1h12a1 1 0 001-1V5a1 1 0 00-1-1H4z" clip-rule="evenodd"></path></svg>
                            </button>
                        </div>
                    </div>
                    {{-- Total Pembayaran --}}
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Total Pembayaran</span>
                        <div class="flex items-center gap-2">
                            <strong class="text-xl text-white">Rp {{ number_format($pembayaran->jumlah_pembayaran, 0, ',', '.') }}</strong>
                            <button @click="navigator.clipboard.writeText('{{ $pembayaran->jumlah_pembayaran }}'); alert('Nominal disalin!')" class="text-cyan-400 hover:text-cyan-300" title="Salin Nominal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM5 11a1 1 0 100 2h4a1 1 0 100-2H5z"></path><path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm2-1a1 1 0 00-1 1v10a1 1 0 001 1h12a1 1 0 001-1V5a1 1 0 00-1-1H4z" clip-rule="evenodd"></path></svg>
                            </button>
                        </div>
                    </div>
                    @if($pembayaran->kode_unik)
                    <div class="text-right text-xs text-amber-400">
                        *Transfer sejumlah nominal di atas, termasuk 3 digit terakhir.
                    </div>
                    @endif
                </div>
            </div>

            {{-- Instruksi Pembayaran --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <h2 class="text-lg font-bold">Instruksi Pembayaran</h2>
                <div class="mt-4 rounded-2xl border border-white/10 bg-black/20 p-4">
                    <div class="flex items-center gap-4">
                        @if(optional($pembayaran->metode)->logo_url)
                        <img src="{{ $pembayaran->metode->logo_url }}" alt="{{ $pembayaran->metode->metode }}" class="h-10 w-auto rounded-lg">
                        @endif
                        <div class="font-bold">{{ optional($pembayaran->metode)->metode }}</div>
                    </div>
                    <div class="mt-4 space-y-4 border-t border-white/10 pt-4 text-sm">
                        @if(optional($pembayaran->metode)->jenis == 'transfer_bank')
                            @php
                                $rekeningKey = strtolower(optional($pembayaran->metode)->metode);
                                $rekening = $rekeningBank[$rekeningKey] ?? null;
                            @endphp
                            @if($rekening)
                                <p>Silakan lakukan transfer ke rekening berikut:</p>
                                <div class="space-y-2">
                                    <div class="flex justify-between"><span>Bank</span><strong>{{ $rekening['bank'] }}</strong></div>
                                    <div class="flex justify-between"><span>No. Rekening</span><strong>{{ $rekening['nomor'] }}</strong></div>
                                    <div class="flex justify-between"><span>Atas Nama</span><strong>{{ $rekening['nama'] }}</strong></div>
                                </div>
                                <p class="text-xs text-slate-400">Penting: Pastikan Anda mentransfer jumlah yang tepat termasuk kode unik untuk verifikasi otomatis.</p>
                            @else
                                <p>Informasi rekening tidak tersedia. Silakan hubungi customer service.</p>
                            @endif
                        @elseif(optional($pembayaran->metode)->jenis == 'ewallet')
                            <p>Silakan lakukan pembayaran ke nomor E-Wallet berikut:</p>
                            <div class="flex justify-between"><span>Nomor Tujuan</span><strong>081234567890 (a.n. MOVR Store)</strong></div>
                        @elseif(optional($pembayaran->metode)->jenis == 'qris')
                            <p>Silakan pindai kode QR di bawah ini menggunakan aplikasi pembayaran favorit Anda.</p>
                            <div class="mt-4 flex justify-center">
                                <div class="h-48 w-48 rounded-lg bg-white p-2">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $transaksi->kode_transaksi }}" alt="QR Code">
                                </div>
                            </div>
                        @elseif(optional($pembayaran->metode)->jenis == 'cod')
                            <p>Siapkan uang tunai dan bayarkan kepada kurir saat pesanan Anda tiba.</p>
                        @else
                             <p>Metode pembayaran tidak dikenali. Silakan hubungi customer service.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Upload Bukti Pembayaran --}}
            @if(in_array(optional($pembayaran->metode)->jenis, ['transfer_bank', 'ewallet']))
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <h2 class="text-lg font-bold">Konfirmasi Pembayaran</h2>
                <p class="mt-1 text-sm text-slate-400">Setelah melakukan pembayaran, unggah bukti transfer Anda di sini.</p>
                <form action="{{ route('payment.upload_proof', ['transaksi' => $transaksi->transaksi_id]) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                    @csrf
                    <div class="flex flex-wrap items-center gap-4">
                        <input type="file" name="bukti_pembayaran" required class="block w-full flex-1 text-sm text-slate-400 file:mr-4 file:rounded-full file:border-0 file:bg-cyan-500/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-cyan-300 hover:file:bg-cyan-500/20">
                        <button type="submit" class="rounded-xl bg-cyan-500 px-6 py-2 font-bold text-slate-950 hover:bg-cyan-400">Unggah</button>
                    </div>
                    @error('bukti_pembayaran') <div class="mt-2 text-sm text-rose-400">{{ $message }}</div> @enderror
                </form>
            </div>
            @endif

            {{-- Ringkasan Produk --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <h2 class="text-lg font-bold">Ringkasan Produk</h2>
                <div class="mt-4 space-y-3">
                    @foreach($transaksi->details as $detail)
                    <div class="flex items-start gap-3 rounded-2xl border border-white/10 bg-black/20 p-3">
                        <img src="{{ optional(optional($detail->detailProduk)->produk)->gambar_url ?? '' }}" class="h-16 w-16 rounded-xl object-cover" alt="produk" />
                        <div class="flex-1">
                            <div class="text-sm font-semibold">{{ $detail->nama_produk_snap }}</div>
                            <div class="mt-1 text-xs text-slate-400">Qty: {{ $detail->quantity }}</div>
                        </div>
                        <div class="text-sm font-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function paymentPage(expiryTimestamp) {
    return {
        countdown: '00:00:00',
        init() {
            if (!expiryTimestamp) return;
            const target = new Date(expiryTimestamp * 1000);
            const interval = setInterval(() => {
                const now = new Date();
                const diff = target.getTime() - now.getTime();

                if (diff <= 0) {
                    this.countdown = '00:00:00';
                    clearInterval(interval);
                    // Refresh halaman untuk menampilkan status kadaluarsa
                    setTimeout(() => window.location.reload(), 1500);
                    return;
                }

                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                this.countdown = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }, 1000);
        }
    }
}
</script>
@endsection