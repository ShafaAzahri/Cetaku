<nav class="navbar">
    <div class="d-flex align-items-center">
        <div class="toggle-sidebar me-3" id="toggle-sidebar">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    
    <form class="search-form d-none d-md-block">
        <input type="text" class="search-input" placeholder="Masukkan ID Pemesanan dan ID Customer untuk mencari detail info">
    </form>
    
    <div class="user-profile">
        <div class="avatar">
            <img src="https://ui-avatars.com/api/?name=&background=4361ee&color=fff" alt="User Avatar">
        </div>
        <div class="dropdown">
            <a class="dropdown-toggle text-decoration-none text-dark" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="d-none d-sm-inline-block me-1 user-name"></span>
                <i class="fas fa-chevron-down fa-xs"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle"></i> Profil</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Pengaturan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button type="button" class="dropdown-item logout-button">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>