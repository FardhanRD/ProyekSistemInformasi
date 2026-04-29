@extends('movr.layouts.app')

@section('content')

    {{-- BREADCRUMB --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <nav class="flex text-xs font-bold uppercase tracking-wider text-gray-500" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="hover:text-black transition">Home</a></li>
                    <li><span class="text-gray-300">/</span></li>
                    <li class="text-black">Shopping Bag</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6">

            {{-- PAGE HEADER --}}
            <div class="flex items-end justify-between mb-8 pb-4 border-b border-black">
                <h1 class="text-3xl md:text-4xl font-heading font-bold text-black uppercase tracking-tight">
                    Shopping Bag
                </h1>
                <span class="text-sm font-bold text-gray-500 mb-2">
                    {{ $keranjangItems->count() }} Items
                </span>
            </div>

            @if ($keranjangItems->count() > 0)
                <div class="flex flex-col lg:flex-row gap-12 lg:gap-16">

                    {{-- LEFT COLUMN: CART ITEMS --}}
                    <div class="lg:w-2/3">
                        <div class="flex flex-col">
                            @foreach ($keranjangItems as $item)
                                <div class="flex gap-4 md:gap-6 py-6 border-b border-gray-100 last:border-0 group">

                                    {{-- Product Image --}}
                                    <div
                                        class="w-24 h-32 md:w-32 md:h-40 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden border border-gray-100 relative">
                                        <a href="{{ route('produk.show', $item->produk->id) }}">
                                            @if ($item->produk->image)
                                                <img src="{{ asset('storage/' . $item->produk->image) }}"
                                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-300"><i
                                                        class="fas fa-image text-2xl"></i></div>
                                            @endif
                                        </a>
                                    </div>

                                    {{-- Details --}}
                                    <div class="flex flex-col flex-grow justify-between">

                                        {{-- Top Row: Name & Remove --}}
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p
                                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                    {{ $item->produk->category ? $item->produk->category->name : 'General' }}
                                                </p>
                                                <h3 class="text-base md:text-lg font-bold text-black leading-tight">
                                                    <a href="{{ route('produk.show', $item->produk->id) }}"
                                                        class="hover:underline">
                                                        {{ $item->produk->name }}
                                                    </a>
                                                </h3>
                                            </div>

                                            {{-- Remove Button --}}
                                            <form action="{{ route('keranjang.destroy', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-gray-400 hover:text-blue-500 transition p-1"
                                                    title="Remove Item">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>

                                        {{-- Bottom Row: Price & Qty --}}
                                        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mt-4">

                                            {{-- Qty Control --}}
                                            <form action="{{ route('keranjang.update', $item->id) }}" method="POST"
                                                class="flex items-center gap-3">
                                                @csrf
                                                @method('PUT')

                                                <div class="flex items-center border border-gray-300 rounded-md h-10 w-32">
                                                    {{-- Note: Type="submit" on buttons ensures form submits when clicked --}}
                                                    <button type="submit" name="action" value="decrease"
                                                        onclick="return decreaseQty(this)"
                                                        class="w-10 h-full flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-black transition border-r border-gray-200">
                                                        <i class="fas fa-minus text-xs"></i>
                                                    </button>

                                                    <input type="number" name="jumlah" value="{{ $item->jumlah }}"
                                                        min="1" max="{{ $item->produk->stock }}"
                                                        class="w-full h-full text-center border-none focus:ring-0 text-sm font-bold text-black appearance-none p-0">

                                                    <button type="submit" name="action" value="increase"
                                                        onclick="return increaseQty(this)"
                                                        class="w-10 h-full flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-black transition border-l border-gray-200">
                                                        <i class="fas fa-plus text-xs"></i>
                                                    </button>
                                                </div>

                                                {{-- Hidden update button (Only shows if JS disabled or manual type) --}}
                                                <button type="submit"
                                                    class="text-xs font-bold text-black border-b border-black pb-0.5 hover:text-gray-600 hover:border-gray-600 transition md:hidden">
                                                    Update
                                                </button>
                                            </form>

                                            {{-- Price --}}
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500 mb-1">Total Price</p>
                                                <p class="text-lg font-bold text-black">
                                                    Rp
                                                    {{ number_format($item->jumlah * $item->harga_saat_ini, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            <a href="{{ route('home') }}"
                                class="inline-flex items-center text-sm font-bold text-black hover:text-gray-600 transition group">
                                <i
                                    class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                                Continue Shopping
                            </a>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: SUMMARY --}}
                    <div class="lg:w-1/3">
                        <div class="bg-gray-50 rounded-xl p-6 lg:p-8 sticky top-8 border border-gray-100">
                            <h2 class="text-xl font-heading font-bold text-black uppercase mb-6 tracking-wide">Order Summary
                            </h2>

                            <div class="space-y-4 mb-6 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span class="font-bold text-black">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Ongkos Kirim</span>
                                    <span class="font-bold text-black">Rp {{ number_format(15000, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Biaya Layanan</span>
                                    <span class="font-bold text-black">Rp {{ number_format(1000, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-4 mb-8">
                                <div class="flex justify-between items-end">
                                    <span class="text-base font-bold text-black uppercase">Total</span>
                                    <span class="text-2xl font-bold text-black">Rp
                                        {{ number_format($total + 15000 + 1000, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            {{-- Pastikan nama route 'checkout.index' atau 'checkout' sudah ada di web.php Anda --}}
                            <a href="{{ route('checkout.index') }}"
                                class="block text-center w-full bg-black text-white py-4 rounded-lg font-bold uppercase tracking-widest text-sm hover:bg-gray-800 transition shadow-lg shadow-gray-200 mb-4">
                                Checkout Now
                            </a>

                            {{-- Secure Icons --}}
                            <div class="flex justify-center gap-4 text-gray-300 text-2xl">
                                <i class="fab fa-cc-visa"></i>
                                <i class="fab fa-cc-mastercard"></i>
                                <i class="fas fa-lock text-lg pt-1"></i>
                            </div>
                        </div>
                    </div>

                </div>
            @else
                {{-- EMPTY STATE --}}
                <div
                    class="flex flex-col items-center justify-center py-20 text-center border-2 border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                    <div
                        class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-sm text-gray-300">
                        <i class="fas fa-shopping-bag text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-black mb-2 uppercase tracking-wide">Your bag is empty</h3>
                    <p class="text-gray-500 mb-8 max-w-sm mx-auto">Looks like you haven't added anything to your cart yet.
                    </p>
                    <a href="{{ route('home') }}"
                        class="px-8 py-3 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-lg hover:bg-gray-800 transition">
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- Script untuk manipulasi input number sebelum submit (UX Improvement) --}}
    <script>
        function showCartToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-6 right-6 z-[9999] px-4 py-3 rounded-lg text-sm font-semibold shadow-lg transition-all duration-300 ${type === 'success' ? 'bg-black text-white' : 'bg-blue-500 text-white'}`;
            toast.textContent = message;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-8px)';
                setTimeout(() => toast.remove(), 300);
            }, 1800);
        }

        function increaseQty(btn) {
            const form = btn.closest('form');
            const input = form.querySelector('input[name="jumlah"]');
            const max = parseInt(input.max || '999999', 10);
            const current = parseInt(input.value || '1', 10);

            if (current >= max) {
                showCartToast('Stok maksimum tercapai!', 'error');
                return false;
            }

            input.value = current + 1;
            return true;
        }

        function decreaseQty(btn) {
            const form = btn.closest('form');
            const input = form.querySelector('input[name="jumlah"]');
            const current = parseInt(input.value || '1', 10);

            if (current <= 1) {
                return false;
            }

            input.value = current - 1;
            return true;
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if (session('status'))
                showCartToast(@json(session('status')));
            @endif

            @if (session('error'))
                showCartToast(@json(session('error')), 'error');
            @endif
        });
    </script>
@endsection
