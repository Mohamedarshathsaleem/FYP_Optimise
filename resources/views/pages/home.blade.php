@extends('layouts.app')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    :root {
        --primary-dark: #043873;
        --primary-light: #4F9CF9;
        --accent: #FFE492;
        --text-dark: #212529;
        --text-light: #6C757D;
        --white: #FFFFFF;
        --light-bg: #F8F9FA;
        --border-radius: 8px;
        --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }
    .hero-section {
        background: linear-gradient(rgba(4, 56, 115, 0.92), rgba(4, 56, 115, 0.88)), url('https://images.unsplash.com/photo-1521791136064-7986c2920216?q=80&w=2070');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        padding: 6rem 0;
    }
    .hero-title {
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }
    .hero-subtitle {
        font-size: 1.25rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }
    .hero-image {
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transition: var(--transition);
    }
    .hero-image:hover {
        transform: translateY(-5px);
    }
    .section-title {
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 1.5rem;
    }
    .section-subtitle {
        color: var(--text-light);
        font-size: 1.1rem;
        margin-bottom: 2rem;
    }
    .feature-card {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 2rem;
        height: 100%;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        border: 1px solid rgba(0,0,0,0.05);
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    .feature-icon {
        width: 60px;
        height: 60px;
        background: rgba(79, 156, 249, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        color: var(--primary-light);
        font-size: 1.5rem;
    }
    .feature-title {
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--primary-dark);
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 4rem 0;
            background-attachment: scroll;
        }
        .hero-title {
            font-size: 2.5rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <h1 class="hero-title text-white">AI-Powered ISO 50001 Compliance Made Simple</h1>
                <p class="hero-subtitle text-white">Reduce energy costs by 20% with automated monitoring and AI insights</p>
                <a href="{{ url('/register') }}" class="btn btn-primary-light btn-lg px-4">
                    Sign Up Now <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-lg-5">
                <img src="{{ asset('images/hero_image.png') }}" alt="Plant in a lightbulb" class="img-fluid hero-image">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Streamline Your Energy Management</h2>
                <p class="section-subtitle">Our AI-powered platform simplifies ISO 50001 compliance while maximizing energy efficiency and cost savings</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h4 class="feature-title">Automated Monitoring</h4>
                    <p class="text-secondary">Continuously track energy consumption patterns and identify optimization opportunities in real-time.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <h4 class="feature-title">Compliance Documentation</h4>
                    <p class="text-secondary">Automatically generate required documentation and reports for ISO 50001 certification and audits.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <h4 class="feature-title">AI-Powered Insights</h4>
                    <p class="text-secondary">Leverage machine learning algorithms to predict energy usage and recommend efficiency improvements.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5 bg-white">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="section-title">Optimize ISO 50001 Compliance</h2>
                <p class="section-subtitle">Our platform was developed by energy management experts to simplify the complex process of ISO 50001 compliance while delivering tangible business benefits.</p>
                <p class="text-secondary mb-4">With our solution, organizations can reduce administrative burden by up to 60% while achieving energy savings that directly impact the bottom line.</p>
                <a href="#" class="btn btn-primary-light btn-lg px-4">
                    Get Started <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-lg-6">
                <div class="bg-light rounded-3 p-4" style="height: 350px; display: flex; align-items: center; justify-content: center;">
                    <p class="text-center text-muted">Data Visualization Dashboard<br><small>Interactive charts showing energy consumption trends</small></p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
