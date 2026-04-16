<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Optimise')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #043873;
            --primary-light: #4F9CF9;
            --accent-yellow: #FFE492;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F9FAFB;
        }
        .bg-primary-dark { background-color: var(--primary-dark); }
        .text-primary-dark { color: var(--primary-dark); }
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
        /* Style baru untuk Nav Tabs di halaman Settings */
        .settings-tabs .nav-link {
            color: black;
            font-weight: 500;
            border-bottom: 3px solid transparent; /* Garis bawah transparan */
            margin-bottom: -1px; /* Rapatkan dengan konten */
        }
        .settings-tabs .nav-link.active {
            color: var(--primary-light) !important;
            font-weight: 700;
            border-bottom-color: var(--primary-light); /* Garis bawah biru saat aktif */
        }
    </style>
</head>
<body>
    <header class="bg-primary-dark sticky-top">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand fs-4 fw-bold" href="{{ url('/') }}">Optimise</a>
                <div class="ms-auto">
                    @if (session('dummy_user_is_logged_in'))
                        <div class="dropdown">
                            <a href="#" class="btn btn-accent d-flex align-items-center dropdown-toggle px-3" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-2"></i> User
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Profile</a></li>
                                <li><a class="dropdown-item" href="{{ url('/settings') }}">Settings</a></li>
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
                <div class="col-lg-3 col-md-12"><h5 class="fw-bold text-white fs-4">Optimise</h5></div>
                <div class="col-lg-6 col-md-12"><div class="row"><div class="col-md-4"><h5 class="fw-bold text-white mb-3">Product</h5><ul class="list-unstyled"><li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Overview</a></li><li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Pricing</a></li><li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Customer stories</a></li></ul></div><div class="col-md-4"><h5 class="fw-bold text-white mb-3">Resources</h5><ul class="list-unstyled"><li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Blog</a></li><li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Guides & tutorials</a></li><li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Help center</a></li></ul></div><div class="col-md-4"><h5 class="fw-bold text-white mb-3">Company</h5><ul class="list-unstyled"><li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">About us</a></li><li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Careers</a></li><li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Media kit</a></li></ul></div></div></div>
                <div class="col-lg-3 col-md-12"><h5 class="fw-bold text-white mb-3">Try It Today</h5><p class="text-white-50">Get started for free. Add your whole team as your needs grow.</p><a href="#" class="btn btn-primary-light">Start today <i class="bi bi-arrow-right"></i></a></div>
            </div>
            <hr class="my-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center"></div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
