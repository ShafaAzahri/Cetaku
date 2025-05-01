// Immediate auth check - runs before anything else
(function () {
    // Fungsi untuk redirect ke login
    function redirectToLogin() {
        localStorage.removeItem("api_token");
        localStorage.removeItem("user");
        localStorage.removeItem("expires_at");
        window.location.href = "/login";
    }

    // Fungsi untuk mendapatkan path saat ini
    function getCurrentPath() {
        return window.location.pathname;
    }

    // Fungsi untuk mendapatkan role yang dibutuhkan dari meta tag
    function getRequiredRole() {
        const requiredRoleElement = document.querySelector(
            'meta[name="required-role"]'
        );
        return requiredRoleElement ? requiredRoleElement.content : null;
    }

    // Fungsi untuk mendapatkan user saat ini
    function getCurrentUser() {
        try {
            const userStr = localStorage.getItem("user");
            return userStr ? JSON.parse(userStr) : null;
        } catch (e) {
            console.error("Error parsing user data:", e);
            return null;
        }
    }

    // Check if authentication is required
    const authRequiredElement = document.querySelector(
        'meta[name="auth-required"]'
    );
    if (!authRequiredElement || authRequiredElement.content !== "true") {
        // Jika halaman tidak membutuhkan autentikasi, keluar dari fungsi
        return;
    }

    const token = localStorage.getItem("api_token");
    const expiresAt = localStorage.getItem("expires_at");
    const userStr = localStorage.getItem("user");

    // 1. Check for basic auth requirements
    if (!token || !expiresAt || new Date(expiresAt) <= new Date()) {
        console.log(
            "Authentication required but missing or expired token. Redirecting to login..."
        );
        redirectToLogin();
        return;
    }

    // 2. Check for role requirements
    try {
        const requiredRole = getRequiredRole();
        const currentPath = getCurrentPath();

        // Jika tidak ada role yang dibutuhkan, izinkan akses
        if (!requiredRole) {
            return;
        }

        // Cek role user
        if (userStr) {
            const user = JSON.parse(userStr);

            // Jika user adalah super_admin, izinkan akses ke semua halaman
            if (user.role === "super_admin") {
                return;
            }

            // Jika user adalah admin, izinkan akses ke halaman admin
            if (
                requiredRole === "admin" &&
                (user.role === "admin" || user.role === "super_admin")
            ) {
                return;
            }

            // Jika halaman adalah product-manager, izinkan akses untuk admin
            if (
                currentPath.includes("product-manager") &&
                (user.role === "admin" || user.role === "super_admin")
            ) {
                return;
            }

            console.log(
                "Authentication passed but insufficient role permissions. Redirecting..."
            );

            // Redirect berdasarkan role user
            if (user.role === "super_admin") {
                window.location.href = "/superadmin/dashboard";
            } else if (user.role === "admin") {
                window.location.href = "/admin/dashboard";
            } else {
                window.location.href = "/user/welcome";
            }
        }
    } catch (e) {
        console.error("Error checking role permissions:", e);
        redirectToLogin();
    }
})();
