@extends('movr.layouts.app')

@section('content')

    {{-- BREADCRUMB --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <nav class="flex text-xs font-bold uppercase tracking-wider text-gray-500">
                <ol class="inline-flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="hover:text-black transition">Home</a></li>
                    <li><span class="text-gray-300">/</span></li>
                    <li class="text-black">Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6">
            
            {{-- HEADER SECTION --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-10">
                <div>
                    <h1 class="text-3xl font-bold text-black mb-1">Halo, {{ Auth::user()->name }} 👋</h1>
                    <p class="text-gray-500 text-sm">Selamat datang kembali di dashboard akun Anda.</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('produk.index') }}" class="inline-flex items-center px-6 py-3 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-lg hover:bg-gray-800 transition shadow-lg shadow-gray-200">
                        <i class="fas fa-shopping-bag mr-2"></i> Belanja Lagi
                    </a>
                </div>
            </div>

            {{-- STATS GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                {{-- Card 1: Total Orders --}}
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl mr-4">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Pesanan</p>
                        <p class="text-2xl font-bold text-black">{{ $totalOrders ?? 0 }}</p>
                    </div>
                </div>
                {{-- Card 2: Pending --}}
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl mr-4">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Dalam Proses</p>
                        <p class="text-2xl font-bold text-black">{{ $pendingOrders ?? 0 }}</p>
                    </div>
                </div>
                {{-- Card 3: Completed --}}
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-xl mr-4">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Selesai</p>
                        <p class="text-2xl font-bold text-black">{{ $completedOrders ?? 0 }}</p>
                    </div>
                </div>
                {{-- Card 4: Total Spent --}}
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-xl mr-4">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Belanja</p>
                        <p class="text-2xl font-bold text-black">Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- MAIN CONTENT GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- LEFT COLUMN: RECENT ORDERS (Table) --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
                            <h2 class="font-bold text-black text-lg">Pesanan Terbaru</h2>
                        </div>

                        @if(isset($orders) && $orders->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-100 text-[10px] uppercase font-bold text-gray-500 tracking-wider">
                                            <th class="px-6 py-4">ID Pesanan</th>
                                            <th class="px-6 py-4">Tanggal</th>
                                            <th class="px-6 py-4">Status</th>
                                            <th class="px-6 py-4">Total</th>
                                            <th class="px-6 py-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($orders as $order)
                                            <tr class="hover:bg-gray-50/50 transition duration-150">
                                                <td class="px-6 py-4">
                                                    <span class="font-bold text-black text-sm">#{{ $order->midtrans_order_id ?? $order->id }}</span>
                                                    <p class="text-xs text-gray-400 mt-0.5">{{ $order->items->count() }} Barang</p>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                                    {{ $order->created_at->format('d M Y') }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    @php
                                                        $statusClasses = [
                                                            'paid' => 'bg-green-100 text-green-700 border-green-200',
                                                            'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                                            'cancelled' => 'bg-blue-100 text-blue-700 border-blue-200',
                                                            'processing' => 'bg-blue-100 text-blue-700 border-blue-200',
                                                            'shipped' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                                            'delivered' => 'bg-purple-100 text-purple-700 border-purple-200',
                                                        ];
                                                        $class = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold border {{ $class }} uppercase tracking-wide">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 font-bold text-black text-sm">
                                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <a href="{{ route('customer.order.show', $order->id) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-full bg-gray-50 hover:bg-black hover:text-white transition text-gray-400">
                                                        <i class="fas fa-arrow-right text-xs"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-16 bg-white">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <i class="fas fa-box-open text-2xl"></i>
                                </div>
                                <h3 class="font-bold text-black">Belum ada pesanan</h3>
                                <p class="text-gray-500 text-sm mt-1 mb-6">Pesanan Anda akan muncul di sini.</p>
                                <a href="{{ route('produk.index') }}" class="text-xs font-bold text-black border-b border-black pb-0.5 hover:text-gray-600 hover:border-gray-600 transition">
                                    Mulai Belanja Sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- RIGHT COLUMN: ACCOUNT SUMMARY --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Profile Card --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                            <h3 class="font-bold text-black text-xs uppercase tracking-wider">Akun Saya</h3>
                            <a href="{{ route('profil.index') }}" class="text-gray-400 hover:text-black transition">
                                <i class="fas fa-cog"></i>
                            </a>
                        </div>
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center font-bold mr-3 text-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="font-bold text-black truncate text-sm">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <a href="{{ route('profil.index') }}" class="block w-full text-center py-2.5 border border-gray-200 rounded-lg text-xs font-bold uppercase hover:bg-black hover:text-white transition duration-200">
                            Edit Profil
                        </a>
                    </div>

                    {{-- Address Card (FIXED ERROR 500) --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <h3 class="font-bold text-black text-xs uppercase tracking-wider mb-4 border-b border-gray-100 pb-3">Alamat Utama</h3>
                        
                        @php
                            // FIX: Cek null safety sebelum filter alamat
                            $alamatUser = Auth::user()->alamat; 
                            $defaultAddress = null;

                            // Cek apakah relasi ada DAN jumlahnya lebih dari 0
                            if ($alamatUser && $alamatUser->count() > 0) {
                                $defaultAddress = $alamatUser->where('is_default', 1)->first() ?? $alamatUser->first();
                            }
                        @endphp

                        @if($defaultAddress)
                            <div class="mb-4">
                                <span class="text-[10px] font-bold bg-gray-100 px-2 py-1 rounded text-gray-600 mb-2 inline-block">{{ $defaultAddress->label }}</span>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    {{ $defaultAddress->detail_alamat }}<br>
                                    {{ $defaultAddress->kecamatan }}, {{ $defaultAddress->kota }}<br>
                                    {{ $defaultAddress->provinsi }} {{ $defaultAddress->kode_pos }}
                                </p>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-4 text-center mb-4 border border-dashed border-gray-200">
                                <p class="text-xs text-gray-400 italic">Belum ada alamat tersimpan.</p>
                            </div>
                        @endif
                        
                        <a href="{{ route('profil.index') }}#address" class="text-xs font-bold text-black hover:text-gray-600 underline decoration-gray-300 underline-offset-4 decoration-2">
                            Kelola Alamat
                        </a>
                    </div>

                    {{-- Help Card --}}
                    <div class="bg-black rounded-xl shadow-lg p-6 text-white relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="font-bold text-base mb-1">Butuh Bantuan?</h3>
                            <p class="text-gray-400 text-xs mb-4 max-w-[150px]">Tim support kami siap membantu kendala pesanan Anda.</p>
                            <a href="#" class="inline-block bg-white text-black text-[10px] font-bold px-4 py-2 rounded-lg hover:bg-gray-200 transition uppercase tracking-wider">
                                Hubungi Kami
                            </a>
                        </div>
                        {{-- Decor Icon --}}
                        <div class="absolute -bottom-4 -right-4 text-gray-800 opacity-20 transform group-hover:scale-110 transition-transform duration-500">
                            <i class="fas fa-headset text-8xl"></i>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection