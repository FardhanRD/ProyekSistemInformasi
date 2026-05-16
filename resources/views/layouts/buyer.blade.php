<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MOVR')</title>
    {{-- Fonts and Tailwind (CDN fallback so layout looks correct without building assets) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="bg-[#F8F9FA] min-h-screen font-sans text-slate-900">

    @include('components.header')

    <main class="container mx-auto px-4 py-6">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 mt-12">
        <div class="container mx-auto px-4 py-6 text-sm text-gray-600">
            © {{ date('Y') }} MOVR. All rights reserved.
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
