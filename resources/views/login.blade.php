@extends('layouts.auth')

@section('title', 'Sign In')

@section('form-content')
    <h2 class="fw-bold">Optimise</h2>
    <p class="text-secondary">Enter your email and password to log in</p>

    <!-- ✅ ERROR ALERT (Login Failed / Validation) -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $error }}<br>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ✅ LOGIN ERROR SESSION -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle-fill me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ url('/login-process') }}" method="POST" class="mt-4 needs-validation" novalidate>
        @csrf
        
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
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
        
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" 
                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                   id="password" 
                   name="password" 
                   placeholder="Min. 8 characters" 
                   required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="keepLoggedIn" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small" for="keepLoggedIn">
                    Keep me logged in
                </label>
            </div>
            <a href="#" class="text-decoration-none small">Forgot password?</a>
        </div>
        
        <button type="submit" class="btn btn-primary-light btn-lg w-100">Login</button>
        <p class="text-center text-secondary small mt-4">
            Don't have any account? <a href="{{ url('/register') }}" class="text-decoration-none fw-bold">Sign Up</a>
        </p>
    </form>
@endsection