@extends('layouts.app')

@section('title', 'Optimise - Features')

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
    
    /* Enhanced Comparison Section Styles */
    .comparison-section {
        background: var(--light-bg);
        padding: 5rem 0;
    }
    
    .comparison-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .comparison-table {
        background: var(--white);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        margin-bottom: 3rem;
    }
    
    .table-header {
        background: var(--primary-dark);
        color: var(--white);
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
    }
    
    .feature-column-header {
        flex: 1;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .approach-column-header {
        flex: 1;
        text-align: center;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .table-row {
        display: flex;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem 2rem;
        align-items: center;
    }
    
    .table-row:last-child {
        border-bottom: none;
    }
    
    .table-row:nth-child(even) {
        background: rgba(248, 249, 250, 0.5);
    }
    
    .feature-column {
        flex: 1;
        font-weight: 600;
        color: var(--primary-dark);
    }
    
    .approach-column {
        flex: 1;
        text-align: center;
        padding: 0 1rem;
    }
    
    .traditional-approach {
        color: #dc3545;
        position: relative;
    }
    
    .traditional-approach:before {
        position: absolute;
        left: -1.5rem;
        color: #dc3545;
        font-weight: bold;
    }
    
    .our-approach {
        color: #198754;
        position: relative;
    }
    
    .our-approach:before {
        position: absolute;
        left: -1.5rem;
        color: #198754;
        font-weight: bold;
    }
    
    .benefit-highlights {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }
    
    .benefit-card {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 2rem;
        box-shadow: var(--box-shadow);
        border-top: 4px solid var(--primary-light);
        transition: var(--transition);
    }
    
    .benefit-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }
    
    .benefit-icon {
        width: 50px;
        height: 50px;
        background: rgba(79, 156, 249, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        color: var(--primary-light);
        font-size: 1.25rem;
    }
    
    .benefit-title {
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--primary-dark);
        font-size: 1.2rem;
    }
    
    .stat-highlight {
        text-align: center;
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
        border-radius: var(--border-radius);
        padding: 3rem 2rem;
        color: var(--white);
        margin-top: 2rem;
    }
    
    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        font-size: 1.1rem;
        opacity: 0.9;
    }
    
    @media (max-width: 768px) {
        .hero-section {
            padding: 4rem 0;
            background-attachment: scroll;
        }
        
        .hero-title {
            font-size: 2.5rem;
        }
        
        .table-header, .table-row {
            flex-direction: column;
            text-align: center;
            padding: 1rem;
        }
        
        .feature-column, .approach-column {
            flex: auto;
            margin-bottom: 0.5rem;
            width: 100%;
        }
        
        .traditional-approach:before, .our-approach:before {
            position: static;
            margin-right: 0.5rem;
        }
        
        .benefit-highlights {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Page Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <span class="breadcrumb-text">Pages / Features</span>
    </div>
</div>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="hero-title text-white">Powerful Features for Energy Management Excellence</h1>
                <p class="hero-subtitle text-white">Discover how our comprehensive platform simplifies ISO 50001 compliance while maximizing your energy efficiency</p>
            </div>
        </div>
    </div>
</section>

<!-- Core Features Section -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Comprehensive ISO 50001 Compliance Tools</h2>
                <p class="section-subtitle">Our platform combines cutting-edge technology with energy management expertise to deliver measurable results</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h4 class="feature-title">Automated Energy Monitoring</h4>
                    <p class="text-secondary">Continuously track energy consumption across all facilities with real-time dashboards and automated alerts for anomalies.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <h4 class="feature-title">Compliance Documentation</h4>
                    <p class="text-secondary">Automatically generate and maintain all required ISO 50001 documentation, including energy reviews, policies, and objectives.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <h4 class="feature-title">AI-Powered Analytics</h4>
                    <p class="text-secondary">Leverage machine learning to identify optimization opportunities, predict energy usage, and recommend efficiency improvements.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-clipboard-data"></i>
                    </div>
                    <h4 class="feature-title">Performance Tracking</h4>
                    <p class="text-secondary">Monitor key energy performance indicators (EnPIs) and track progress against your energy objectives and targets.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4 class="feature-title">Audit Preparation</h4>
                    <p class="text-secondary">Simplify certification and surveillance audits with organized evidence, automated reporting, and compliance checklists.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h4 class="feature-title">Stakeholder Engagement</h4>
                    <p class="text-secondary">Facilitate communication and training with built-in tools for employee awareness and management review meetings.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced Benefits Comparison Section -->
<section class="comparison-section">
    <div class="container">
        <div class="comparison-header">
            <h2 class="section-title">Traditional vs. AI-Powered Energy Management</h2>
            <p class="section-subtitle">See how our platform transforms ISO 50001 implementation and ongoing compliance</p>
        </div>
        
        <div class="comparison-table">
            <div class="table-header">
                <div class="feature-column-header">Energy Management Aspect</div>
                <div class="approach-column-header">Traditional Approach</div>
                <div class="approach-column-header">Our AI-Powered Platform</div>
            </div>
            
            <div class="table-row">
                <div class="feature-column">Documentation Management</div>
                <div class="approach-column traditional-approach">Manual processes, scattered documents, version control issues</div>
                <div class="approach-column our-approach">Automated documentation with centralized repository and version tracking</div>
            </div>
            
            <div class="table-row">
                <div class="feature-column">Energy Monitoring & Data Collection</div>
                <div class="approach-column traditional-approach">Periodic manual data collection with delayed insights</div>
                <div class="approach-column our-approach">Real-time automated monitoring with instant anomaly detection</div>
            </div>
            
            <div class="table-row">
                <div class="feature-column">Performance Analysis</div>
                <div class="approach-column traditional-approach">Basic spreadsheet analysis with limited predictive capabilities</div>
                <div class="approach-column our-approach">AI-powered analytics with predictive modeling and optimization recommendations</div>
            </div>
            
            <div class="table-row">
                <div class="feature-column">Audit Preparation</div>
                <div class="approach-column traditional-approach">Time-consuming manual evidence gathering and organization</div>
                <div class="approach-column our-approach">Automated audit trails and organized evidence repository</div>
            </div>
            
            <div class="table-row">
                <div class="feature-column">Implementation Timeline</div>
                <div class="approach-column traditional-approach">6-12 months for full implementation</div>
                <div class="approach-column our-approach">2-4 months with our streamlined platform</div>
            </div>
            
            <div class="table-row">
                <div class="feature-column">Ongoing Maintenance</div>
                <div class="approach-column traditional-approach">Significant manual effort required for updates and reporting</div>
                <div class="approach-column our-approach">Automated updates with minimal manual intervention needed</div>
            </div>
        </div>
        
        <div class="benefit-highlights">
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="bi bi-clock"></i>
                </div>
                <h4 class="benefit-title">60% Time Savings</h4>
                <p class="text-secondary">Reduce administrative burden by automating documentation, reporting, and compliance tracking.</p>
            </div>
            
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <h4 class="benefit-title">15-25% Energy Cost Reduction</h4>
                <p class="text-secondary">Achieve significant savings through AI-identified optimization opportunities and continuous monitoring.</p>
            </div>
            
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h4 class="benefit-title">Streamlined Certification</h4>
                <p class="text-secondary">Simplify the certification and audit process with organized evidence and automated compliance checks.</p>
            </div>
        </div>
        
        <div class="stat-highlight">
            <div class="stat-number">85%</div>
            <div class="stat-label">of organizations achieve ISO 50001 certification faster with our platform</div>
        </div>
    </div>
</section>

@endsection
