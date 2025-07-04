<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <span class="logo-icon"><i class="fas fa-print"></i></span>
            <span class="logo-text">{{ $tokoInfo->nama ?? 'CETAKU' }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ url('/') }}">Beranda</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Kategori
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
    @foreach ($categories as $category)
        <li>
            <a class="dropdown-item" href="{{ route('produk-all', ['kategori' => $category->nama_kategori]) }}">
                {{ $category->nama_kategori }}
            </a>
        </li>
    @endforeach
</ul>

                </li>
            </ul>
            
           <div class="search-box d-flex">
                <form class="d-flex w-100" action="{{ route('search') }}" method="GET">
                    <input 
                    class="form-control me-2" 
                    type="search" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Mau cetak apa?" 
                    aria-label="Search">
                    <button class="btn search-btn" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <ul class="navbar-nav ms-auto align-items-center">
                @if(session()->has('api_token') && session()->has('user'))
                    <!-- Notifikasi -->
                    <li class="nav-item">
    <a href="{{ route('user.pesanan') }}" class="dropdown-item">
        <i class="far fa-bell"></i>
    </a>
</li>


                    <!-- Keranjang -->
                    <li class="nav-item">
                        <a class="nav-link cart-icon" href="{{ route('keranjang') }}">
                            <i class="fas fa-shopping-cart"></i>
                          
                        </a>
                    </li>

                    <!-- User Login Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-profile" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ session('user')['nama'] }}&background=4361ee&color=fff" alt="User Avatar">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @if(session('user')['role'] == 'super_admin')
                                <li><a class="dropdown-item" href="{{ route('superadmin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            @elseif(session('user')['role'] == 'admin')
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('user.profile') }}"><i class="fas fa-user me-2"></i>Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <!-- Jika belum login -->
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="btn btn-login">Login</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<style>
    .search-box .form-control {
        width: 500px;
    }
    @media (max-width: 768px) {
        .search-box .form-control {
            width: 100%;
        }
    }
</style>
