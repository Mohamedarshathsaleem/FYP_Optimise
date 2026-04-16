<div style="height: 200px; background-image: url('{{ asset('images/banner.jpg') }}'); background-size: cover; background-position: center;">
</div>

<div class="d-flex align-items-end" style="margin-top: -60px;">
    <div class="p-2 rounded-circle bg-primary-light d-inline-block z-1 shadow-sm" style="border: 5px solid white;">
        <i class="bi bi-person-fill text-white" style="font-size: 90px;"></i>
    </div>
    <nav class="nav nav-tabs border-0 ps-4">
        <a class="nav-link {{ ($activeTab ?? '') == 'details' ? 'active fw-bold text-dark' : 'text-secondary' }}" href="{{ url('/settings/details') }}">My details</a>
        <a class="nav-link {{ ($activeTab ?? '') == 'password' ? 'active fw-bold text-dark' : 'text-secondary' }}" href="{{ url('/settings/password') }}">Password</a>
        <a class="nav-link text-secondary" href="#">Plan</a>
        <a class="nav-link text-secondary" href="#">Billing</a>
        <a class="nav-link text-secondary" href="#">Notifications</a>
    </nav>
</div>
