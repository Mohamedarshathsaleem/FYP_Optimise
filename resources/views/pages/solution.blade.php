@extends('layouts.app')

@section('title', 'Optimise - Solution')

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
    
    .hero-section {
        background: var(--white);
        padding: 5rem 0;
    }
    
    .hero-title {
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }
    
    .hero-highlight {
        background-color: var(--accent);
        padding: 0 0.5rem;
        display: inline-block;
    }
    
    .hero-subtitle {
        font-size: 1.25rem;
        color: var(--text-light);
        margin-bottom: 2rem;
        line-height: 1.6;
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
        line-height: 1.2;
    }
    
    .section-subtitle {
        color: var(--text-light);
        font-size: 1.1rem;
        margin-bottom: 2rem;
        line-height: 1.6;
    }
    
    .features-section {
        background: var(--light-bg);
        padding: 5rem 0;
    }
    
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }
    
    .feature-card {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 2.5rem 2rem;
        height: 100%;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        border: 1px solid rgba(0,0,0,0.05);
        text-align: center;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    
    .feature-icon {
        width: 70px;
        height: 70px;
        background: rgba(79, 156, 249, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: var(--primary-light);
        font-size: 1.75rem;
    }
    
    .feature-title {
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--primary-dark);
        font-size: 1.25rem;
    }
    
    .feature-description {
        color: var(--text-light);
        line-height: 1.6;
    }
    
    .process-section {
        background: var(--white);
        padding: 5rem 0;
    }
    
    .process-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 3rem 0;
    }
    
    .process-steps:before {
        content: '';
        position: absolute;
        top: 35px;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--primary-light);
        z-index: 1;
    }
    
    .process-step {
        text-align: center;
        position: relative;
        z-index: 2;
        flex: 1;
    }
    
    .step-number {
        width: 70px;
        height: 70px;
        background: var(--primary-dark);
        color: var(--white);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .step-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--primary-dark);
    }
    
    .step-description {
        color: var(--text-light);
        font-size: 0.9rem;
        padding: 0 1rem;
    }
    
    .benefits-section {
        background: var(--light-bg);
        padding: 5rem 0;
    }
    
    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }
    
    .benefit-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .benefit-icon {
        width: 24px;
        height: 24px;
        background: var(--primary-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 0.75rem;
        flex-shrink: 0;
        margin-top: 0.25rem;
    }
    
    .benefit-content h4 {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--primary-dark);
    }
    
    .benefit-content p {
        color: var(--text-light);
        margin: 0;
        line-height: 1.5;
    }
    
    @media (max-width: 768px) {
        .hero-section {
            padding: 3rem 0;
        }
        
        .hero-title {
            font-size: 2.5rem;
        }
        
        .process-steps {
            flex-direction: column;
            gap: 2rem;
        }
        
        .process-steps:before {
            display: none;
        }
        
        .feature-grid, .benefits-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Page Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <span class="breadcrumb-text">Pages / Solution</span>
    </div>
</div>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="hero-title">Optimise ISO <span class="hero-highlight">50001</span> Compliance</h1>
                <p class="hero-subtitle">Our AI-powered platform transforms complex energy management requirements into a streamlined, automated process. We eliminate the administrative burden of ISO 50001 compliance while delivering measurable energy savings and operational efficiency.</p>
                <a href="{{ url('/register') }}" class="btn btn-primary btn-lg px-4">
                    Get Started <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?q=80&w=2070" alt="Energy management dashboard" class="img-fluid hero-image">
            </div>
        </div>
    </div>
</section>

<!-- Features System Section -->
<section class="features-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?q=80&w=2070" alt="Features System Diagram" class="img-fluid rounded-3 shadow-sm">
            </div>
            <div class="col-lg-6">
                <h2 class="section-title">Comprehensive <span class="hero-highlight">Feature System</span></h2>
                <p class="section-subtitle">Our integrated platform combines powerful modules that work together to simplify ISO 50001 implementation and ongoing compliance management.</p>
                
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4 class="feature-title">Energy Monitoring</h4>
                        <p class="feature-description">Real-time tracking of energy consumption with automated data collection and anomaly detection.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h4 class="feature-title">Documentation Management</h4>
                        <p class="feature-description">Automated generation and organization of all required ISO 50001 documentation and records.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-lightbulb"></i>
                        </div>
                        <h4 class="feature-title">AI Analytics</h4>
                        <p class="feature-description">Machine learning algorithms identify optimization opportunities and predict energy usage patterns.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <h4 class="feature-title">Compliance Tracking</h4>
                        <p class="feature-description">Automated monitoring of compliance status with alerts for required actions and deadlines.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Implementation Process Section -->
<section class="process-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Streamlined Implementation <span class="hero-highlight">Process</span></h2>
                <p class="section-subtitle">Our structured approach ensures successful ISO 50001 implementation with minimal disruption to your operations.</p>
            </div>
        </div>
        
        <div class="process-steps">
            <div class="process-step">
                <div class="step-number">1</div>
                <h4 class="step-title">Assessment & Planning</h4>
                <p class="step-description">Comprehensive energy review and gap analysis to establish baseline and objectives.</p>
            </div>
            
            <div class="process-step">
                <div class="step-number">2</div>
                <h4 class="step-title">System Setup</h4>
                <p class="step-description">Configuration of monitoring systems and integration with existing data sources.</p>
            </div>
            
            <div class="process-step">
                <div class="step-number">3</div>
                <h4 class="step-title">Implementation</h4>
                <p class="step-description">Deployment of energy management processes and documentation system.</p>
            </div>
            
            <div class="process-step">
                <div class="step-number">4</div>
                <h4 class="step-title">Monitoring & Optimization</h4>
                <p class="step-description">Ongoing performance tracking and continuous improvement initiatives.</p>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="#" class="btn btn-outline-primary btn-lg px-4">
                View Implementation Guide <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="benefits-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Tangible Business <span class="hero-highlight">Benefits</span></h2>
                <p class="section-subtitle">Our solution delivers measurable value beyond compliance, impacting your bottom line and sustainability goals.</p>
            </div>
        </div>
        
        <div class="benefits-grid">
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="benefit-content">
                    <h4>Reduced Energy Costs</h4>
                    <p>Achieve 15-25% reduction in energy expenses through optimized consumption and efficiency improvements.</p>
                </div>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="benefit-content">
                    <h4>Time Savings</h4>
                    <p>Reduce administrative burden by up to 60% with automated documentation and reporting.</p>
                </div>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="benefit-content">
                    <h4>Faster Certification</h4>
                    <p>Accelerate ISO 50001 certification timeline from 12+ months to just 4-6 months.</p>
                </div>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="benefit-content">
                    <h4>Improved Compliance</h4>
                    <p>Maintain continuous compliance with automated monitoring and audit trail generation.</p>
                </div>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="benefit-content">
                    <h4>Data-Driven Decisions</h4>
                    <p>Leverage AI insights to make informed decisions about energy investments and improvements.</p>
                </div>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="benefit-content">
                    <h4>Sustainability Reporting</h4>
                    <p>Automatically generate reports for ESG compliance and sustainability initiatives.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection