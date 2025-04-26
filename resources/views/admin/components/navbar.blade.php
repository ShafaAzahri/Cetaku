<div class="navbar">
    <div class="d-flex align-items-center">
        <button id="sidebar-toggle" class="btn btn-link d-md-none">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <form class="search-form">
        <input type="text" placeholder="Masukkan ID Pemesanan dan ID Customer untuk mencari detail info yang dicari" />
    </form>
    
    <div class="user-dropdown">
        <img src="{{ asset('images/user-avatar.jpg') }}" alt="User Avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ Auth::user()->nama }}&background=random'">
        <div class="dropdown">
            <a class="dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                {{ Auth::user()->nama }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="#">Profil</a></li>
                <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>