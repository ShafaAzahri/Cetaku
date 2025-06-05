<aside class="sidebar" id="sidebar">
    <div class="brand-logo">
        <i class="fas fa-print"></i>
        <span class="brand-text">{{ $tokoInfo->nama ?? 'CETAKU' }}</span>
    </div>
    <ul class="nav-list">
        <li class="nav-item">
            <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('superadmin.admin.index') }}" class="nav-link {{ request()->routeIs('superadmin.admin.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i>
                <span class="nav-text">Manajemen Admin</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('superadmin.user.index') }}" class="nav-link {{ request()->routeIs('superadmin.user.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span class="nav-text">Manajemen User</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('superadmin.operator.index') }}" class="nav-link {{ request()->routeIs('superadmin.operator.*') ? 'active' : '' }}">
                <i class="fas fa-user-tie"></i>
                <span class="nav-text">Manajemen Operator</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('superadmin.laporan.index') }}" class="nav-link {{ request()->routeIs('superadmin.laporan.*') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i>
                <span class="nav-text">Laporan</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('superadmin.pengaturan.index') }}" class="nav-link {{ request()->routeIs('superadmin.pengaturan.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span class="nav-text">Pengaturan</span>
            </a>
        </li>
        <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
                @csrf
            </form>
            <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-text">Logout</span>
            </a>
        </li>
    </ul>
</aside>