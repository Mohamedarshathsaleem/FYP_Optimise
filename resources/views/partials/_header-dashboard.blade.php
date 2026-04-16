<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Pages / @yield('page-title', 'Dashboard')</p>
        <h3 class="fw-bold">@yield('page-title-main', 'Main Dashboard')</h3>
    </div>
    
    <div class="d-flex align-items-center">
        <!-- Search -->
        <div class="input-group me-3">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control border-start-0" placeholder="Search">
        </div>
        
        <!-- ✅ PROFILE DROPDOWN RESPONSIVE -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" 
               id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ auth()->user()->avatar ?? 'https://i.pravatar.cc/40?img=' . substr(auth()->user()->email ?? '1', 0, 1) }}" 
                     class="rounded-circle me-2" alt="User" width="40" height="40">
                <div class="d-none d-md-block text-start">
                    <div class="fw-bold small">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="text-secondary small">{{ ucfirst(auth()->user()->role ?? 'user') }}</div>
                </div>
            </a>
            
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdownwn">
                
                    <div class="dropdown-item-text px-3 py-2">
                        <img src="{{ auth()->user()->avatar ?? 'https://i.pravatar.cc/40?img=' . substr(auth()->user()->email ?? '1', 0, 1) }}" 
                             class="rounded-circle me-2" alt="Avatar" width="40" height="40">
                        <div>
                            <h6 class="mb-0">{{ auth()->user()->name ?? 'User' }}</h6>
                            <small class="text-muted">{{ auth()->user()->email ?? '' }}</small>
                        </div>
                    </div>
                </l/li>
                <hr class="dropdown-divider"></l/li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></l/li>
                <a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></l/li>
                <a class="dropdown-item" href="#"><i class="bi bi-bell me-2"></i>Notifications</a></l/li>
                <hr class="dropdown-divider"></l/li>
                
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>