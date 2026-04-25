@extends('layouts.auth')

@section('title', 'Sign Up')

@section('form-content')
    <!-- Header -->
    <div class="auth-form-header d-flex align-items-center gap-2 mb-1">
        <img src="{{ asset('images/icon-optimise.png') }}" alt="Optimise" class="brand-logo-inline">
        <h2>Optimise</h2>
    </div>
    <p class="text-secondary mb-4" style="font-size:0.875rem;">Create your account to get started</p>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold" style="font-size:0.875rem;">Full Name</label>
            <div class="input-icon-wrapper">
                <i class="bi bi-person input-icon"></i>
                <input
                    type="text"
                    class="form-control form-control-lg @error('name') is-invalid @enderror"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="John Doe"
                    required
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold" style="font-size:0.875rem;">Email</label>
            <div class="input-icon-wrapper">
                <i class="bi bi-envelope input-icon"></i>
                <input
                    type="email"
                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="mail@example.com"
                    required
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold" style="font-size:0.875rem;">Password</label>
            <div class="input-icon-wrapper">
                <i class="bi bi-lock input-icon"></i>
                <input
                    type="password"
                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                    id="password"
                    name="password"
                    placeholder="Min. 8 characters"
                    required
                >
                <button type="button" class="toggle-pw" onclick="togglePw('password', this)" tabindex="-1">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold" style="font-size:0.875rem;">Confirm Password</label>
            <div class="input-icon-wrapper">
                <i class="bi bi-lock-fill input-icon"></i>
                <input
                    type="password"
                    class="form-control form-control-lg"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Min. 8 characters"
                    required
                >
                <button type="button" class="toggle-pw" onclick="togglePw('password_confirmation', this)" tabindex="-1">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary-light btn-lg w-100">
            <i class="bi bi-person-plus me-2"></i>Register
        </button>

        <p class="text-center text-secondary mt-4 mb-0" style="font-size:0.875rem;">
            Already have an account? <a href="{{ url('/login') }}" class="text-decoration-none fw-bold" style="color:var(--primary-light);">Sign In</a>
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
