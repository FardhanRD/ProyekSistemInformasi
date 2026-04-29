<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOVR - Premium Sports</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            color: #333;
        }

        /* Floating Navigation */
        header {
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 100;
            display: flex;
            gap: 40px;
            align-items: center;
            padding: 16px 40px;
            background: white;
            border-bottom: 1px solid #f0f0f0;
        }

        .logo {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .logo img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        nav {
            display: flex;
            gap: 40px;
            align-items: center;
            flex: 1;
            justify-content: center;
        }

        nav a {
            color: #111;
            text-decoration: none;
            font-weight: 400;
            font-size: 0.9rem;
            transition: color 0.3s ease;
            letter-spacing: 0.3px;
            position: relative;
            padding-bottom: 4px;
        }

        nav a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: #9B2226;
            transition: width 0.3s ease;
        }

        nav a:hover::after,
        nav a.active::after {
            width: 100%;
        }

        nav a:hover {
            color: #9B2226;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            padding: 8px 14px;
            flex-shrink: 0;
        }

        .search-box input {
            background: transparent;
            border: none;
            outline: none;
            color: #111;
            font-family: inherit;
            width: 120px;
            font-size: 0.85rem;
        }

        .search-box input::placeholder {
            color: #999;
        }

        .search-icon {
            color: #999;
            font-size: 0.95rem;
            cursor: pointer;
        }

        .auth-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-shrink: 0;
        }

        .login-white {
            background: white;
            color: #111;
            border: 1px solid #ccc;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-white:hover {
            background: #f5f5f5;
        }

        .login-red {
            background: #9B2226;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-red:hover {
            background: #7a1a1d;
        }

        .login-white:hover {
            background: #f0f0f0;
        }

        .login-red {
            background-color: #9B2226;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-red:hover {
            background-color: #7a1a1e;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #2d5f5f 0%, #3a7070 100%);
            min-height: 100vh;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            padding: 80px 40px 40px;
            position: relative;
            overflow: hidden;
        }

        .hero-video {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.85;
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 50%;
        }

        .avatar-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .avatars {
            display: flex;
            align-items: center;
        }

        .avatars img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
            margin-left: -15px;
        }

        .avatars img:first-child {
            margin-left: 0;
        }

        .avatar-text {
            color: rgba(255, 255, 255, 0.92);
            font-size: 0.92rem;
            line-height: 1.5;
        }

        .hero-title {
            font-size: 5.8rem;
            font-weight: 900;
            line-height: 0.92;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .hero-title .sports {
            color: #9B2226;
            display: block;
        }

        .hero-title .passion {
            color: white;
            display: block;
            text-shadow: 
                -1px -1px 0 rgba(255, 255, 255, 0.4),
                1px -1px 0 rgba(255, 255, 255, 0.4),
                -1px 1px 0 rgba(255, 255, 255, 0.4),
                1px 1px 0 rgba(255, 255, 255, 0.4),
                -2px 0 0 rgba(255, 255, 255, 0.3),
                2px 0 0 rgba(255, 255, 255, 0.3),
                0 -2px 0 rgba(255, 255, 255, 0.3),
                0 2px 0 rgba(255, 255, 255, 0.3);
        }

        /* Limited Slots Card */
        .limited-card {
            position: absolute;
            right: 40px;
            top: 140px;
            width: 280px;
            background: white;
            border-radius: 20px;
            padding: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            z-index: 3;
        }

        .limited-card img {
            width: 100%;
            height: 150px;
            border-radius: 15px;
            object-fit: cover;
        }

        .limited-card h3 {
            font-size: 1rem;
            font-weight: 900;
            margin-top: 12px;
            color: #111;
            letter-spacing: 0.5px;
        }

        .limited-card p {
            font-size: 0.8rem;
            color: #666;
            margin-top: 8px;
            line-height: 1.4;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            header {
                padding: 12px 30px;
                gap: 30px;
            }

            nav {
                gap: 30px;
                font-size: 0.85rem;
            }

            .search-box {
                width: 100px;
            }

            .search-box input {
                width: 60px;
            }

            .hero {
                padding: 80px 30px 40px;
                flex-direction: column;
                text-align: center;
                align-items: flex-start;
            }

            .hero-content {
                max-width: 100%;
            }

            .hero-video {
                width: 100%;
                opacity: 0.3;
            }

            .hero-title {
                font-size: 4rem;
            }

            .limited-card {
                position: static;
                margin-top: 30px;
                width: 100%;
                max-width: 400px;
            }

            .avatar-section {
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            header {
                flex-wrap: wrap;
                padding: 12px 20px;
                gap: 12px;
            }

            nav {
                gap: 20px;
                font-size: 0.8rem;
                order: 3;
                width: 100%;
                margin-top: 8px;
                justify-content: center;
            }

            .auth-buttons {
                order: 2;
                gap: 6px;
            }

            .search-box {
                display: none;
            }

            .login-white,
            .login-red {
                padding: 8px 12px;
                font-size: 0.75rem;
            }

            .hero {
                padding: 90px 20px 40px;
                min-height: 80vh;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .limited-card {
                width: 100%;
                max-width: 350px;
                margin: 20px auto 0;
            }

            .avatar-section {
                justify-content: flex-start;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <img src="{{ asset('images/LOGO_MOVR.png') }}" alt="MOVR Logo">
        </div>
        <nav>
            <a href="#home">Home</a>
            <a href="#shop">Shop</a>
            <a href="#about">About</a>
            <a href="#sale">Sale</a>
            <a href="#new">New</a>
        </nav>
        <div class="search-box">
            <input type="text" placeholder="Search...">
            <span class="search-icon">🔍</span>
        </div>
        <div class="auth-buttons">
            <button class="login-white">Login</button>
            <button class="login-red">Sign up</button>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <video class="hero-video" autoplay muted loop playsinline preload="metadata">
            <source src="{{ asset('videos/hero-bg.mp4') }}" type="video/mp4">
        </video>
        <div class="hero-content">
            <div class="avatar-section">
                <div class="avatars">
                    <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80" alt="Avatar 1">
                    <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80" alt="Avatar 2">
                    <img src="https://images.unsplash.com/photo-1521119989659-a83eee488004?auto=format&fit=crop&w=200&q=80" alt="Avatar 3">
                </div>
                <p class="avatar-text">Our intelligent training tools, event updates, and community-driven stories bring together athletes, teams, and fans to push performance beyond limits.</p>
            </div>

            <h1 class="hero-title">
                <span class="sports">SPORTS</span>
                <span class="passion">PASSION</span>
            </h1>
        </div>

        <div class="limited-card">
            <img src="https://images.unsplash.com/photo-1612531385446-f7b8f0f14933?auto=format&fit=crop&w=1000&q=80" alt="Badminton racket">
            <h3>LIMITED SLOTS AVAILABLE</h3>
            <p>Our online training tools, event updates community driven stories connect</p>
        </div>
    </section>
</body>
</html>
