<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Optimise</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
        .bg-primary-dark { background-color: var(--primary-dark); }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="row g-0 vh-100">
            <div class="col-lg-7 d-flex align-items-center justify-content-center bg-white p-4">
                <div class="col-11 col-sm-9 col-md-8 col-lg-7 col-xl-6">
                    @yield('form-content')
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-flex flex-column align-items-center justify-content-center bg-primary-dark text-white position-relative">
                <div class="position-absolute top-0 start-0 h-100" style="width: 60px; background-color: white; border-top-right-radius: 60px; border-bottom-right-radius: 60px;"></div>

                <div class="z-1 text-center">
                    <img src="{{ asset('images/icon-optimise.png') }}" alt="Optimise Icon" style="width: 160px; height: auto;">
                    <h1 class="display-3 fw-bold mt-3 ">Optimise</h1>
                </div>
            </div>
        </div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
