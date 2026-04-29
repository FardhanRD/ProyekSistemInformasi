@extends('movr.layouts.app')

@section('content')
<section class="py-12 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center mb-8">
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-black mr-4 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-black">Checkout</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white border border-gray-300 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-black mb-6 flex items-center">
                        <span class="w-8 h-8 rounded-full bg-accent-green text-white flex items-center justify-center text-sm font-bold mr-3">1</span>
                        Alamat Pengiriman
                    </h2>

                    <form id="addressForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-600 text-sm mb-2 font-medium">Nama Penerima</label>
                                <input type="text" id="receiver_name" value="{{ Auth::user()->name }}" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-black focus:outline-none focus:border-accent-green transition">
                            </div>
                            <div>
                                <label class="block text-gray-600 text-sm mb-2 font-medium">Nomor Telepon</label>
                                <input type="text" id="phone" placeholder="Contoh: 08123456789" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-black focus:outline-none focus:border-accent-green transition">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-600 text-sm mb-2 font-medium">Alamat Lengkap</label>
                            <textarea id="full_address" rows="3" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-black focus:outline-none focus:border-accent-green transition" placeholder="Nama Jalan, No Rumah, Kecamatan, Kota..."></textarea>
                        </div>
                    </form>
                </div>

                <div class="bg-white border border-gray-300 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-black mb-6 flex items-center">
                        <span class="w-8 h-8 rounded-full bg-accent-green text-white flex items-center justify-center text-sm font-bold mr-3">2</span>
                        Metode Pembayaran
                    </h2>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-accent-green rounded-lg cursor-pointer bg-gray-100 transition relative overflow-hidden group">
                            <input type="radio" name="payment_method" value="credit_card" class="text-accent-green focus:ring-accent-green w-4 h-4" checked>
                            <div class="ml-4">
                                <span class="block text-black font-bold group-hover:text-accent-green transition">Kartu Kredit/Debit</span>
                                <span class="text-gray-600 text-sm">Visa, Mastercard, JCB</span>
                            </div>
                            <i class="fas fa-credit-card ml-auto text-gray-500 group-hover:text-accent-green transition"></i>
                        </label>

                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer bg-white hover:border-accent-green transition relative overflow-hidden group">
                            <input type="radio" name="payment_method" value="bank_transfer" class="text-accent-green focus:ring-accent-green w-4 h-4">
                            <div class="ml-4">
                                <span class="block text-black font-bold group-hover:text-accent-green transition">Transfer Bank</span>
                                <span class="text-gray-600 text-sm">BCA, Mandiri, BRI, BNI</span>
                            </div>
                            <i class="fas fa-university ml-auto text-gray-500 group-hover:text-accent-green transition"></i>
                        </label>

                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer bg-white hover:border-accent-green transition relative overflow-hidden group">
                            <input type="radio" name="payment_method" value="gopay" class="text-accent-green focus:ring-accent-green w-4 h-4">
                            <div class="ml-4">
                                <span class="block text-black font-bold group-hover:text-accent-green transition">GoPay</span>
                                <span class="text-gray-600 text-sm">Pembayaran melalui GoPay</span>
                            </div>
                            <i class="fas fa-wallet ml-auto text-gray-500 group-hover:text-accent-green transition"></i>
                        </label>

                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer bg-white hover:border-accent-green transition relative overflow-hidden group">
                            <input type="radio" name="payment_method" value="ewallet" class="text-accent-green focus:ring-accent-green w-4 h-4">
                            <div class="ml-4">
                                <span class="block text-black font-bold group-hover:text-accent-green transition">E-Wallet</span>
                                <span class="text-gray-600 text-sm">OVO, Dana, LinkAja</span>
                            </div>
                            <i class="fas fa-mobile-alt ml-auto text-gray-500 group-hover:text-accent-green transition"></i>
                        </label>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-300 rounded-xl p-6 sticky top-6 shadow-xl">
                    <h2 class="text-lg font-bold text-black mb-4 border-b border-gray-300 pb-4">Ringkasan Pesanan</h2>

                    <div class="space-y-4 mb-6 border-b border-gray-300 pb-6">
                        @foreach($items as $item)
                        <div class="flex gap-4 items-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden border border-gray-300 flex-shrink-0">
                                @if(isset($item['image']) && $item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-500"><i class="fas fa-image"></i></div>
                                @endif
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <h3 class="text-black font-medium text-sm truncate">{{ $item['name'] }}</h3>
                                <p class="text-gray-600 text-xs mt-1">{{ $item['qty'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                            <div class="text-accent-green font-bold text-sm">
                                Rp {{ number_format($item['total'], 0, ',', '.') }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>Subtotal Produk</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>Ongkos Kirim</span>
                            <span>Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>Biaya Layanan</span>
                            <span>Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-gray-300 pt-4 mb-6">
                        <div class="flex justify-between items-end">
                            <span class="text-black font-bold">Total Bayar</span>
                            <span class="text-2xl font-bold text-accent-green">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button id="payButton" class="w-full bg-accent-green text-white py-3.5 rounded-xl font-bold hover:bg-accent-green/90 transition shadow-lg shadow-accent-green/20 lift-effect">
                        Bayar Sekarang
                    </button>

                    <div class="mt-4 flex justify-center items-center text-xs text-gray-600 bg-gray-100 py-2 rounded">
                        <i class="fas fa-lock mr-2"></i> Pembayaran Aman & Terenkripsi
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Midtrans Snap Script -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        document.getElementById('payButton').addEventListener('click', function() {
            // Get form data
            const phone = document.getElementById('phone').value;
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            // Validation
            if (!phone) {
                alert('Silakan isi nomor telepon Anda');
                return;
            }

            // Show loading state
            const payButton = document.getElementById('payButton');
            payButton.disabled = true;
            payButton.innerHTML = 'Memproses...';

            // Send AJAX request to process payment
            fetch('{{ route("checkout.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    phone: phone,
                    payment_method: paymentMethod
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to Midtrans payment page
                    window.location.href = data.redirect_url;
                } else {
                    // Show error message
                    alert(data.error || 'Terjadi kesalahan saat memproses pembayaran');
                    payButton.disabled = false;
                    payButton.innerHTML = 'Bayar Sekarang';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
                payButton.disabled = false;
                payButton.innerHTML = 'Bayar Sekarang';
            });
        });
    </script>
</section>
@endsection