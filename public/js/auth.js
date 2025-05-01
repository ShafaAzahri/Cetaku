/**
 * Auth.js - File untuk menangani autentikasi menggunakan API
 */

// Fungsi untuk login dan mendapatkan token
async function login(email, password) {
    try {
        const response = await axios.post("/api/auth/login", {
            email: email,
            password: password,
        });

        if (response.data.success) {
            // Simpan token ke localStorage
            localStorage.setItem("auth_token", response.data.api_token);
            localStorage.setItem("auth_expires", response.data.expires_at);
            localStorage.setItem(
                "auth_user",
                JSON.stringify(response.data.user)
            );

            // Redirect ke halaman sesuai role
            window.location.href = response.data.redirect_url;
            return true;
        } else {
            return false;
        }
    } catch (error) {
        console.error("Login failed:", error);
        return false;
    }
}

// Fungsi untuk register dan mendapatkan token
async function register(nama, email, password, password_confirmation) {
    try {
        const response = await axios.post("/api/auth/register", {
            nama: nama,
            email: email,
            password: password,
            password_confirmation: password_confirmation,
        });

        if (response.data.success) {
            // Simpan token ke localStorage
            localStorage.setItem("auth_token", response.data.api_token);
            localStorage.setItem("auth_expires", response.data.expires_at);
            localStorage.setItem(
                "auth_user",
                JSON.stringify(response.data.user)
            );

            // Redirect ke halaman user
            window.location.href = response.data.redirect_url;
            return true;
        } else {
            return false;
        }
    } catch (error) {
        console.error("Registration failed:", error);
        return false;
    }
}

// Fungsi untuk logout
async function logout() {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) {
            window.location.href = "/";
            return;
        }

        // Set token di header request
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

        const response = await axios.post("/api/auth/logout");

        // Hapus semua data autentikasi dari localStorage
        localStorage.removeItem("auth_token");
        localStorage.removeItem("auth_expires");
        localStorage.removeItem("auth_user");

        // Redirect ke halaman home
        window.location.href = response.data.redirect_url || "/";
    } catch (error) {
        console.error("Logout failed:", error);
        // Hapus data autentikasi dan redirect ke home jika terjadi error
        localStorage.removeItem("auth_token");
        localStorage.removeItem("auth_expires");
        localStorage.removeItem("auth_user");
        window.location.href = "/";
    }
}

// Fungsi untuk mengecek apakah user sudah login
function isLoggedIn() {
    const token = localStorage.getItem("auth_token");
    const expires = localStorage.getItem("auth_expires");

    if (!token || !expires) {
        return false;
    }

    // Cek apakah token sudah expired
    const now = new Date();
    const expiryDate = new Date(expires);

    return now < expiryDate;
}

// Fungsi untuk mendapatkan user yang login
function getCurrentUser() {
    const userJson = localStorage.getItem("auth_user");
    if (!userJson) {
        return null;
    }

    return JSON.parse(userJson);
}

// Fungsi untuk mengecek role user
function hasRole(roleName) {
    const user = getCurrentUser();
    return user && user.role === roleName;
}

// Fungsi untuk cek autentikasi dan redirect jika perlu
async function checkAuth() {
    if (!isLoggedIn()) {
        // Jika tidak login dan berada di halaman yang memerlukan auth, redirect ke login
        if (
            window.location.pathname !== "/" &&
            window.location.pathname !== "/login" &&
            window.location.pathname !== "/register"
        ) {
            window.location.href = "/login";
        }
        return;
    }

    // Jika sudah login, cek apakah memiliki akses ke halaman saat ini
    const user = getCurrentUser();

    // Redirect jika user berada di halaman yang tidak sesuai role
    if (user) {
        // Path untuk admin
        if (window.location.pathname.startsWith("/admin/")) {
            if (user.role !== "admin" && user.role !== "superadmin") {
                window.location.href = "/user/welcome";
            }
        }

        // Path untuk superadmin
        if (window.location.pathname.startsWith("/superadmin/")) {
            if (user.role !== "superadmin") {
                window.location.href = "/user/welcome";
            }
        }

        // Jika berada di halaman login/register tapi sudah login, redirect ke halaman sesuai role
        if (
            window.location.pathname === "/login" ||
            window.location.pathname === "/register"
        ) {
            if (user.role === "superadmin") {
                window.location.href = "/superadmin/dashboard";
            } else if (user.role === "admin") {
                window.location.href = "/admin/dashboard";
            } else {
                window.location.href = "/user/welcome";
            }
        }
    }
}

// Pastikan token selalu disertakan di setiap request API
document.addEventListener("DOMContentLoaded", function () {
    const token = localStorage.getItem("auth_token");
    if (token) {
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    }

    // Cek autentikasi pada load halaman
    checkAuth();
});
