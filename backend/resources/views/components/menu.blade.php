<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon"><i class="fa-solid fa-globe"></i></div>
        <div>
            <div class="logo-text">DTGHRM</div>
            <div class="logo-sub">The Globe – HRM</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fa-solid fa-gauge-high"></i></div>
            Dashboard
        </a>

        <div class="nav-section-label">People</div>
        <div class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-users"></i></div>
            Staff
            <span class="nav-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </div>
        <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fa-solid fa-user-tie"></i></div>
            Employee
            <span class="nav-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </a>

        <div class="nav-section-label">Productivity</div>
        <div class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-list-check"></i></div>
            To-Do List
            <span class="nav-badge">3</span>
        </div>
        <div class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-bell"></i></div>
            Notice
            <span class="nav-badge">2</span>
        </div>
        <div class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-diagram-project"></i></div>
            Project
        </div>
        <div class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-building"></i></div>
            Units
        </div>

        <div class="nav-section-label">HR Operations</div>
        <div class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-umbrella-beach"></i></div>
            Leave
            <span class="nav-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </div>
        <div class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-fingerprint"></i></div>
            Attendance
            <span class="nav-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </div>
        <div class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-clock"></i></div>
            TimeSheet
        </div>
        <div class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-sack-dollar"></i></div>
            Payroll
            <span class="nav-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </div>
        <div class="nav-section-label">System</div>
        <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') || request()->routeIs('branches.*') || request()->routeIs('departments.*') || request()->routeIs('designations.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fa-solid fa-gears"></i></div>
            Settings
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">
                @if(Auth::check() && Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="" class="h-full w-full rounded-full object-cover">
                @else
                    {{ Auth::check() ? substr(Auth::user()->name, 0, 2) : '?' }}
                @endif
            </div>
            <div>
                <div class="user-name">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
                <div class="user-role">{{ Auth::check() && Auth::user()->isCompany() ? 'Company Admin' : 'Employee' }}</div>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            <div class="user-opts" onclick="document.getElementById('logout-form').submit()"><i class="fa-solid fa-right-from-bracket"></i></div>
        </div>
    </div>
</aside>
