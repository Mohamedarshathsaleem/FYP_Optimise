<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optimise</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">
    @stack('scripts')
    <style>
        
        /* GLOBAL FONT SIZE ADJUSTMENT - DIPERKECIL */
        html {
            font-size: 14px; /* Default biasanya 16px, diperkecil jadi 14px */
        }

        /* Alternative: gunakan scale untuk proportional scaling */
        body {
            font-size: 0.875rem; /* 14px jika base 16px */
            line-height: 1.4; /* Sedikit diperkecil dari default 1.5 */
        }

        /* Mendefinisikan warna kustom & font utama */
        :root {
            --primary-dark: #043873;
            --primary-light: #4F9CF9;
            --accent-yellow: #FFE492;
            --text-dark: #212529;
            --text-light: #6C757D;
            --text-selected: #0080ffff;
            --light-bg: #F8F9FA;
            --transition: all 0.3s ease;
        }
        
        .search-box {
            width: 260px;
            min-width: 220px;
        }
        
        .search-box .input-group-text,
        .search-box .form-control {
            border-color: #dee2e6;
            box-shadow: none;
        }
        
        .search-box .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
        
        @media (max-width: 768px) {
            .search-box {
                width: 180px;
                min-width: 180px;
            }
        }

        /* Custom Navbar and Breadcrumb Styles */
        .navbar-brand {
            font-weight: 700;
            color: var(--text-dark);
        }
        .navbar-brand:hover {
            color: var(--primary-light);
        }
        .nav-link {
            font-weight: 500;
            color: var(--text-dark);
            transition: var(--transition);
        }
        .nav-link:hover {
            color: var(--primary-light);
        }
        .nav-link.active {
            color: var(--text-selected) !important;
        }
        .breadcrumb-section {
            background: var(--light-bg);
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .breadcrumb-item a {
            color: var(--text-light);
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: var(--primary-dark);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F9FAFB;
        }

        .bg-primary-dark {
            background-color: var(--primary-dark);
        }

        .text-primary-dark {
            color: var(--primary-dark);
        }

        .header-with-bg {
            background: linear-gradient(rgba(4, 56, 115, 0.8), rgba(4, 56, 115, 0.8)), url('https://images.unsplash.com/photo-1554497342-99bb59095d34?q=80&w=2070');
            background-size: cover;
            background-position: center;
        }

        /* OVERRIDE BOOTSTRAP FONT SIZES - DIPERKECIL */
        .display-1 { font-size: 4.5rem; } /* Bootstrap default: 5rem */
        .display-2 { font-size: 3.5rem; } /* Bootstrap default: 4.5rem */
        .display-3 { font-size: 2.8rem; } /* Bootstrap default: 3.5rem */
        .display-4 { font-size: 2.2rem; } /* Bootstrap default: 2.5rem */

        h1, .h1 { font-size: 2rem; } /* Bootstrap default: 2.5rem */
        h2, .h2 { font-size: 1.7rem; } /* Bootstrap default: 2rem */
        h3, .h3 { font-size: 1.4rem; } /* Bootstrap default: 1.75rem */
        h4, .h4 { font-size: 1.2rem; } /* Bootstrap default: 1.5rem */
        h5, .h5 { font-size: 1rem; } /* Bootstrap default: 1.25rem */
        h6, .h6 { font-size: 0.9rem; } /* Bootstrap default: 1rem */

        .lead {
            font-size: 1.1rem; /* Bootstrap default: 1.25rem */
            font-weight: 400;
        }

        .fs-1 { font-size: 2rem !important; }
        .fs-2 { font-size: 1.7rem !important; }
        .fs-3 { font-size: 1.4rem !important; }
        .fs-4 { font-size: 1.2rem !important; }
        .fs-5 { font-size: 1rem !important; }
        .fs-6 { font-size: 0.9rem !important; }

        /* NAVBAR FONT SIZE - DIPERKECIL */
        .navbar-brand {
            font-size: 1.1rem !important; /* Bootstrap default: 1.25rem */
        }

        .nav-link {
            font-size: 0.9rem; /* Bootstrap default: 1rem */
        }

        /* BUTTON FONT SIZE - DIPERKECIL */
        .btn {
            font-size: 0.8rem; /* Bootstrap default: 1rem */
            padding: 0.375rem 0.75rem; /* Sedikit diperkecil */
        }

        .btn-lg {
            font-size: 1rem; /* Bootstrap default: 1.25rem */
            padding: 0.5rem 1rem; /* Bootstrap default: 0.5rem 1rem */
        }

        .btn-sm {
            font-size: 0.75rem; /* Bootstrap default: 0.875rem */
            padding: 0.25rem 0.5rem;
        }

        /* DROPDOWN FONT SIZE - DIPERKECIL */
        .dropdown-item {
            font-size: 0.85rem; /* Bootstrap default: 1rem */
        }

        /* FOOTER FONT SIZE - DIPERKECIL */
        footer h5 {
            font-size: 1.1rem; /* Diperkecil dari default */
        }

        footer .text-white-50,
        footer p {
            font-size: 0.85rem; /* Diperkecil */
        }

        /* Kustomisasi Tombol */
        .btn-primary-light {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
            color: white;
        }

        .btn-primary-light:hover {
            background-color: #3b8cdf;
            border-color: #3b8cdf;
            color: white;
        }

        .btn-accent {
            background-color: var(--accent-yellow);
            border-color: var(--accent-yellow);
            color: var(--primary-dark);
            font-weight: 700;
        }

        .btn-accent:hover {
            background-color: #f0d17a;
            border-color: #f0d17a;
            color: var(--primary-dark);
        }

        /* RESPONSIVE FONT SIZE ADJUSTMENTS */
        @media (max-width: 768px) {
            html {
                font-size: 13px; /* Lebih kecil lagi di mobile */
            }

            .display-3 {
                font-size: 2.2rem; /* Lebih kecil di mobile */
            }

            .display-4 {
                font-size: 1.8rem;
            }

            .navbar-brand {
                font-size: 1rem !important;
            }
        }

        @media (max-width: 576px) {
            html {
                font-size: 12px; /* Paling kecil di mobile kecil */
            }

            .display-3 {
                font-size: 2rem;
            }
        }

        /* BREADCRUMB FONT SIZE - DIPERKECIL */
        .small {
            font-size: 0.8rem; /* Bootstrap default: 0.875rem */
        }

        /* CARD FONT SIZE - DIPERKECIL */
        .card-title {
            font-size: 1.1rem; /* Bootstrap default: 1.25rem */
        }

        .card-text {
            font-size: 0.9rem; /* Bootstrap default: 1rem */
        }
    </style>
</head>
<body>
    <header class="bg-primary-light">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">Optimise</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('features') ? 'active' : '' }}" href="{{ url('/features') }}">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('solutions') ? 'active' : '' }}" href="{{ url('/solution') }}">Solutions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('pricing') ? 'active' : '' }}" href="{{ url('/pricing') }}">Pricing</a>
                        </li>
                    </ul>
                    @if (session('dummy_user_is_logged_in'))
                    <div class="dropdown">
                        <a href="#" class="btn btn-accent d-flex align-items-center dropdown-toggle px-3" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i>
                            User
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ url('/dashboard') }}">Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ url('/settings')}}">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ url('/logout') }}">Logout</a></li>
                        </ul>
                    </div>
                    @else
                    <a href="{{ url('/login') }}" class="btn btn-accent px-4">Login</a>
                    @endif
                </div>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-primary-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row justify-content-between gy-5">
                <div class="col-lg-3 col-md-12">
                    <h5 class="fw-bold text-white">Optimise</h5>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="fw-bold text-white mb-3">Product</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Overview</a></li>
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Pricing</a></li>
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Customer stories</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5 class="fw-bold text-white mb-3">Resources</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Blog</a></li>
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Guides & tutorials</a></li>
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Help center</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5 class="fw-bold text-white mb-3">Company</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">About us</a></li>
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Careers</a></li>
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Media kit</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-12">
                     <h5 class="fw-bold text-white mb-3">Try It Today</h5>
                     <p class="text-white-50">Get started for free. Add your whole team as your needs grow.</p>
                     <a href="#" class="btn btn-primary-light">Start today <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>

            <hr class="my-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                 </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
