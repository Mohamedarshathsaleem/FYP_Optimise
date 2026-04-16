<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Optimise</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #043873;
            --primary-light: #3965FF;
            --accent-yellow: #FFE492;
            --sidebar-bg: #FFFFFF;
            --main-bg: #F4F7FE;
            --primary-green: #05CD99;
            --active-menu-bg: #E9EFFF;
        }

        html {
            font-size: 15px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--main-bg);
            font-size: 0.93rem;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }

        h1, .h1 { font-size: 2rem; }
        h2, .h2 { font-size: 1.6rem; }
        h3, .h3 { font-size: 1.4rem; }
        h4, .h4 { font-size: 1.2rem; }
        h5, .h5 { font-size: 1.1rem; }
        h6, .h6 { font-size: 0.95rem; }

        .small { font-size: 0.8rem; }
        .text-secondary { font-size: 0.87rem; }

        .card-title { font-size: 1.1rem; font-weight: 600; }
        .card-body { font-size: 0.9rem; }
        .card-body li { font-size: 0.87rem; line-height: 1.5; margin-bottom: 0.4rem; }

        .table { font-size: 0.87rem; }
        .table td, .table th { padding: 0.65rem; font-size: 0.8rem; }

        .btn {
            font-size: 0.8rem;
            padding: 0.5rem 0.9rem;
            border-radius: 12px !important;
        }
        .btn-sm { font-size: 0.75rem; padding: 0.35rem 0.7rem; }

        .form-label { font-size: 0.87rem; font-weight: 500; }
        .form-control, .form-select {
            font-size: 0.87rem;
            padding: 0.55rem 0.8rem;
            background-color: #F5F6FA;
        }

        .modal-title { font-size: 1.25rem; }
        .modal-body { font-size: 0.87rem; }

        .input-group-text { font-size: 0.87rem; }
        .search-box .form-control { font-size: 0.87rem; }

        .fw-bold { font-weight: 600; }
        .fw-semibold { font-weight: 500; }

        .wrapper {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 280px;
            background-color: var(--sidebar-bg);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            border-right: 1px solid #e9ecef;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1040;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-collapsed .sidebar {
            width: 70px;
            padding: 1rem 0.5rem;
        }

        /* ===== TOGGLE BUTTON ===== */
        .toggle-btn {
            position: fixed !important;
            top: 20px !important;
            left: 250px !important;
            z-index: 1060 !important;
            background: linear-gradient(135deg, #868CFF 0%, #4318FF 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 50% !important;
            width: 42px !important;
            height: 42px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            box-shadow: 0 4px 12px rgba(67, 24, 255, 0.4) !important;
            transition: all 0.3s ease !important;
        }

        .modal-open .toggle-btn { display: none !important; }
        .modal.show~.toggle-btn,
        .modal-backdrop~.toggle-btn {
            z-index: 1040 !important;
        }

        .toggle-btn:hover {
            background: linear-gradient(135deg, #4318FF 0%, #868CFF 100%) !important;
            transform: scale(1.05) !important;
            box-shadow: 0 6px 20px rgba(67, 24, 255, 0.5) !important;
        }

        .sidebar-collapsed .toggle-btn { left: 40px !important; }

        .hamburger-icon {
            width: 18px !important;
            height: 18px !important;
            position: relative;
            transform: rotate(0deg);
            transition: .3s ease-in-out;
            cursor: pointer;
        }

        .hamburger-icon span {
            display: block;
            position: absolute;
            height: 2.5px !important;
            width: 100%;
            background: white !important;
            border-radius: 2px;
            opacity: 1;
            left: 0;
            transform: rotate(0deg);
            transition: .25s ease-in-out;
        }

        .hamburger-icon span:nth-child(1) { top: 3px; }
        .hamburger-icon span:nth-child(2) { top: 8px; }
        .hamburger-icon span:nth-child(3) { top: 13px; }

        .sidebar-open .hamburger-icon span:nth-child(1) {
            top: 8px;
            transform: rotate(135deg);
        }
        .sidebar-open .hamburger-icon span:nth-child(2) {
            opacity: 0;
            left: -18px;
        }
        .sidebar-open .hamburger-icon span:nth-child(3) {
            top: 8px;
            transform: rotate(-135deg);
        }

        /* ===== SIDEBAR CONTENT ===== */
        .sidebar-brand {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary-dark);
            text-decoration: none;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .sidebar-brand img {
            width: 50px;
            height: 50px;
            margin-right: 8px;
            transition: all 0.3s ease;
        }

        /* ===== PERBAIKAN: SCROLLABLE NAVIGATION ===== */
        .sidebar nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            margin-bottom: 0;
            padding-right: 8px;
            padding-bottom: 1rem;
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
            scroll-behavior: smooth;
            /* Kalkulasi tinggi: 100vh - logo section (90px) - logout button (60px) - padding (30px) */
            max-height: calc(100vh - 180px);
        }

        /* Custom scrollbar untuk webkit browsers */
        .sidebar nav::-webkit-scrollbar { 
            width: 6px; 
        }
        
        .sidebar nav::-webkit-scrollbar-track { 
            background: transparent;
            margin: 4px 0;
        }
        
        .sidebar nav::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
            transition: background 0.2s;
        }
        
        .sidebar nav::-webkit-scrollbar-thumb:hover { 
            background: #9ca3af; 
        }

        /* Sidebar links */
        .sidebar-link,
        .sidebar-submenu-link {
            display: flex;
            align-items: center;
            padding: 0.4rem 0.8rem;
            margin-bottom: 0.2rem;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
            color: #6c757d;
            position: relative;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .sidebar-link:hover,
        .sidebar-submenu-link:hover {
            color: #212529;
            background-color: #f8f9fa;
        }

        .sidebar-link.active,
        .sidebar-submenu-link.active {
            background-color: var(--primary-green);
            color: white;
            font-weight: 600;
        }

        .sidebar-link[aria-expanded="true"] .bi-chevron-right {
            transform: rotate(90deg);
        }

        .sidebar-link .bi-chevron-right {
            transition: transform 0.2s ease-in-out;
        }

        .sidebar-link i {
            margin-right: 10px;
            width: 18px;
            text-align: center;
            font-size: 0.9rem;
        }

        /* Indentasi submenu yang jelas */
        .ps-4 {
            padding-left: 2rem !important;
        }

        .ps-3 {
            padding-left: 1.5rem !important;
        }

        /* ===== LOGOUT BUTTON - STICKY AT BOTTOM ===== */
        .logout-button {
            background: linear-gradient(135deg, #868CFF 0%, #4318FF 100%) !important;
            border: none !important;
            color: white !important;
            position: sticky !important;
            bottom: 0 !important;
            margin: 0 -1.5rem !important;
            padding: 12px 16px !important;
            border-top: 1px solid #e9ecef !important;
            border-radius: 0 !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            z-index: 10 !important;
            transition: all 0.3s ease !important;
            background-color: white;
            flex-shrink: 0;
            margin-top: auto !important;
        }

        .logout-button:hover {
            background: linear-gradient(135deg, #4318FF 0%, #868CFF 100%) !important;
            color: white !important;
        }

        /* ===== COLLAPSED STATE ===== */
        .sidebar-collapsed .sidebar .sidebar-brand h4,
        .sidebar-collapsed .sidebar .sidebar-link span,
        .sidebar-collapsed .sidebar .sidebar-submenu-link span,
        .sidebar-collapsed .sidebar .logout-button span,
        .sidebar-collapsed .sidebar .bi-chevron-right {
            display: none;
        }

        .sidebar-collapsed .sidebar .menu-with-submenu {
            display: none !important;
        }

        .sidebar-collapsed .sidebar .sidebar-brand {
            justify-content: center;
            margin-bottom: 1rem;
        }

        .sidebar-collapsed .sidebar .sidebar-brand img {
            margin-right: 0;
            width: 40px;
            height: 40px;
        }

        .sidebar-collapsed .sidebar .sidebar-link,
        .sidebar-collapsed .sidebar .sidebar-submenu-link {
            justify-content: center;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-collapsed .sidebar .sidebar-link i,
        .sidebar-collapsed .sidebar .sidebar-submenu-link i {
            margin-right: 0;
            font-size: 1.1rem;
        }

        .sidebar-collapsed .sidebar .logout-button {
            display: flex;
            justify-content: center;
            padding: 12px !important;
            margin: 0 -0.5rem !important;
        }

        .sidebar-collapsed .sidebar .collapse {
            display: none !important;
        }

        .sidebar-collapsed .sidebar nav {
            max-height: calc(100vh - 160px);
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            flex-grow: 1;
            margin-left: 280px;
            padding: 2rem;
            padding-top: 4rem;
            overflow-y: auto;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .sidebar-collapsed .main-content {
            margin-left: 70px;
        }

        /* ===== TOOLTIPS FOR COLLAPSED ===== */
        .sidebar-collapsed .sidebar-link,
        .sidebar-collapsed .sidebar-submenu-link {
            position: relative;
        }

        .sidebar-collapsed .sidebar-link::after,
        .sidebar-collapsed .sidebar-submenu-link::after {
            content: attr(data-title);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background-color: #333;
            color: white;
            padding: 0.4rem 0.6rem;
            border-radius: 4px;
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            margin-left: 8px;
            z-index: 1000;
        }

        .sidebar-collapsed .sidebar-link:hover::after,
        .sidebar-collapsed .sidebar-submenu-link:hover::after {
            opacity: 1;
        }

        /* ===== OTHER STYLES ===== */
        .btn-primary-light {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
            color: white;
        }

        .btn-primary-light:hover {
            opacity: 0.9;
        }

        .text-primary-green {
            color: var(--primary-green);
        }

        .search-box {
            background-color: #FFFFFF;
            border-radius: 50rem;
            padding: 0.25rem 0.5rem;
            border: 1px solid #e9ecef;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .search-box .form-control,
        .search-box .input-group-text {
            border: none;
            box-shadow: none;
            background-color: transparent;
        }

        .card {
            border-radius: 20px !important;
        }

        /* ===== MOBILE OVERLAY ===== */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1030;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* ===== MOBILE RESPONSIVE ===== */
        @media (max-width: 768px) {
            .toggle-btn {
                left: 15px !important;
                top: 15px !important;
            }

            .sidebar-collapsed .toggle-btn {
                left: 15px !important;
            }

            .sidebar {
                position: fixed;
                height: 100vh;
                z-index: 1040;
                transform: translateX(-100%);
                left: 0;
                top: 0;
                width: 280px;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-collapsed .sidebar {
                width: 280px;
                padding: 1.5rem;
                transform: translateX(-100%);
            }

            .sidebar-collapsed .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 2rem;
                padding-top: 5rem;
                padding-left: 2rem;
            }

            .sidebar-collapsed .main-content {
                padding-left: 2rem;
                margin-left: 0;
            }

            .sidebar-collapsed .sidebar .sidebar-brand h4,
            .sidebar-collapsed .sidebar .sidebar-link span,
            .sidebar-collapsed .sidebar .sidebar-submenu-link span,
            .sidebar-collapsed .sidebar .logout-button span,
            .sidebar-collapsed .sidebar .bi-chevron-right {
                display: block;
            }

            .sidebar-collapsed .sidebar .menu-with-submenu {
                display: block !important;
            }

            .sidebar-collapsed .sidebar .sidebar-brand {
                justify-content: flex-start;
                margin-bottom: 1.5rem;
            }

            .sidebar-collapsed .sidebar .sidebar-brand img {
                margin-right: 8px;
                width: 50px;
                height: 50px;
            }

            .sidebar-collapsed .sidebar .sidebar-link,
            .sidebar-collapsed .sidebar .sidebar-submenu-link {
                justify-content: flex-start;
                padding: 0.6rem 0.8rem;
            }

            .sidebar-collapsed .sidebar .collapse {
                display: block !important;
            }

            .sidebar-collapsed .sidebar .logout-button {
                margin: 0 -1.5rem !important;
                padding: 12px 16px !important;
                justify-content: flex-start;
            }

            .sidebar nav {
                max-height: calc(100vh - 180px);
            }
        }

        @media (max-width: 768px) {
            html { font-size: 14px; }
            h3, .h3 { font-size: 1.3rem; }
            .table td, .table th {
                font-size: 0.75rem;
                padding: 0.5rem;
            }
            .btn {
                font-size: 0.75rem;
                padding: 0.4rem 0.7rem;
            }
            .card-body li { font-size: 0.8rem; }
        }

        .bi { font-size: 1rem; }
        .card-title .bi { font-size: 1.1rem; }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <button type="button" class="toggle-btn" id="sidebarToggle">
        <div class="hamburger-icon">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </button>

    <div class="wrapper">
        <aside class="sidebar shadow-sm" id="sidebar">
            <div class="sidebar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Optimise Logo">
                <h4 class="mb-0">Optimise</h4>
            </div>

            <nav class="nav flex-column">
                @foreach($menus as $menu)
                    @if($menu->children->isEmpty())
                        <a class="sidebar-link {{ ($menu->route && Request::is(trim($menu->route, '/') . '*')) ? 'active' : '' }}"
                           href="{{ $menu->route ? url($menu->route) : '#' }}"
                           data-title="{{ $menu->name }}">
                            @if($menu->icon)
                                <i class="{{ $menu->icon }}"></i>
                            @endif
                            <span>{{ $menu->name }}</span>
                        </a>
                    @else
                        <div class="menu-with-submenu">
                            <a class="sidebar-link d-flex justify-content-between {{ Request::is(collect($menu->children)->pluck('route')->map(fn($r) => trim($r, '/') . '*')->implode('|')) ? 'active' : '' }}"
                               data-bs-toggle="collapse"
                               href="#menu{{ $menu->id }}"
                               role="button"
                               data-title="{{ $menu->name }}">
                                <span>
                                    @if($menu->icon)
                                        <i class="{{ $menu->icon }}"></i>
                                    @endif
                                    <span>{{ $menu->name }}</span>
                                </span>
                                <i class="bi bi-chevron-right small"></i>
                            </a>
                            <div class="collapse {{ Request::is(collect($menu->children)->pluck('route')->map(fn($r) => trim($r, '/') . '*')->implode('|')) ? 'show' : '' }}"
                                 id="menu{{ $menu->id }}">
                                <div class="ps-4">
                                    @foreach($menu->children as $child)
                                        @if($child->children->isEmpty())
                                            <a class="sidebar-submenu-link {{ Request::is(trim($child->route, '/') . '*') ? 'active' : '' }}"
                                               href="{{ $child->route ? url($child->route) : '#' }}"
                                               data-title="{{ $child->name }}">
                                                <span>{{ $child->name }}</span>
                                            </a>
                                        @else
                                            <div>
                                                <a class="sidebar-submenu-link d-flex justify-content-between"
                                                   data-bs-toggle="collapse"
                                                   href="#menu{{ $child->id }}"
                                                   role="button"
                                                   data-title="{{ $child->name }}">
                                                    <span>{{ $child->name }}</span>
                                                    <i class="bi bi-chevron-right small"></i>
                                                </a>
                                                <div class="collapse {{ Request::is(collect($child->children)->pluck('route')->map(fn($r) => trim($r, '/') . '*')->implode('|')) ? 'show' : '' }}"
                                                     id="menu{{ $child->id }}">
                                                    <div class="ps-3">
                                                        @foreach($child->children as $grandchild)
                                                            <a class="sidebar-submenu-link {{ Request::is(trim($grandchild->route, '/') . '*') ? 'active' : '' }}"
                                                               href="{{ $grandchild->route ? url($grandchild->route) : '#' }}"
                                                               data-title="{{ $grandchild->name }}">
                                                                <span>{{ $grandchild->name }}</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($menu->slug === 'enpi-baseline-management' && auth()->user()->isSuperAdmin())
                        <div class="mt-3 pt-2" style="border-top: 1px solid #e9ecef;"></div>
                    @endif
                @endforeach
            </nav>

            <a href="{{ url('/logout') }}" class="btn logout-button fw-bold" data-title="Logout">
                <i class="bi bi-box-arrow-left"></i> <span>Logout</span>
            </a>
        </aside>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;
            const navContainer = sidebar.querySelector('nav');

            function isMobile() {
                return window.innerWidth <= 768;
            }

            function updateButtonState() {
                if (isMobile()) {
                    const sidebarVisible = sidebar.classList.contains('show');
                    if (sidebarVisible) {
                        body.classList.add('sidebar-open');
                    } else {
                        body.classList.remove('sidebar-open');
                    }
                } else {
                    if (body.classList.contains('sidebar-collapsed')) {
                        body.classList.remove('sidebar-open');
                    } else {
                        body.classList.add('sidebar-open');
                    }
                }
            }

            function toggleSidebar() {
                if (isMobile()) {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                } else {
                    body.classList.toggle('sidebar-collapsed');
                    if (body.classList.contains('sidebar-collapsed')) {
                        const openSubmenus = document.querySelectorAll('.collapse.show');
                        openSubmenus.forEach(submenu => {
                            submenu.classList.remove('show');
                        });
                    }
                }
                updateButtonState();
            }

            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSidebar();
            });

            overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                updateButtonState();
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (isMobile() && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        overlay.classList.remove('show');
                        updateButtonState();
                    } else if (!isMobile() && body.classList.contains('sidebar-collapsed')) {
                        body.classList.remove('sidebar-collapsed');
                        updateButtonState();
                    }
                }
            });

            window.addEventListener('resize', function() {
                if (!isMobile()) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                } else {
                    body.classList.remove('sidebar-collapsed');
                }
                updateButtonState();
            });

            // Jangan close sidebar/collapse saat klik submenu link
            navContainer.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link) return;
                
                const href = link.getAttribute('href');
                
                // Kalau link ini untuk toggle collapse (punya data-bs-toggle), stop di sini
                if (link.hasAttribute('data-bs-toggle')) {
                    return;
                }
                
                // Kalau link submenu biasa (di dalam collapse), JANGAN close collapse
                // Hanya close sidebar di mobile untuk navigasi
                if (isMobile() && href && href !== '#' && !href.startsWith('#menu')) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                    updateButtonState();
                }
            });

            // Auto scroll saat collapse dibuka
            const collapseElements = document.querySelectorAll('.collapse');
            collapseElements.forEach(collapse => {
                collapse.addEventListener('show.bs.collapse', function() {
                    const toggleLink = document.querySelector(`[href="#${this.id}"]`);
                    if (toggleLink) {
                        setTimeout(() => {
                            const linkTop = toggleLink.offsetTop;
                            const navHeight = navContainer.clientHeight;
                            const targetScroll = linkTop - (navHeight * 0.2);
                            
                            navContainer.scrollTo({
                                top: Math.max(0, targetScroll),
                                behavior: 'smooth'
                            });
                        }, 10);
                    }
                });
            });

            updateButtonState();
        });
    </script>
</body>

</html>