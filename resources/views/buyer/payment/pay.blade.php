@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Pembayaran Pesanan</h1>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-6">
                {{ session('info') }}
            </div>
        @endif

        @if($isExpired)
            <div class="bg-red-50 p-6 rounded-lg text-center shadow-sm border border-red-200">
                <h2 class="text-xl font-bold text-red-600 mb-2">Pembayaran Kadaluarsa</h2>
                <p class="text-gray-700 mb-4">Waktu pembayaran untuk transaksi ini telah habis.</p>
                <a href="{{ route('home') }}" class="inline-block bg-blue-600 text-white font-bold py-2 px-6 rounded shadow hover:bg-blue-700 transition">Belanja Kembali</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Bagian Kiri: Instruksi & Form -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Timer & Kode Transaksi -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <div class="flex justify-between items-center border-b pb-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Kode Transaksi</p>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-lg" id="kode-transaksi">{{ $transaksi->kode_transaksi }}</span>
                                    <button onclick="copyToClipboard('{{ $transaksi->kode_transaksi }}')" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Salin
                                    </button>
                                </div>
                            </div>
                            <div class="text-right text-red-600"
                                x-data="countdown('{{ $pembayaran->expired_at }}')"
                                x-init="start()">
                                <p class="text-sm">Batas Waktu Pembayaran</p>
                                <p class="font-bold text-xl"><span x-text="hours"></span>:<span x-text="minutes"></span>:<span x-text="seconds"></span></p>
                            </div>
                        </div>

                        <div class="text-center bg-gray-50 p-4 rounded mb-4">
                            <p class="text-gray-600 text-sm mb-1">Total Pembayaran</p>
                            <p class="text-3xl font-bold text-orange-600">Rp {{ number_format($pembayaran->jumlah_pembayaran, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Instruksi Pembayaran -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="font-bold text-lg mb-4">Instruksi Pembayaran</h3>
                        
                        @if($pembayaran->metode->jenis_metode === 'transfer')
                            <p class="text-gray-700 mb-4">Silakan transfer tepat sesuai nominal di atas ke rekening berikut:</p>
                            <div class="bg-blue-50 p-4 rounded border border-blue-100 mb-4">
                                <p class="font-bold">{{ $akunPembayaran->nama_akun ?? 'PT. MOVR' }}</p>
                                <p class="text-lg font-mono">{{ $akunPembayaran->nomor_akun ?? '-' }}</p>
                                <p class="text-sm text-gray-600">Bank {{ $pembayaran->metode->nama_metode }}</p>
                            </div>
                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-2">
                                <li>Gunakan ATM, M-Banking, atau Internet Banking.</li>
                                <li>Pastikan nominal transfer sesuai.</li>
                                <li>Simpan bukti transfer Anda.</li>
                            </ul>
                        @elseif($pembayaran->metode->jenis_metode === 'ewallet')
                            <p class="text-gray-700 mb-4">Silakan bayar menggunakan {{ $pembayaran->metode->nama_metode }} ke nomor berikut:</p>
                            <div class="bg-green-50 p-4 rounded border border-green-100 mb-4">
                                <p class="font-bold">{{ $akunPembayaran->nama_akun ?? 'MOVR Official' }}</p>
                                <p class="text-lg font-mono">{{ $akunPembayaran->nomor_akun ?? '-' }}</p>
                            </div>
                        @elseif($pembayaran->metode->jenis_metode === 'qris')
                            <div class="text-center">
                                <p class="text-gray-700 mb-4">Scan QRIS berikut menggunakan aplikasi e-wallet atau m-banking Anda:</p>
                                <div class="inline-block p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 mb-4">
                                    <div class="w-48 h-48 bg-gray-200 flex items-center justify-center text-gray-500">
                                        [ Placeholder QR Code QRIS ]
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500">Pastikan nama merchant adalah MOVR.</p>
                            </div>
                        @elseif($pembayaran->metode->jenis_metode === 'cod')
                            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded text-yellow-800">
                                <p class="font-bold flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Bayar di Tempat (COD)
                                </p>
                                <p class="mt-2 text-sm">Anda telah memilih metode pembayaran COD. Silakan siapkan uang tunai sebesar <strong>Rp {{ number_format($pembayaran->jumlah_pembayaran, 0, ',', '.') }}</strong> saat kurir mengantarkan paket Anda.</p>
                            </div>
                        @else
                            <p class="text-gray-600">Instruksi pembayaran untuk {{ $pembayaran->metode->nama_metode }} ({{ $pembayaran->metode->jenis_metode }}).</p>
                        @endif
                    </div>

                    <!-- Payment Status & Upload Bukti -->
                    @if($pembayaran->status_pembayaran === 'menunggu_konfirmasi')
                        @if(!in_array($pembayaran->metode->jenis_metode, ['cod', 'qris']))
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <h3 class="font-bold text-lg mb-4">Upload Bukti Pembayaran</h3>
                            <p class="text-gray-600 text-sm mb-4">Silakan upload bukti pembayaran Anda di bawah ini untuk verifikasi lebih lanjut.</p>
                            <form action="{{ route('payment.upload_proof', $transaksi->kode_transaksi) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="bukti_pembayaran">
                                        File Bukti (JPG, JPEG, PNG)
                                    </label>
                                    <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" accept="image/*" required>
                                    @error('bukti_pembayaran')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition">
                                    Konfirmasi Pembayaran
                                </button>
                            </form>
                        </div>
                        @endif
                    @elseif($pembayaran->status_pembayaran === 'berhasil')
                        <div class="bg-green-50 border border-green-200 p-6 rounded-lg">
                            <div class="flex items-center gap-3 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <p class="font-bold text-green-800">Pembayaran Berhasil</p>
                            </div>
                            <p class="text-green-700 text-sm">Pembayaran Anda telah dikonfirmasi. Pesanan Anda akan segera diproses dan dikirimkan.</p>
                        </div>
                    @elseif($pembayaran->status_pembayaran === 'gagal' || $pembayaran->status_pembayaran === 'ditolak')
                        <div class="bg-red-50 border border-red-200 p-6 rounded-lg">
                            <div class="flex items-center gap-3 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <p class="font-bold text-red-800">Pembayaran {{ ucfirst(str_replace('_', ' ', $pembayaran->status_pembayaran)) }}</p>
                            </div>
                            <p class="text-red-700 text-sm mb-4">Pembayaran Anda tidak dapat diproses. Silakan coba lagi atau hubungi dukungan pelanggan.</p>
                            <a href="{{ route('checkout.index') }}" class="inline-block bg-red-600 text-white font-bold py-2 px-4 rounded hover:bg-red-700 transition">Coba Pembayaran Lagi</a>
                        </div>
                    @endif
                </div>

                <!-- Bagian Kanan: Ringkasan Pesanan -->
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="font-bold text-lg mb-4 border-b pb-2">Ringkasan Pesanan</h3>
                        
                        <div class="space-y-4 mb-4 max-h-60 overflow-y-auto pr-2">
                            @foreach($transaksi->details as $detail)
                                <div class="flex gap-3">
                                    @php
                                        $img = $detail->detailProduk->produk->images->first();
                                        $imgPath = $img ? asset('storage/'.$img->gambar_url) : 'https://placehold.co/100x100?text=No+Image';
                                    @endphp
                                    <img src="{{ $imgPath }}" alt="Produk" class="w-16 h-16 object-cover rounded">
                                    <div class="flex-1 text-sm">
                                        <p class="font-bold line-clamp-2">{{ $detail->detailProduk->produk->nama_produk }}</p>
                                        <p class="text-gray-500">{{ $detail->quantity }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t pt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal Produk</span>
                                <span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ongkos Kirim</span>
                                <span>Rp {{ number_format($transaksi->ongkos_kirim, 0, ',', '.') }}</span>
                            </div>
                            @if($transaksi->diskon_voucher > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Diskon Voucher</span>
                                <span>- Rp {{ number_format($transaksi->diskon_voucher, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2">
                                <span>Total Belanja</span>
                                <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Kode transaksi disalin: ' + text);
        }, function(err) {
            console.error('Async: Could not copy text: ', err);
        });
    }

    function countdown(expiredAt) {
        return {
            expiredTime: new Date(expiredAt).getTime(),
            now: new Date().getTime(),
            distance: 0,
            hours: '00',
            minutes: '00',
            seconds: '00',

            start() {
                this.updateTime();
                setInterval(() => {
                    this.updateTime();
                }, 1000);
            },

            updateTime() {
                this.now = new Date().getTime();
                this.distance = this.expiredTime - this.now;

                if (this.distance < 0) {
                    this.hours = '00';
                    this.minutes = '00';
                    this.seconds = '00';
                    if (this.distance > -2000 && this.distance <= 0) {
                        window.location.reload();
                    }
                    return;
                }

                let h = Math.floor((this.distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let m = Math.floor((this.distance % (1000 * 60 * 60)) / (1000 * 60));
                let s = Math.floor((this.distance % (1000 * 60)) / 1000);

                this.hours = h.toString().padStart(2, '0');
                this.minutes = m.toString().padStart(2, '0');
                this.seconds = s.toString().padStart(2, '0');
            }
        };
    }
</script>
@endpush
@endsection
