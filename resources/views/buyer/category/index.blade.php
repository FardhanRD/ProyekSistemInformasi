@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-6">
        
        <!-- Sidebar Filter -->
        <aside class="w-full md:w-1/4 flex-shrink-0">
            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 sticky top-24">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold">Filter</h2>
                    <a href="{{ request()->url() }}" class="text-sm text-blue-600 hover:text-blue-800">Reset</a>
                </div>
                
                <form action="{{ request()->url() }}" method="GET" x-data="priceFilter({{ request('min_price', 0) }}, {{ request('max_price', $filterData['maxPrice'] ?? 1000000) }}, {{ $filterData['maxPrice'] ?? 1000000 }})">
                    <!-- Preserve search query if exists -->
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    
                    <!-- Kategori (Jika bukan di halaman detail kategori) -->
                    @if(!isset($cat))
                    <div class="mb-6">
                        <h3 class="font-semibold mb-3">Kategori</h3>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                            @foreach($filterData['categories'] as $category)
                                <div class="flex items-center">
                                    <input type="checkbox" name="kategori[]" value="{{ $category->kategori_id }}" id="cat_{{ $category->kategori_id }}"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        {{ in_array($category->kategori_id, request('kategori', [])) ? 'checked' : '' }}>
                                    <label for="cat_{{ $category->kategori_id }}" class="ml-2 text-sm text-gray-700">{{ $category->nama_kategori }}</label>
                                </div>
                                <!-- Simplifikasi: hanya level 1 untuk list, jika perlu child bisa dilooping -->
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Harga -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-3">Harga (Rp)</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span x-text="minPriceFormatted"></span>
                                <span x-text="maxPriceFormatted"></span>
                            </div>
                            
                            <!-- Range Slider (Sederhana dengan Alpine) -->
                            <div class="relative w-full h-2 bg-gray-200 rounded-full">
                                <input type="range" name="min_price" min="0" :max="maxRange" x-model="minPrice" @input="updateMin" class="absolute w-full h-2 opacity-0 cursor-pointer pointer-events-auto">
                                <input type="range" name="max_price" min="0" :max="maxRange" x-model="maxPrice" @input="updateMax" class="absolute w-full h-2 opacity-0 cursor-pointer pointer-events-auto">
                                <div class="absolute h-full bg-blue-500 rounded-full pointer-events-none" :style="`left: ${(minPrice / maxRange) * 100}%; right: ${100 - (maxPrice / maxRange) * 100}%`"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Ukuran -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-3">Ukuran</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($filterData['sizes'] as $size)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="sizes[]" value="{{ $size }}" class="peer sr-only"
                                        {{ in_array($size, request('sizes', [])) ? 'checked' : '' }}>
                                    <div class="px-3 py-1 border border-gray-300 rounded text-sm peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 hover:bg-gray-50 transition">
                                        {{ $size }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Warna -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-3">Warna</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($filterData['colors'] as $color)
                                <label class="cursor-pointer" title="{{ $color }}">
                                    <input type="checkbox" name="colors[]" value="{{ $color }}" class="peer sr-only"
                                        {{ in_array($color, request('colors', [])) ? 'checked' : '' }}>
                                    <div class="w-8 h-8 rounded-full border-2 border-gray-200 peer-checked:border-blue-600 peer-checked:ring-2 peer-checked:ring-blue-300 transition"
                                         style="background-color: {{ strtolower($color) === 'putih' ? '#ffffff' : (strtolower($color) === 'hitam' ? '#000000' : strtolower($color)) }}">
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-3">Gender</h3>
                        <div class="space-y-2">
                            @foreach(['Pria' => 'cowo', 'Wanita' => 'cewe', 'Anak' => 'kids', 'Unisex' => 'unisex'] as $label => $val)
                            <div class="flex items-center">
                                <input type="radio" name="gender" value="{{ $val }}" id="gen_{{ $val }}"
                                    class="text-blue-600 focus:ring-blue-500"
                                    {{ request('gender') === $val ? 'checked' : '' }}>
                                <label for="gen_{{ $val }}" class="ml-2 text-sm text-gray-700">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-3">Rating Minimum</h3>
                        <div class="space-y-2">
                            @foreach([4 => '≥ 4 Bintang', 3 => '≥ 3 Bintang', 2 => '≥ 2 Bintang'] as $val => $label)
                            <div class="flex items-center">
                                <input type="radio" name="rating" value="{{ $val }}" id="rat_{{ $val }}"
                                    class="text-blue-600 focus:ring-blue-500"
                                    {{ request('rating') == $val ? 'checked' : '' }}>
                                <label for="rat_{{ $val }}" class="ml-2 text-sm text-yellow-500 flex items-center gap-1">
                                    {{ $label }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition">
                        Terapkan Filter
                    </button>
                </form>
            </div>
        </aside>

        <!-- Product Grid & Sorting -->
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl font-bold text-gray-800">
                        @if(isset($cat))
                            Kategori: {{ $cat->nama_kategori }}
                        @elseif(request('q'))
                            Pencarian: "{{ request('q') }}"
                        @else
                            Semua Produk
                        @endif
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <label for="sort" class="text-sm font-medium text-gray-700 whitespace-nowrap">Urutkan:</label>
                    <select id="sort" class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500" onchange="updateSort(this.value)">
                        <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="terlaris" {{ request('sort') == 'terlaris' ? 'selected' : '' }}>Terlaris</option>
                        <option value="harga_terendah" {{ request('sort') == 'harga_terendah' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="harga_tertinggi" {{ request('sort') == 'harga_tertinggi' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="rating_tertinggi" {{ request('sort') == 'rating_tertinggi' ? 'selected' : '' }}>Rating Tertinggi</option>
                    </select>
                </div>
            </div>

            <!-- Grid -->
            @if($products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <x-product-card :produk="$product" />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-white p-12 text-center rounded-lg shadow-sm border border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Produk tidak ditemukan</h3>
                    <p class="text-gray-500">Coba ubah filter atau kata kunci pencarian Anda.</p>
                    <a href="{{ request()->url() }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800 font-medium">Reset Filter</a>
                </div>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script>
    function updateSort(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', value);
        window.location.href = url.toString();
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('priceFilter', (initMin, initMax, absoluteMax) => ({
            minPrice: initMin,
            maxPrice: initMax,
            maxRange: absoluteMax,

            get minPriceFormatted() {
                return new Intl.NumberFormat('id-ID').format(this.minPrice);
            },
            get maxPriceFormatted() {
                return new Intl.NumberFormat('id-ID').format(this.maxPrice);
            },
            
            updateMin() {
                if (parseInt(this.minPrice) > parseInt(this.maxPrice)) {
                    this.minPrice = this.maxPrice;
                }
            },
            updateMax() {
                if (parseInt(this.maxPrice) < parseInt(this.minPrice)) {
                    this.maxPrice = this.minPrice;
                }
            }
        }));
    });
</script>
@endpush
@endsection