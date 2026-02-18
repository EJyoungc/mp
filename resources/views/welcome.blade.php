<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="MaaSMS - An innovative Maternal Health Monitoring System providing vital health insights and pregnancy tips via SMS. Developed by Techlink360 for MicroMek.">
    <meta name="keywords" content="maasms, micromek.net, techlink360.net, maternal health, pregnancy tips, SMS monitoring, Malawi healthcare, health tracking, uchembere wabwino, pregnancy education">
    <meta name="author" content="Techlink360">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://maasms.micromek.net/">
    <meta property="og:title" content="MaaSMS - Maternal Health Monitoring System">
    <meta property="og:description" content="Empowering mothers with vital health insights via tailored SMS pregnancy tips.">
    <meta property="og:image" content="{{ asset('img/1.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://maasms.micromek.net/">
    <meta property="twitter:title" content="MaaSMS - Maternal Health Monitoring System">
    <meta property="twitter:description" content="Empowering mothers with vital health insights via tailored SMS pregnancy tips.">
    <meta property="twitter:image" content="{{ asset('img/1.png') }}">

    <title>MaaSMS - Maternal Health Monitoring System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2d3748;
            overflow-x: hidden;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .hero-section {
            padding: 160px 0 100px;
            background: linear-gradient(135deg, #fdfcfb 0%, #e2d1c3 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -10%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(233, 30, 99, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .btn-primary {
            background-color: #e91e63;
            border-color: #e91e63;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
        }

        .btn-primary:hover {
            background-color: #d81b60;
            border-color: #d81b60;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(233, 30, 99, 0.4);
        }

        .btn-outline-primary {
            color: #e91e63;
            border-color: #e91e63;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-outline-primary:hover {
            background-color: #e91e63;
            color: #fff;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 40px;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50px;
            height: 3px;
            background: #e91e63;
        }

        .feature-card {
            border: none;
            border-radius: 15px;
            padding: 30px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: rgba(233, 30, 99, 0.1);
            color: #e91e63;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .abstract-shape {
            position: absolute;
            z-index: 0;
            opacity: 0.5;
        }

        .footer {
            background: #1a202c;
            color: #a0aec0;
            padding: 60px 0 30px;
        }

        .footer h5 {
            color: #fff;
            margin-bottom: 20px;
        }

        .footer a {
            color: #a0aec0;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: #e91e63;
        }

        .copyright {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 30px;
            margin-top: 30px;
            font-size: 14px;
        }

        .badge-soft {
            background: rgba(233, 30, 99, 0.1);
            color: #e91e63;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
            display: inline-block;
        }

        /* New Message Bubble Styles */
        .message-container {
            perspective: 1000px;
        }

        .message-bubble {
            background: #fff;
            border-radius: 20px;
            padding: 20px;
            position: relative;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            max-width: 400px;
            margin-left: auto;
            transform-origin: bottom right;
            animation: popup 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            border: 1px solid rgba(233, 30, 99, 0.1);
        }

        .message-bubble::after {
            content: '';
            position: absolute;
            bottom: -10px;
            right: 30px;
            width: 20px;
            height: 20px;
            background: #fff;
            transform: rotate(45deg);
            border-bottom: 1px solid rgba(233, 30, 99, 0.1);
            border-right: 1px solid rgba(233, 30, 99, 0.1);
        }

        @keyframes popup {
            0% { transform: scale(0.5) translateY(50px); opacity: 0; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }

        .message-content {
            transition: opacity 0.4s ease;
        }

        .message-content.fade-out {
            opacity: 0;
        }

        /* Partners Styles */
        .partner-logo {
            max-width: 180px;
            height: auto;
            filter: grayscale(100%);
            opacity: 0.6;
            transition: all 0.3s ease;
        }

        .partner-logo:hover {
            filter: grayscale(0%);
            opacity: 1;
            transform: scale(1.05);
        }
    </style>
</head>
<body class="layout-top-nav">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-light fixed-top">
        <div class="container">
            <a href="/" class="navbar-brand">
                <span class="brand-text font-weight-bold" style="color: #e91e63; font-size: 24px;">MaaSMS</span>
            </a>

            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                <ul class="navbar-nav ml-auto">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link">Log in</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm ml-md-3">Get Started</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <span class="badge-soft">Innovative Maternal Health</span>
                    <h1 class="display-4 font-weight-bold mb-4">Empowering Mothers with Vital Health Insights</h1>
                    <p class="lead mb-5 text-muted">MaaSMS is an advanced monitoring system providing tailored pregnancy tips via SMS, ensuring every mother has the knowledge for a healthy pregnancy journey.</p>
                    <div class="d-flex flex-wrap">
                        <a href="{{ route('login') }}" class="btn btn-primary mr-3 mb-3">Launch Platform</a>
                        <a href="#features" class="btn btn-outline-primary mb-3">Explore Features</a>
                    </div>
                </div>
                <div class="col-lg-6 col-12 col-md-12  d-lg-block position-relative">
                    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="abstract-shape" style="width: 100%; height: auto;">
                        <path fill="#E91E63" d="M44.7,-76.4C58.1,-69.2,69.2,-57.1,76.4,-43.3C83.6,-29.5,86.9,-14.7,85.2,-0.9C83.6,12.8,77,25.7,69.1,38.2C61.2,50.7,52,62.8,40,70.9C28,79.1,14,83.2,-0.7,84.4C-15.3,85.6,-30.7,83.8,-43.6,76.5C-56.5,69.2,-67,56.4,-74.6,42.4C-82.2,28.4,-86.9,14.2,-86.1,0.5C-85.2,-13.2,-78.8,-26.4,-70.5,-39.1C-62.2,-51.8,-52.1,-64,-39.7,-71.8C-27.4,-79.6,-13.7,-83.1,0.6,-84.2C14.9,-85.2,29.9,-83.8,44.7,-76.4Z" transform="translate(100 100)" opacity="0.1" />
                    </svg>

                    <div class="message-container">
                        <div class="message-bubble" id="tip-bubble">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-pink rounded-circle d-flex align-items-center justify-center mr-2" style="width: 30px; height: 30px; background: #e91e63;">
                                    {{-- <i class="fas fa-user text-white small"></i> --}}
                                </div>
                                <span class="font-weight-bold small" style="color: #e91e63;">MaaSMS Health Tip</span>
                                <span class="ml-auto text-muted tiny" style="font-size: 10px;">Just now</span>
                            </div>
                            <div class="message-content" id="message-text">
                                "Dear Mary, you are now in week 24. Remember to stay hydrated and include more iron-rich foods in your diet today."
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="section-title">Comprehensive Care Management</h2>
                <p class="text-muted">A robust ecosystem designed for health providers and patients.</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-sms"></i>
                        </div>
                        <h4>Automated SMS Tips</h4>
                        <p class="text-muted">Personalized pregnancy education sent directly to mobile phones based on the specific week and day of pregnancy.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-hospital-user"></i>
                        </div>
                        <h4>Role-Based Portals</h4>
                        <p class="text-muted">Dedicated access for Doctors, Practitioners, and Admins to manage patient records and health trends effectively.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Health Analytics</h4>
                        <p class="text-muted">Track pregnancy progress, calculate trimesters, and monitor the health status of a community through detailed dashboards.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Impact Section -->
    <div class="py-5" style="background: #f8f9fa;">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="{{ asset('img/1.png') }}" alt="Healthcare Professionals" class="img-fluid rounded shadow-lg" style="border-radius: 20px;">
                </div>
                <div class="col-lg-6 pl-lg-5">
                    <h2 class="section-title">Bridging the Gap in Maternal Care</h2>
                    <p class="mb-4">Using the Africa's Talking SMS Gateway, MaaSMS reaches mothers even in remote areas, providing them with evidence-based health tips that make a real difference.</p>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-check-circle text-pink mr-2" style="color: #e91e63;"></i> Accurate trimester calculations</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-pink mr-2" style="color: #e91e63;"></i> Automated weekly check-ins</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-pink mr-2" style="color: #e91e63;"></i> Secure organization verification</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-pink mr-2" style="color: #e91e63;"></i> Data-driven healthcare insights</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Partners Section -->
    <div class="py-5 bg-white border-top border-bottom">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title text-center">Our Partners</h2>
                    <p class="mb-4"> Tribute and thank to our strategic partners in the Region </p>
            </div>

            <div class="text-center mb-4">
                {{-- <p class="text-uppercase small font-weight-bold text-muted tracking-widest">Our Strategic Partners</p> --}}
            </div>
            <div class="row align-items-center justify-content-center text-center">
                <div class="col-6 col-md-3 mb-4 mb-md-0">
                    <a href="https://micromek.net" target="_blank" class="d-block">
                        <span class="h2 font-weight-bold  text-muted partner-logo">MicroMek</span>
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-4 mb-md-0">
                    <a href="#" class="d-block">
                        <span class="h2 font-weight-bold   text-muted partner-logo">Uchembere Wabwino</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-5 text-white" style="background: linear-gradient(135deg, #e91e63 0%, #9c27b0 100%);">
        <div class="container py-5 text-center">
            <h2 class="font-weight-bold mb-4">Ready to improve maternal outcomes?</h2>
            <p class="lead mb-5 opacity-75">Join healthcare providers using MaaSMS to transform maternal health delivery.</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 font-weight-bold" style="border-radius: 8px; color: #e91e63;">Join the Network</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="font-weight-bold" style="color: #e91e63;">MaaSMS</h5>
                    <p>A comprehensive maternal health monitoring system designed to provide vital health information to expectant mothers via accessible mobile technology.</p>
                    <div class="social-links mt-3">
                        <a href="#" class="mr-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="mr-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="mr-3"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 mb-4">
                    <h5>Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 mb-4">
                    <h5>Platform</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">For Doctors</a></li>
                        <li><a href="#">For Mothers</a></li>
                        <li><a href="#">For Organizations</a></li>
                        <li><a href="#">SMS Services</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 mb-4">
                    <h5>Developed By</h5>
                    <p class="mb-1">Designed & Developed by</p>
                    <a href="https://techlink360.net" target="_blank" class="font-weight-bold" style="color: #e91e63">Techlink360</a>
                </div>
            </div>
            <div class="row copyright">
                <div class="col-md-12 text-center">
                    <p>&copy; {{ date('Y') }} MaaSMS. All rights reserved for <a href="https://micromek.net" target="_blank" style="color: #e91e63;">MicroMek</a></p>
                </div>
            </div>
        </div>
    </footer>

</div>

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
<script>
    $(window).scroll(function() {
        if ($(this).scrollTop() > 50) {
            $('.navbar').addClass('shadow-sm');
        } else {
            $('.navbar').removeClass('shadow-sm');
        }
    });

    // Message Cycling Logic
    const messages = [
        "Dear Mary, you are now in week 24. Remember to stay hydrated and include more iron-rich foods in your diet today.",
        "Hi Sarah, it's week 12! Time for your first trimester screening. Please visit the clinic this week.",
        "Hello Grace, week 36 is here. Keep your hospital bag ready and track the baby's movements hourly.",
        "Dear Mother, week 18 is a great time to start talking to your baby. They can now hear your voice!",
        "Important: Week 28 check-up is due. Have you checked your blood pressure this morning?"
    ];

    let currentMsgIndex = 0;
    const messageText = document.getElementById('message-text');
    const bubble = document.getElementById('tip-bubble');

    function cycleMessage() {
        // Trigger exit animation by removing and re-adding the bubble or using classes
        bubble.style.animation = 'none';
        bubble.offsetHeight; /* trigger reflow */

        messageText.classList.add('fade-out');

        setTimeout(() => {
            currentMsgIndex = (currentMsgIndex + 1) % messages.length;
            messageText.innerText = `"${messages[currentMsgIndex]}"`;
            messageText.classList.remove('fade-out');
            bubble.style.animation = 'popup 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards';
        }, 400);
    }

    // Change message every 5 seconds
    setInterval(cycleMessage, 5000);
</script>
</body>
</html>
