<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MOVR') }} - Premium Sportswear</title>
    
    {{-- FONTS: Google Fonts (Inter untuk UI, Oswald untuk Headings) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    
    {{-- ICONS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- TAILWIND & CUSTOM CONFIG --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Oswald', 'sans-serif'],
                    },
                    colors: {
                        'accent-green': '#00bf8f',
                        'accent-dark': '#008f6b',
                        'dark-primary': '#111111',
                        'dark-secondary': '#1a1a1a',
                    }
                }
            }
        }
    </script>
    
    {{-- CSS CUSTOM: Scrollbar & Animations --}}
    <style>
        /* Sembunyikan scrollbar default tapi tetap bisa scroll */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #00bf8f; }
        
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col font-sans">

    {{-- ==================================================================== --}}
    {{-- NAVIGASI UTAMA (LOGIC: ADMIN vs USER)                                --}}
    {{-- ==================================================================== --}}

    @if(auth()->check() && auth()->user()->isAdmin())
        <nav class="bg-dark-primary text-white sticky top-0 z-50 border-b border-gray-800 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-8">
                        <a href="{{ route('admin.dashboard') }}" class="text-xl font-heading font-bold tracking-wider text-accent-green">
                            MOVR <span class="text-white text-xs font-sans font-normal border border-white/20 px-2 py-0.5 rounded ml-1">ADMIN</span>
                        </a>
                        <div class="hidden md:flex items-center space-x-6 text-sm font-medium text-gray-400">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-white' : '' }}">Dashboard</a>
                            <a href="{{ route('admin.produk.index') }}" class="hover:text-white transition-colors {{ request()->routeIs('admin.produk.*') ? 'text-white' : '' }}">Products</a>
                            <a href="#" class="hover:text-white transition-colors">Orders</a>
                            <a href="#" class="hover:text-white transition-colors">Users</a>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-400 hidden sm:block">Hi, {{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded text-xs font-bold uppercase tracking-wider transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

    @else
        <nav class="glass-nav sticky top-0 z-50 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20"> <div class="flex items-center gap-12">
                        <a href="{{ route('home') }}" class="text-3xl font-heading font-bold italic tracking-tighter text-black">
                            MOVR<span class="text-accent-green">.</span>
                        </a>

                        <div class="hidden md:flex items-center space-x-8">
                            <a href="{{ route('home') }}" class="text-sm font-bold uppercase tracking-widest hover:text-accent-green transition-colors {{ request()->routeIs('home') ? 'text-accent-green' : 'text-gray-900' }}">
                                Home
                            </a>
                            <a href="{{ route('produk.index') }}" class="text-sm font-bold uppercase tracking-widest hover:text-accent-green transition-colors {{ request()->routeIs('produk.*') ? 'text-accent-green' : 'text-gray-900' }}">
                                Shop
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <form action="{{ route('produk.index') }}" method="GET" class="hidden lg:block relative group">
                            @if(request('kategori'))
                                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                            @endif
                            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search..."
                                class="bg-gray-100 border-none rounded-full py-2 px-4 pl-10 pr-12 text-sm w-48 transition-all duration-300 focus:w-64 focus:ring-1 focus:ring-accent-green focus:bg-white placeholder-gray-400 text-gray-900">
                            <button type="submit" class="absolute right-3 top-1.5 text-gray-500 hover:text-gray-800 w-8 h-8 flex items-center justify-center">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>

                        @auth
                            <a href="{{ route('favorit.index') }}" class="relative text-gray-900 hover:text-accent-green transition">
                                <i class="far fa-heart text-xl"></i>
                            </a>

                            <a href="{{ route('keranjang.index') }}" class="relative text-gray-900 hover:text-accent-green transition">
                                <i class="fas fa-shopping-bag text-xl"></i>
                                @if(auth()->user()->keranjangItems()->count() > 0)
                                    <span class="absolute -top-1 -right-1.5 bg-accent-green text-white text-[10px] font-bold h-4 w-4 rounded-full flex items-center justify-center">
                                        {{ auth()->user()->keranjangItems()->count() }}
                                    </span>
                                @endif
                            </a>

                            <div class="relative group">
                                <button class="flex items-center gap-2 focus:outline-none">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold border border-transparent group-hover:border-accent-green transition">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                </button>
                                
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right z-50 border border-gray-100">
                                    <div class="px-4 py-2 border-b border-gray-100 mb-2">
                                        <p class="text-xs text-gray-400">Signed in as</p>
                                        <p class="text-sm font-bold truncate">{{ auth()->user()->name }}</p>
                                    </div>
                                    <a href="{{ route('profil.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-accent-green">My Profile</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-accent-green">Orders</a>
                                    <form action="{{ route('logout') }}" method="POST" class="mt-2 border-t border-gray-100 pt-2">
                                        @csrf
                                        <button class="w-full text-left px-4 py-2 text-sm text-blue-600 font-bold hover:bg-blue-50">Log Out</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-4">
                                <a href="{{ route('login') }}" class="text-sm font-bold hover:text-accent-green transition">Login</a>
                                <a href="{{ route('register') }}" class="bg-black text-white px-5 py-2.5 rounded-full text-sm font-bold hover:bg-accent-green hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                                    Sign Up
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    @endif

    {{-- ==================================================================== --}}
    {{-- MAIN CONTENT                                                         --}}
    {{-- ==================================================================== --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- ==================================================================== --}}
    {{-- FOOTER (MODERN & DARK)                                               --}}
    {{-- ==================================================================== --}}
    @if(!auth()->check() || !auth()->user()->isAdmin())
        <footer class="bg-dark-primary text-white pt-20 pb-10 border-t border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-16">
                    
                    {{-- BRAND COLUMN --}}
                    <div class="md:col-span-4">
                        <a href="{{ route('home') }}" class="text-4xl font-heading font-bold italic tracking-tighter text-white mb-6 block">
                            MOVR<span class="text-accent-green">.</span>
                        </a>
                        <p class="text-gray-400 leading-relaxed mb-6 max-w-sm">
                            Platform e-commerce olahraga premium. Kami mengutamakan kualitas, inovasi, dan performa untuk atlet di setiap level.
                        </p>
                        <div class="flex gap-4">
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-accent-green hover:text-white transition-all"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-accent-green hover:text-white transition-all"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-accent-green hover:text-white transition-all"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>

                    {{-- LINKS COLUMN 1 --}}
                    <div class="md:col-span-2 md:col-start-6">
                        <h4 class="text-lg font-heading font-bold uppercase tracking-wider mb-6">Shop</h4>
                        <ul class="space-y-4 text-gray-400 text-sm">
                            <li><a href="#" class="hover:text-accent-green transition">New Arrivals</a></li>
                            <li><a href="#" class="hover:text-accent-green transition">Best Sellers</a></li>
                            <li><a href="#" class="hover:text-accent-green transition">Men</a></li>
                            <li><a href="#" class="hover:text-accent-green transition">Women</a></li>
                        </ul>
                    </div>

                    {{-- LINKS COLUMN 2 --}}
                    <div class="md:col-span-2">
                        <h4 class="text-lg font-heading font-bold uppercase tracking-wider mb-6">Support</h4>
                        <ul class="space-y-4 text-gray-400 text-sm">
                            <li><a href="#" class="hover:text-accent-green transition">FAQ</a></li>
                            <li><a href="#" class="hover:text-accent-green transition">Shipping</a></li>
                            <li><a href="#" class="hover:text-accent-green transition">Returns</a></li>
                            <li><a href="#" class="hover:text-accent-green transition">Contact Us</a></li>
                        </ul>
                    </div>

                    {{-- LINKS COLUMN 3 --}}
                    <div class="md:col-span-2">
                        <h4 class="text-lg font-heading font-bold uppercase tracking-wider mb-6">Company</h4>
                        <ul class="space-y-4 text-gray-400 text-sm">
                            <li><a href="#" class="hover:text-accent-green transition">About MOVR</a></li>
                            <li><a href="#" class="hover:text-accent-green transition">Careers</a></li>
                            <li><a href="#" class="hover:text-accent-green transition">Privacy Policy</a></li>
                            <li><a href="#" class="hover:text-accent-green transition">Terms</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                    <p>&copy; 2025 MOVR Inc. All rights reserved.</p>
                    <div class="flex gap-6 mt-4 md:mt-0">
                        <span>Indonesia</span>
                        <span>English (US)</span>
                    </div>
                </div>
            </div>
        </footer>
    @endif

</body>
</html>