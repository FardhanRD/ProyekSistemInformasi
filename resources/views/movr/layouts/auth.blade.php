<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MOVR') }} - Akun</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Archivo+Black&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Manrope', 'sans-serif'],
                        heading: ['Archivo Black', 'sans-serif'],
                    },
                    colors: {
                        'accent-primary': '#9B2226',
                        'accent-dark': '#6f171b',
                        'dark-primary': '#111111',
                        'dark-secondary': '#1a1a1a',
                    }
                }
            }
        }
    </script>

    <style>
        .auth-bg {
            background-image: linear-gradient(rgba(0, 0, 0, 0.08), rgba(0, 0, 0, 0.08)), url('{{ asset('images/auth-bg.png') }}');
            background-size: cover;
            background-position: center;
        }

        .frost {
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen auth-bg text-gray-900 antialiased font-sans">
    <div class="relative min-h-screen overflow-hidden">
        <div class="relative z-10 min-h-screen flex items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </div>
</body>
</html>