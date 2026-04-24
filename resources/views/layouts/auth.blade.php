<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Optimise</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #043873;
            --primary-light: #4F9CF9;
            --accent-yellow: #FFE492;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F0F4FA;
        }

        /* Left panel */
        .auth-left-panel {
            background: #ffffff;
            min-height: 100vh;
        }

        .auth-form-container {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow: 0 4px 32px rgba(4, 56, 115, 0.08);
        }

        /* Input with icon */
        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper .input-icon {
            position: absolute;
            top: 50%;
            left: 0.9rem;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 1rem;
            pointer-events: none;
            z-index: 5;
        }

        .input-icon-wrapper .form-control {
            padding-left: 2.6rem;
        }

        .input-icon-wrapper .toggle-pw {
            position: absolute;
            top: 50%;
            right: 0.9rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            color: #adb5bd;
            font-size: 1rem;
            cursor: pointer;
            z-index: 5;
        }

        .input-icon-wrapper .toggle-pw:hover {
            color: var(--primary-light);
        }

        /* Submit button */
        .btn-primary-light {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
            color: white;
            font-weight: 600;
            letter-spacing: 0.01em;
            transition: background-color 0.2s, box-shadow 0.2s;
        }

        .btn-primary-light:hover {
            background-color: #3b8cdf;
            border-color: #3b8cdf;
            color: white;
            box-shadow: 0 4px 14px rgba(79, 156, 249, 0.4);
        }

        /* Right branding panel */
        .auth-right-panel {
            background: linear-gradient(145deg, #043873 0%, #06529e 60%, #043873 100%);
            position: relative;
            overflow: hidden;
        }

        .auth-right-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 70% 30%, rgba(79,156,249,0.18) 0%, transparent 65%);
        }

        .auth-right-panel .curve-strip {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 60px;
            background-color: white;
            border-top-right-radius: 60px;
            border-bottom-right-radius: 60px;
        }

        .auth-brand-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .auth-brand-content .brand-icon {
            width: 130px;
            height: auto;
            filter: drop-shadow(0 8px 24px rgba(0,0,0,0.25));
        }

        .auth-brand-content h1 {
            font-size: 3.25rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-top: 1rem;
            margin-bottom: 0.25rem;
        }

        .auth-brand-content .brand-subtitle {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.65);
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .auth-form-header .brand-logo-inline {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .auth-form-header h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 0;
        }

        .auth-form-header p {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0;
        }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="row g-0" style="min-height:100vh;">

            <!-- Left: form panel -->
            <div class="col-lg-7 auth-left-panel d-flex align-items-center justify-content-center p-4">
                <div class="col-11 col-sm-9 col-md-8 col-lg-8 col-xl-7">
                    <div class="auth-form-container">
                        @yield('form-content')
                    </div>
                </div>
            </div>

            <!-- Right: branding panel (desktop only) -->
            <div class="col-lg-5 auth-right-panel d-none d-lg-flex flex-column align-items-center justify-content-center text-white">
                <div class="curve-strip"></div>
                <div class="auth-brand-content">
                    <img src="{{ asset('images/icon-optimise.png') }}" alt="Optimise" class="brand-icon">
                    <h1>Optimise</h1>
                    <p class="brand-subtitle">Energy Management System</p>
                </div>
            </div>

        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
