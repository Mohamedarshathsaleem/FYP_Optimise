@extends('layouts.auth')

@section('title', 'Sign In')

@section('form-content')
    <!-- Header -->
    <div class="auth-form-header d-flex align-items-center gap-2 mb-1">
        <img src="{{ asset('images/icon-optimise.png') }}" alt="Optimise" class="brand-logo-inline">
        <h2>Optimise</h2>
    </div>
    <p class="text-secondary mb-4" style="font-size:0.875rem;">Enter your email and password to log in</p>

    <!-- Error alerts -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $error }}<br>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle-fill me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ url('/login-process') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold" style="font-size:0.875rem;">Email</label>
            <div class="input-icon-wrapper">
                <i class="bi bi-envelope input-icon"></i>
                <input type="email"
                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="mail@example.com"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold" style="font-size:0.875rem;">Password</label>
            <div class="input-icon-wrapper">
                <i class="bi bi-lock input-icon"></i>
                <input type="password"
                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                       id="password"
                       name="password"
                       placeholder="Min. 8 characters"
                       required>
                <button type="button" class="toggle-pw" onclick="togglePw('password', this)" tabindex="-1">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="keepLoggedIn" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="keepLoggedIn" style="font-size:0.875rem;">
                    Keep me logged in
                </label>
            </div>
            <a href="#" class="text-decoration-none fw-semibold" style="font-size:0.875rem; color:var(--primary-light);">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary-light btn-lg w-100">
            <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </button>

        <p class="text-center text-secondary mt-4 mb-0" style="font-size:0.875rem;">
            Don't have an account? <a href="{{ url('/register') }}" class="text-decoration-none fw-bold" style="color:var(--primary-light);">Sign Up</a>
        </p>
    </form>

    <script>
    function togglePw(inputId, btn) {
        var input = document.getElementById(inputId);
        var icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }
    </script>
@endsection
