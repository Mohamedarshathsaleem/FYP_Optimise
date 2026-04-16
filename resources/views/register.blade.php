@extends('layouts.auth')

@section('title', 'Sign Up')

@section('form-content')
    <h2 class="fw-bold">Optimise</h2>
    <p class="text-secondary">Create an account</p>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}" class="mt-4">
        @csrf

        <!-- ✅ NAME FIELD BARU -->
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
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

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
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

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
                type="password"
                class="form-control form-control-lg @error('password') is-invalid @enderror"
                id="password"
                name="password"
                placeholder="Min. 8 characters"
                required
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input
                type="password"
                class="form-control form-control-lg"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Min. 8 characters"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary-light btn-lg w-100">Register</button>

        <p class="text-center text-secondary small mt-4">
            Already have an account? <a href="{{ url('/login') }}" class="text-decoration-none fw-bold">Sign In</a>
        </p>
    </form>
@endsection