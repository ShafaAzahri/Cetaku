<aside class="sidebar" id="sidebar">
    <div class="brand-logo">
        <i class="fas fa-print"></i>
        <span class="brand-text">{{ $tokoInfo->nama ?? 'CETAKU' }}</span>
    </div>
    <ul class="nav-list">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span class="nav-text">Beranda</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.product-manager') }}" class="nav-link {{ request()->routeIs('admin.product-manager') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i>
                <span class="nav-text">Kelola Produk</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.pesanan.index') }}" class="nav-link {{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span class="nav-text">Pesanan</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.mesins.index') }}" class="nav-link {{ request()->routeIs('admin.mesins.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span class="nav-text">Mesin</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.operators.index') }}" class="nav-link {{ request()->routeIs('admin.operators.*') ? 'active' : '' }}">
                <i class="fas fa-user-tie"></i>
                <span class="nav-text">Operator</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.ekspedisi.index') }}" class="nav-link {{ request()->routeIs('admin.ekspedisi.*') ? 'active' : '' }}">
                <i class="fas fa-truck"></i>
                <span class="nav-text">Ekspedisi</span>
            </a>
        </li>
        <!-- <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-history"></i>
                <span class="nav-text">Riwayat</span>
            </a>
        </li> -->
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