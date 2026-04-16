@extends('layouts.app')

@section('title', 'Optimise - Pricing')

@section('content')

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
    
    .page-breadcrumb {
        background: var(--white);
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 0;
    }
    
    .breadcrumb-text {
        font-size: 0.875rem;
        color: var(--text-light);
        margin: 0;
    }
    
    .pricing-hero {
        background: var(--white);
        padding: 5rem 0 3rem;
        text-align: center;
    }
    
    .pricing-title {
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 1rem;
    }
    
    .pricing-highlight {
        background-color: var(--accent);
        padding: 0 0.5rem;
        display: inline-block;
    }
    
    .pricing-subtitle {
        font-size: 1.25rem;
        color: var(--text-light);
        max-width: 600px;
        margin: 0 auto;
    }
    
    .pricing-section {
        background: var(--light-bg);
        padding: 3rem 0 5rem;
    }
    
    .pricing-card {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 2.5rem;
        height: 100%;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .pricing-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    
    .pricing-card.popular {
        background: var(--primary-dark);
        color: var(--white);
        transform: scale(1.05);
        z-index: 2;
    }
    
    .pricing-card.popular:hover {
        transform: scale(1.05) translateY(-5px);
    }
    
    .popular-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: var(--accent);
        color: var(--primary-dark);
        padding: 0.5rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-bottom-left-radius: var(--border-radius);
    }
    
    .pricing-period {
        color: var(--text-light);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }
    
    .pricing-card.popular .pricing-period {
        color: rgba(255,255,255,0.7);
    }
    
    .price-amount {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1;
        margin: 1rem 0;
    }
    
    .price-currency {
        font-size: 1.5rem;
        font-weight: 600;
        vertical-align: super;
    }
    
    .pricing-description {
        color: var(--text-light);
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    
    .pricing-card.popular .pricing-description {
        color: rgba(255,255,255,0.8);
        border-bottom-color: rgba(255,255,255,0.2);
    }
    
    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0 0 2.5rem;
        flex-grow: 1; /* This makes the feature list take available space */
    }
    
    .feature-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
        color: var(--text-dark);
    }
    
    .pricing-card.popular .feature-item {
        color: var(--white);
    }
    
    .feature-icon {
        color: var(--primary-light);
        margin-right: 0.75rem;
        flex-shrink: 0;
        margin-top: 0.125rem;
    }
    
    .pricing-card.popular .feature-icon {
        color: var(--accent);
    }
    
    .feature-text {
        line-height: 1.5;
    }
    
    .btn-pricing {
        width: 100%;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-radius: var(--border-radius);
        transition: var(--transition);
        margin-top: auto; /* This pushes the button to the bottom */
    }
    
    @media (max-width: 992px) {
        .pricing-card.popular {
            transform: none;
            margin: 1rem 0;
        }
        
        .pricing-card.popular:hover {
            transform: translateY(-5px);
        }
    }
    
    @media (max-width: 768px) {
        .pricing-hero {
            padding: 3rem 0 2rem;
        }
        
        .pricing-title {
            font-size: 2.5rem;
        }
        
        .price-amount {
            font-size: 2.5rem;
        }
    }
</style>

<!-- Page Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <span class="breadcrumb-text">Pages / Pricing</span>
    </div>
</div>

<!-- Hero Section -->
<section class="pricing-hero">
    <div class="container">
        <h1 class="pricing-title">Choose <span class="pricing-highlight">Your Plan</span></h1>
        <p class="pricing-subtitle">Select the perfect plan for your organization and start optimizing energy management with ISO 50001 compliance</p>
    </div>
</section>

<!-- Pricing Cards Section -->
<section class="pricing-section">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <!-- Weekly Plan -->
            <div class="col-lg-4 d-flex">
                <div class="pricing-card w-100">
                    <div class="pricing-period">WEEKLY</div>
                    <div class="price-amount">
                        <span class="price-currency">RM</span> -
                    </div>
                    <p class="pricing-description">Perfect for short-term evaluation and testing</p>
                    
                    <ul class="feature-list">
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Basic energy monitoring dashboard</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Up to 5 energy meters</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Standard reporting templates</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Email support</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Basic compliance checklist</span>
                        </li>
                    </ul>
                    
                    <a href="{{ url('/register') }}" class="btn btn-outline-primary btn-pricing">
                        Get Started
                    </a>
                </div>
            </div>

            <!-- Monthly Plan - Popular -->
            <div class="col-lg-4 d-flex">
                <div class="pricing-card popular w-100">
                    <div class="popular-badge">MOST POPULAR</div>
                    <div class="pricing-period">MONTHLY</div>
                    <div class="price-amount">
                        <span class="price-currency">RM</span> -
                    </div>
                    <p class="pricing-description">Ideal for ongoing energy management and compliance</p>
                    
                    <ul class="feature-list">
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Advanced energy monitoring & analytics</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Up to 25 energy meters</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">AI-powered optimization insights</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Automated ISO 50001 documentation</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Priority email & chat support</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Compliance audit preparation</span>
                        </li>
                    </ul>
                    
                    <a href="{{ url('/register') }}" class="btn btn-light btn-pricing">
                        Get Started
                    </a>
                </div>
            </div>

            <!-- Yearly Plan -->
            <div class="col-lg-4 d-flex">
                <div class="pricing-card w-100">
                    <div class="pricing-period">YEARLY</div>
                    <div class="price-amount">
                        <span class="price-currency">RM</span> -
                    </div>
                    <p class="pricing-description">Best value for long-term energy management strategy</p>
                    
                    <ul class="feature-list">
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Enterprise energy management suite</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Unlimited energy meters</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Advanced AI predictive analytics</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Full ISO 50001 compliance automation</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Dedicated account manager</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">Custom reporting & integration</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-check-circle-fill feature-icon"></i>
                            <span class="feature-text">24/7 premium support</span>
                        </li>
                    </ul>
                    
                    <a href="{{ url('/register') }}" class="btn btn-outline-primary btn-pricing">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection