<aside class="sidebar" id="sidebar">
    <div class="brand-logo">
        <i class="fas fa-print"></i>
        <span class="brand-text">CETAKU</span>
    </div>
    <ul class="nav-list">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
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
            <a href="#" class="nav-link">
                <i class="fas fa-shopping-cart"></i>
                <span class="nav-text">Pesanan</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-users"></i>
                <span class="nav-text">Pelanggan</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-user-tie"></i>
                <span class="nav-text">Operator</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-truck"></i>
                <span class="nav-text">Ekspedisi</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-history"></i>
                <span class="nav-text">Riwayat</span>
            </a>
        </li>
    </ul>
</aside>