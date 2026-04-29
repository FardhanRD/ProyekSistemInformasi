@extends('movr.layouts.app')

@section('content')

    {{-- BREADCRUMB --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <nav class="flex text-xs font-bold uppercase tracking-wider text-gray-500" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="hover:text-black transition">Home</a></li>
                    <li><span class="text-gray-300">/</span></li>
                    <li class="text-black">Wishlist</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6">
            
            {{-- HEADER --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 pb-6 border-b border-gray-100">
                <div>
                    <h1 class="text-4xl font-heading font-bold text-black uppercase tracking-tight mb-2">My Wishlist</h1>
                    <p class="text-gray-500 text-sm">Kelola produk yang ingin Anda beli nanti.</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="text-xs font-bold bg-black text-white px-3 py-1 rounded-md uppercase tracking-wider">
                        {{ $favorits->total() }} Items
                    </span>
                </div>
            </div>

            @if($favorits->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-12">
                    @foreach($favorits as $item)
                        @if($item->product)
                        {{-- CARD ITEM --}}
                        <div id="wishlist-item-{{ $item->product->id }}" class="group flex flex-col h-full fade-transition">
                            
                            {{-- IMAGE AREA (Tanpa Overlay Tombol) --}}
                            <div class="relative w-full aspect-[4/5] bg-gray-50 rounded-lg overflow-hidden mb-4 border border-gray-100">
                                <a href="{{ route('produk.show', $item->product->id) }}" class="block w-full h-full">
                                    @if($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" 
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <i class="fas fa-image text-3xl"></i>
                                        </div>
                                    @endif
                                </a>

                                {{-- BADGE STOCK --}}
                                @if($item->product->stock <= 0)
                                    <span class="absolute top-2 left-2 bg-gray-900 text-white text-[10px] font-bold px-2 py-1 uppercase tracking-wider rounded-sm">
                                        Sold Out
                                    </span>
                                @endif
                            </div>

                            {{-- INFO AREA --}}
                            <div class="flex flex-col flex-grow">
                                <div class="mb-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    {{ $item->product->category->name ?? 'Product' }}
                                </div>
                                
                                <h3 class="text-sm md:text-base font-bold text-black leading-tight mb-2 group-hover:text-gray-600 transition-colors">
                                    <a href="{{ route('produk.show', $item->product->id) }}">
                                        {{ $item->product->name }}
                                    </a>
                                </h3>
                                
                                <div class="text-sm font-bold text-black mb-4">
                                    Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                </div>

                                {{-- ACTION BUTTONS GROUP (Pindah ke Bawah) --}}
                                <div class="mt-auto flex gap-2 h-10">
                                    {{-- Tombol Add to Cart --}}
                                    @if($item->product->stock > 0)
                                        <form action="{{ route('keranjang.store') }}" method="POST" class="flex-grow">
                                            @csrf
                                            <input type="hidden" name="produk_id" value="{{ $item->product->id }}">
                                            <input type="hidden" name="jumlah" value="1">
                                            <button type="submit" class="w-full h-full bg-black text-white text-[10px] md:text-xs font-bold uppercase tracking-widest hover:bg-gray-800 transition-all rounded-md flex items-center justify-center gap-2">
                                                <i class="fas fa-shopping-bag"></i> <span class="hidden sm:inline">Add</span>
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex-grow">
                                            <button disabled class="w-full h-full bg-gray-100 text-gray-400 text-[10px] font-bold uppercase tracking-widest cursor-not-allowed rounded-md">
                                                Habis
                                            </button>
                                        </div>
                                    @endif

                                    {{-- Tombol Hapus (Intuitif & Jelas) --}}
                                    <button onclick="removeFavorite({{ $item->product->id }})" 
                                            class="w-10 h-full flex-none flex items-center justify-center border border-gray-200 rounded-md text-gray-400 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all"
                                            title="Hapus dari Wishlist">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-16 border-t border-gray-100 pt-8">
                    {{ $favorits->links() }}
                </div>

            @else
                {{-- EMPTY STATE --}}
                <div class="flex flex-col items-center justify-center py-24 text-center border-2 border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm text-gray-300">
                        <i class="far fa-heart text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-black mb-2 uppercase tracking-wide">Wishlist Kosong</h3>
                    <p class="text-gray-500 mb-8 max-w-sm mx-auto text-sm">Anda belum menyimpan produk apapun.</p>
                    <a href="{{ route('produk.index') }}" class="px-8 py-3 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-lg hover:bg-gray-800 transition">
                        Mulai Belanja
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- SCRIPT --}}
    <script>
        function showFavoriteToast(message, type = 'success') {
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

        function removeFavorite(productId) {
            // Konfirmasi standar browser yang aman dan dimengerti user
            if(!confirm('Hapus produk ini dari daftar favorit?')) return;

            const card = document.getElementById(`wishlist-item-${productId}`);

            fetch('{{ route('favorit.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'removed') {
                    showFavoriteToast('Produk dihapus dari favorit.');
                    // Animasi smooth
                    card.style.transition = "all 0.4s ease";
                    card.style.opacity = "0";
                    card.style.transform = "translateY(10px)";
                    
                    setTimeout(() => {
                        card.remove();
                        // Cek jika habis, reload untuk tampilkan empty state
                        if(document.querySelectorAll('[id^="wishlist-item-"]').length === 0) {
                            location.reload(); 
                        }
                    }, 400);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>

@endsection