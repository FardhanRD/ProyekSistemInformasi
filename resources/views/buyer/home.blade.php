@extends('layouts.buyer')

@section('title', 'Home - MOVR')

@section('content')
@php
    $heroProduct = $newArrivals->first();
    $heroImage = optional($heroProduct?->gambarUtama)->url_lengkap
        ?? optional($heroProduct?->gambarUtama)->url_safe
        ?? asset('images/default-banner.svg');

    $tickerItems = ['Running', 'Basketball', 'Football', 'Gym', 'Tennis', 'Cycling', 'Swimming', 'Yoga', 'Boxing', 'Training'];
    $sportCards = [
        [
            'image' => 'https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?auto=format&fit=crop&w=600&q=80', 
            'title' => 'Running', 
            'subtitle' => 'Shoes, apparel, and endurance gear'
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&w=600&q=80', 
            'title' => 'Gym & Training', 
            'subtitle' => 'Strength essentials and activewear'
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=600&q=80', 
            'title' => 'Football', 
            'subtitle' => 'Kits, boots, and match-day equipment'
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1595435934249-5df7ed86e1c0?auto=format&fit=crop&w=600&q=80', 
            'title' => 'Racket Sports', 
            'subtitle' => 'Fast play gear for tennis and more'
        ],
    ];
    $featureStats = [
        ['value' => '2K+', 'label' => 'Products curated'],
        ['value' => '15+', 'label' => 'Sport categories'],
        ['value' => '98%', 'label' => 'Satisfaction rate'],
    ];
@endphp

<div class="space-y-16 py-6 sm:py-10">
    <style>
        @keyframes movrTicker {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        @keyframes movrFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .movr-ticker-track {
            animation: movrTicker 22s linear infinite;
        }

        .movr-float {
            animation: movrFloat 4s ease-in-out infinite;
        }

        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <section class="section-shell">
        <div class="relative overflow-hidden rounded-[2rem] border border-slate-800 bg-[#0A1020] text-white shadow-2xl shadow-slate-300/20">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(99,162,187,0.36),transparent_34%),linear-gradient(125deg,rgba(10,16,32,0.96),rgba(13,23,44,0.92))]"></div>
            <div class="absolute -right-12 top-12 h-56 w-56 rounded-full border border-white/10"></div>
            <div class="absolute -left-16 bottom-0 h-72 w-72 rounded-full border border-[#63A2BB]/15"></div>

            <div class="relative grid gap-10 px-6 py-10 md:grid-cols-2 md:px-12 md:py-16 lg:px-16 lg:py-20">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-3 rounded-full border border-[#63A2BB]/30 bg-[#63A2BB]/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.28em] text-[#8FD0E3]">
                        <span class="h-2 w-2 rounded-full bg-[#63A2BB] shadow-[0_0_0_6px_rgba(99,162,187,0.18)]"></span>
                        New Drop 2026
                    </div>

                    <div class="space-y-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.32em] text-white/55">PUSH YOUR LIMITS</p>
                        <h1 class="max-w-xl text-4xl font-black leading-[0.9] tracking-tight sm:text-5xl lg:text-7xl">
                            <span class="block">PUSH</span>
                            <span class="block text-transparent [-webkit-text-stroke:2px_#63A2BB]">YOUR</span>
                            <span class="block text-[#63A2BB]">LIMITS</span>
                        </h1>
                        <p class="max-w-xl text-sm leading-7 text-white/70 sm:text-base">
                            Premium sportswear, footwear, and gear untuk latihan, kompetisi, dan lifestyle aktif yang bergerak cepat.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('product.index') }}" class="inline-flex items-center justify-center rounded-full bg-[#63A2BB] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-[#63A2BB]/30 transition hover:-translate-y-0.5 hover:bg-[#4e8fa8]">
                            Shop Now
                            <span class="ml-1">→</span>
                        </a>
                        <a href="#shop-by-sport" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-6 py-3 text-sm font-bold text-white transition hover:border-[#63A2BB]/40 hover:bg-white/10">
                            Explore Sports
                        </a>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        @foreach($featureStats as $stat)
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4 backdrop-blur-sm">
                                <div class="text-2xl font-black text-white">{{ $stat['value'] }}</div>
                                <div class="mt-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">{{ $stat['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="relative flex items-center justify-center">
                    <div class="movr-float relative w-full max-w-sm rounded-[2rem] border border-white/10 bg-white/8 p-4 backdrop-blur-xl">
                        <div class="absolute -left-4 top-8 rounded-2xl bg-white px-4 py-3 text-slate-900 shadow-2xl">
                            <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#63A2BB]">Top Pick</div>
                            <div class="mt-1 text-sm font-black">Sports Ready</div>
                        </div>

                        <div class="overflow-hidden rounded-[1.5rem] bg-slate-900/40">
                            <img src="{{ $heroImage }}" alt="Hero product" class="h-[350px] w-full object-cover object-center">
                        </div>

                        <div class="mt-4 rounded-[1.5rem] border border-white/10 bg-white/5 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/40">Featured product</div>
                                    <div class="mt-1 text-base font-bold text-white">{{ $heroProduct->nama_produk ?? 'Training Essential' }}</div>
                                    <div class="text-sm text-white/55">{{ $heroProduct->tipe_olahraga ?? 'Performance gear for every session' }}</div>
                                </div>
                                <div class="rounded-2xl bg-[#63A2BB] px-4 py-3 text-right text-white">
                                    <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/80">From</div>
                                    <div class="text-lg font-black">Rp {{ number_format($heroProduct->harga_dasar ?? 349000, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -bottom-5 right-6 rounded-2xl border border-white/10 bg-white px-4 py-3 text-slate-900 shadow-2xl shadow-black/20">
                        <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#63A2BB]">4.9 / 5.0</div>
                        <div class="text-sm font-semibold">Rated by athletes</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden border-y border-[#63A2BB]/15 bg-[#63A2BB] py-4 text-white">
        <div class="movr-ticker-track flex w-max items-center gap-10 whitespace-nowrap px-4 text-xs font-black uppercase tracking-[0.35em] sm:text-sm">
            @foreach(array_merge($tickerItems, $tickerItems) as $tickerItem)
                <span class="inline-flex items-center gap-3">
                    <span class="h-1.5 w-1.5 rounded-full bg-white/70"></span>
                    {{ $tickerItem }}
                </span>
            @endforeach
        </div>
    </section>

    {{-- Flash Sale Section --}}
    @if(isset($flashProducts) && $flashProducts->isNotEmpty())
        <section class="section-shell space-y-6" x-data="countdown('{{ $flashEndTime }}')" x-init="init()">
            <div class="rounded-[2rem] border border-[#FF4E4E]/30 bg-gradient-to-br from-[#1C0D0D] to-[#0A0505] p-6 sm:p-8 lg:p-10 shadow-xl shadow-red-950/20 relative overflow-hidden">
                {{-- Decorative background glow --}}
                <div class="absolute -right-24 -top-24 w-80 h-80 rounded-full bg-red-600/10 blur-[80px]"></div>
                <div class="absolute -left-24 -bottom-24 w-80 h-80 rounded-full bg-[#63A2BB]/10 blur-[80px]"></div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-red-500/10 relative z-10">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-2 rounded-full border border-red-500/30 bg-red-500/10 px-4 py-1 text-xs font-black uppercase tracking-[0.2em] text-[#FF4E4E] shadow-sm animate-pulse">
                            ⚡ FLASH SALE
                        </div>
                        <h2 class="text-3xl font-black text-white tracking-tight">Deals of the Day</h2>
                        <p class="text-sm text-slate-400 font-medium">Beli sekarang sebelum kehabisan! Stok dan waktu terbatas.</p>
                    </div>

                    {{-- Countdown Timer --}}
                    <div class="flex items-center gap-3 bg-black/40 border border-red-500/20 px-5 py-3 rounded-2xl backdrop-blur-md">
                        <span class="text-[11px] font-black uppercase tracking-[0.2em] text-red-500">BERAKHIR:</span>
                        <div class="flex items-center gap-1.5 font-mono text-lg font-black text-white">
                            <span class="bg-red-500/10 border border-red-500/20 text-[#FF4E4E] px-2.5 py-1 rounded-xl" x-text="hours">00</span>
                            <span class="text-red-500">:</span>
                            <span class="bg-red-500/10 border border-red-500/20 text-[#FF4E4E] px-2.5 py-1 rounded-xl" x-text="minutes">00</span>
                            <span class="text-red-500">:</span>
                            <span class="bg-red-500/10 border border-red-500/20 text-[#FF4E4E] px-2.5 py-1 rounded-xl" x-text="seconds">00</span>
                        </div>
                    </div>
                </div>

                {{-- Products Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 pt-8 relative z-10">
                    @foreach($flashProducts as $fp)
                        @php
                            $produk = $fp->produk;
                            $promo = $fp->promo;
                            $originalPrice = (float) $produk->harga_dasar;
                            $discountAmount = (float) $promo->diskon;
                            $finalPrice = max(0, $originalPrice - $discountAmount);
                            $percent = $originalPrice > 0 ? round(($discountAmount / $originalPrice) * 100) : 0;
                            
                            $imageSource = optional($produk->gambarUtama)->url_lengkap 
                                ?? optional($produk->gambarUtama)->url_safe 
                                ?? asset('images/placeholder.png');
                                
                            // stock progress
                            $stokTerjual = $produk->total_terjual ?? 0;
                            $stokFlash = $promo->stok_flash_sale ?? 10;
                            $stokTotal = $stokFlash + $stokTerjual;
                            $progressPct = $stokTotal > 0 ? round(($stokTerjual / $stokTotal) * 100) : 0;
                        @endphp
                        
                        <div class="group relative flex h-full flex-col overflow-hidden rounded-[1.8rem] border border-red-500/10 bg-slate-950 p-4 hover:border-red-500/30 transition-all duration-300">
                            {{-- Image container --}}
                            <div class="relative aspect-[3/4] overflow-hidden rounded-2xl bg-slate-900">
                                <a href="{{ route('product.show', $produk->slug) }}" class="block h-full w-full">
                                    <img src="{{ $imageSource }}" alt="{{ $produk->nama_produk }}" class="h-full w-full object-cover transition-transform duration-500 ease-in-out group-hover:scale-105" onerror="this.src='{{ asset('images/placeholder.png') }}';">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 to-transparent"></div>
                                </a>
                                
                                {{-- Percent Discount Badge --}}
                                <span class="absolute left-3 top-3 rounded-full bg-red-600 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-white shadow-lg shadow-red-950/50">
                                    -{{ $percent }}%
                                </span>
                            </div>

                            {{-- Product Details --}}
                            <div class="flex flex-1 flex-col mt-4">
                                @if(isset($produk->kategori->nama_kategori))
                                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-[#FF4E4E] mb-1.5 block">
                                        {{ $produk->kategori->nama_kategori }}
                                    </span>
                                @endif

                                <a href="{{ route('product.show', $produk->slug) }}" class="line-clamp-2 text-sm font-extrabold text-white transition-colors duration-200 hover:text-[#FF4E4E] leading-snug min-h-[2.5rem]">
                                    {{ $produk->nama_produk }}
                                </a>

                                {{-- Price display --}}
                                <div class="mt-4 space-y-0.5">
                                    <div class="text-[11px] font-bold text-slate-500 line-through tracking-wider">
                                        Rp {{ number_format($originalPrice, 0, ',', '.') }}
                                    </div>
                                    <div class="text-base font-black text-[#FF4E4E]">
                                        Rp {{ number_format($finalPrice, 0, ',', '.') }}
                                    </div>
                                </div>

                                {{-- Stock progress bar --}}
                                <div class="mt-4 space-y-1.5">
                                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-400">
                                        <span>Tersedia: <span class="text-white font-black">{{ $stokFlash }} Pcs</span></span>
                                        <span>{{ $progressPct }}% Terjual</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-red-600 to-[#FF4E4E] rounded-full" style="width: {{ min(100, max(8, $progressPct)) }}%"></div>
                                    </div>
                                </div>

                                {{-- Action button --}}
                                <div class="mt-5">
                                    <a href="{{ route('product.show', $produk->slug) }}" class="inline-flex items-center justify-center w-full rounded-full bg-red-600 py-3 text-xs font-bold text-white transition-all duration-300 hover:bg-[#FF4E4E] hover:-translate-y-0.5 shadow-lg shadow-red-950/20">
                                        Beli Sekarang ⚡
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="shop-by-sport" class="section-shell space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#63A2BB]">Shop by Sport</p>
                <h2 class="mt-1 text-3xl font-black text-slate-900">Find your next move</h2>
            </div>
            <a href="{{ route('product.index') }}" class="text-sm font-semibold text-[#63A2BB] hover:text-[#4e8fa8]">View all →</a>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($sportCards as $card)
                <a href="{{ route('product.index') }}?q={{ urlencode($card['title']) }}" class="group relative block h-[320px] overflow-hidden rounded-[2rem] shadow-md transition-all duration-500 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-[#63A2BB]/18">
                    <!-- Background Image -->
                    <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" class="absolute inset-0 h-full w-full object-cover object-center transition-transform duration-700 group-hover:scale-110">
                    
                    <!-- Dark Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent opacity-85 transition-opacity duration-300 group-hover:opacity-90"></div>
                    
                    <!-- Border Highlight -->
                    <div class="absolute inset-0 border-2 border-transparent rounded-[2rem] transition-colors duration-300 group-hover:border-[#63A2BB]/30"></div>

                    <!-- Content -->
                    <div class="absolute inset-x-0 bottom-0 p-6 flex flex-col justify-end z-10">
                        <span class="w-8 h-8 rounded-full bg-white/10 backdrop-blur-md border border-white/15 flex items-center justify-center text-white mb-3 transition-transform duration-300 group-hover:translate-x-1">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </span>
                        <h3 class="text-lg font-black text-white tracking-tight">{{ $card['title'] }}</h3>
                        <p class="mt-1 text-[11px] text-white/70 leading-normal font-medium">{{ $card['subtitle'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="section-shell space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#63A2BB]">Deals & Highlights</p>
                <h2 class="mt-1 text-3xl font-black text-slate-900">Promo banners built for motion</h2>
            </div>
            <a href="{{ route('product.index') }}?sort=terbaru" class="text-sm font-semibold text-[#63A2BB] hover:text-[#4e8fa8]">See collections →</a>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
            <!-- Main Banner with Background Image -->
            <div class="relative overflow-hidden rounded-[2rem] h-[360px] shadow-xl border border-slate-800 transition-all duration-500 hover:shadow-2xl hover:shadow-[#63A2BB]/12 group">
                <img src="https://images.unsplash.com/photo-1483721310020-03333e577078?auto=format&fit=crop&w=1200&q=80" alt="Summer Sport Collection" class="absolute inset-0 h-full w-full object-cover object-center transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent opacity-90"></div>
                <div class="absolute inset-0 border-2 border-transparent rounded-[2rem] transition-colors duration-300 group-hover:border-[#63A2BB]/30"></div>
                
                <div class="absolute inset-x-0 bottom-0 p-8 flex flex-col justify-end z-10">
                    <div>
                        <span class="inline-flex rounded-full border border-[#63A2BB]/40 bg-[#63A2BB]/15 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.24em] text-[#8FD0E3] shadow-sm">Flash Sale · Up to 40% Off</span>
                    </div>
                    <h3 class="mt-4 text-3xl font-black tracking-tight text-white sm:text-4xl">Summer Sport Collection</h3>
                    <p class="mt-2 max-w-lg text-sm text-white/80 font-medium">
                        Gear ringan, breathable, dan siap dipakai untuk sesi latihan intens maupun gaya sehari-hari.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('product.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-black text-slate-900 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg">
                            Shop the Sale
                            <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column Banner Cards with Photo Backgrounds -->
            <div class="grid gap-6">
                <!-- Card 1 -->
                <div class="relative overflow-hidden rounded-[2rem] h-[168px] border border-slate-800 shadow-lg transition-all duration-500 hover:shadow-2xl hover:border-[#63A2BB]/40 group">
                    <img src="https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&w=600&q=80" alt="Basketball Ready" class="absolute inset-0 h-full w-full object-cover object-center transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/70 to-transparent opacity-90"></div>
                    
                    <div class="absolute inset-0 p-6 flex flex-col justify-center z-10">
                        <div class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8FD0E3]">New Arrivals</div>
                        <h4 class="mt-1 text-xl font-black tracking-tight text-white">Basketball Ready</h4>
                        <p class="mt-1 text-xs text-white/70 max-w-xs leading-normal">Curated pieces for faster footwork and cleaner looks.</p>
                        <a href="{{ route('product.index') }}?q=basketball" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-[#8FD0E3] transition-all duration-200 group-hover:gap-3">
                            Explore <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="relative overflow-hidden rounded-[2rem] h-[168px] border border-slate-800 shadow-lg transition-all duration-500 hover:shadow-2xl hover:border-[#63A2BB]/40 group">
                    <img src="https://images.unsplash.com/photo-1595435934249-5df7ed86e1c0?auto=format&fit=crop&w=600&q=80" alt="Racket Sports" class="absolute inset-0 h-full w-full object-cover object-center transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/70 to-transparent opacity-90"></div>
                    
                    <div class="absolute inset-0 p-6 flex flex-col justify-center z-10">
                        <div class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8FD0E3]">Limited Edition</div>
                        <h4 class="mt-1 text-xl font-black tracking-tight text-white">Racket Sports</h4>
                        <p class="mt-1 text-xs text-white/70 max-w-xs leading-normal">Fast-match essentials for training and competition.</p>
                        <a href="{{ route('product.index') }}?q=tennis" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-[#8FD0E3] transition-all duration-200 group-hover:gap-3">
                            Explore <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Recommended Products (Horizontal Carousel) --}}
    @if(isset($recommendedProducts) && $recommendedProducts->isNotEmpty())
        <section class="section-shell space-y-6">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#63A2BB]">Rekomendasi Untuk Anda</p>
                    <h2 class="mt-1 text-3xl font-black text-slate-900">Recommended Products</h2>
                </div>
                <span class="text-xs text-slate-400 font-semibold hidden sm:inline-flex items-center gap-1.5 bg-slate-100 px-3 py-1.5 rounded-full">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    Swipe ke kanan / kiri
                </span>
            </div>

            <div class="flex overflow-x-auto pb-4 gap-6 scroll-smooth snap-x snap-mandatory scrollbar-none" style="-webkit-overflow-scrolling: touch;">
                @foreach($recommendedProducts as $product)
                    <div class="w-[260px] sm:w-[280px] flex-shrink-0 snap-start">
                        <x-product-card :produk="$product" :badge="'FOR YOU'" :showWishlistBtn="true" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- New Arrivals --}}
    @if($newArrivals->isNotEmpty())
        <section class="section-shell space-y-6">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#63A2BB]">Fresh Drop</p>
                    <h2 class="mt-1 text-3xl font-black text-slate-900">New Arrivals</h2>
                </div>
                <a href="{{ route('product.index') }}?sort=terbaru" class="text-sm font-semibold text-[#63A2BB] hover:text-[#4e8fa8]">View all →</a>
            </div>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                @foreach($newArrivals as $product)
                    <x-product-card :produk="$product" :badge="'NEW'" :showWishlistBtn="true" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function countdown(endTimeStr) {
        return {
            endTime: endTimeStr ? new Date(endTimeStr).getTime() : 0,
            hours: '00',
            minutes: '00',
            seconds: '00',
            timer: null,
            init() {
                if (!this.endTime) return;
                this.update();
                this.timer = setInterval(() => {
                    this.update();
                }, 1000);
            },
            update() {
                const now = new Date().getTime();
                const diff = this.endTime - now;
                if (diff <= 0) {
                    clearInterval(this.timer);
                    this.hours = '00';
                    this.minutes = '00';
                    this.seconds = '00';
                    return;
                }
                const h = Math.floor(diff / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);
                this.hours = String(h).padStart(2, '0');
                this.minutes = String(m).padStart(2, '0');
                this.seconds = String(s).padStart(2, '0');
            }
        }
    }
</script>
@endsection
