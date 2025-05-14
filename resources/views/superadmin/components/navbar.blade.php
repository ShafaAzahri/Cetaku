<nav class="navbar">
    <div class="d-flex align-items-center">
        <div class="toggle-sidebar me-3" id="toggle-sidebar">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    
    <form class="search-form d-none d-md-block">
        <input type="text" class="search-input" placeholder="Cari di sistem...">
    </form>
    
    <div class="user-profile">
        <div class="avatar">
            <img src="https://ui-avatars.com/api/?name={{ session('user')['nama'] }}&background=6366f1&color=fff" alt="User Avatar" id="user-avatar">
        </div>
        <div class="dropdown">
            <a class="dropdown-toggle text-decoration-none text-dark" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="d-none d-sm-inline-block me-1 user-name">{{ session('user')['nama'] }}</span>
                <i class="fas fa-chevron-down fa-xs"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle"></i> Profil</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Pengaturan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>