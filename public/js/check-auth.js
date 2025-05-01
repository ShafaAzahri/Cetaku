/**
 * Script pengecekan otentikasi yang sudah diperbaiki
 * File ini menangani pengecekan otentikasi dan pengalihan halaman
 * dengan mempertimbangkan akses halaman berdasarkan role
 */

// Fungsi untuk mengecek apakah user sudah login
function isLoggedIn() {
    const token = localStorage.getItem("api_token");
    const expiresAt = localStorage.getItem("expires_at");

    if (!token || !expiresAt) {
        return false;
    }

    // Cek apakah token sudah expired
    return new Date(expiresAt) > new Date();
}

// Fungsi untuk mendapatkan data user saat ini
function getCurrentUser() {
    const userStr = localStorage.getItem("user");
    if (!userStr) {
        return null;
    }

    try {
        return JSON.parse(userStr);
    } catch (e) {
        console.error("Error parsing user data:", e);
        return null;
    }
}

// Fungsi untuk mengecek role user
function checkUserRole(allowedRoles) {
    const user = getCurrentUser();

    if (!user || !user.role) {
        return false;
    }

    // Jika user adalah super_admin, izinkan akses ke semua halaman
    if (user.role === "super_admin") {
        return true;
    }

    // Cek apakah role user termasuk dalam role yang diizinkan
    return allowedRoles.includes(user.role);
}

// Fungsi untuk menangani redirect berdasarkan status otentikasi dan role
function handleAuthCheck() {
    // Jika user belum login, redirect ke halaman login
    if (!isLoggedIn()) {
        window.location.href = "/login";
        return false;
    }

    // Dapatkan current path
    const currentPath = window.location.pathname;

    // Cek meta tag untuk role yang dibutuhkan
    const requiredRoleElement = document.querySelector(
        'meta[name="required-role"]'
    );

    // Jika tidak ada meta tag required-role, izinkan akses
    if (!requiredRoleElement) {
        return true;
    }

    const requiredRole = requiredRoleElement.content;

    // Buat array role yang diizinkan berdasarkan required-role
    let allowedRoles = [];

    if (requiredRole === "admin") {
        allowedRoles = ["admin", "super_admin"];
    } else if (requiredRole === "super_admin") {
        allowedRoles = ["super_admin"];
    } else if (requiredRole === "user") {
        allowedRoles = ["user", "admin", "super_admin"];
    }

    // Cek apakah user memiliki role yang diizinkan
    if (!checkUserRole(allowedRoles)) {
        const user = getCurrentUser();
        // Redirect berdasarkan role user
        if (user && user.role) {
            if (user.role === "super_admin") {
                window.location.href = "/superadmin/dashboard";
            } else if (user.role === "admin") {
                window.location.href = "/admin/dashboard";
            } else {
                window.location.href = "/user/welcome";
            }
        } else {
            window.location.href = "/login";
        }
        return false;
    }

    return true;
}

// Tambahkan event listener untuk DOMContentLoaded
document.addEventListener("DOMContentLoaded", function () {
    // Cek apakah halaman membutuhkan otentikasi
    const authRequiredElement = document.querySelector(
        'meta[name="auth-required"]'
    );

    if (authRequiredElement && authRequiredElement.content === "true") {
        // Jalankan pengecekan otentikasi
        handleAuthCheck();
    }

    // Update nama user jika ada
    const user = getCurrentUser();
    if (user) {
        const userNameElements = document.querySelectorAll(".user-name");
        userNameElements.forEach((element) => {
            element.textContent = user.nama;
        });

        // Update avatar jika ada
        const avatarImgElements = document.querySelectorAll("#user-avatar");
        if (avatarImgElements.length > 0) {
            const initials = user.nama
                .split(" ")
                .map((name) => name.charAt(0))
                .join("")
                .substring(0, 2);

            avatarImgElements.forEach((element) => {
                element.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(
                    initials
                )}&background=4361ee&color=fff`;
            });
        }
    }

    // Handle logout
    const logoutButtons = document.querySelectorAll(".logout-button");
    logoutButtons.forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            logout();
        });
    });
});

// Fungsi untuk logout
function logout() {
    const token = localStorage.getItem("api_token");

    if (!token) {
        // Jika tidak ada token, langsung hapus data dan redirect
        clearAuthData();
        window.location.href = "/login";
        return;
    }

    // Kirim request logout ke server
    fetch("/api/auth/logout", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
            "X-CSRF-TOKEN":
                document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content") || "",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            console.log("Logout successful:", data);
        })
        .catch((error) => {
            console.error("Logout error:", error);
        })
        .finally(() => {
            // Hapus data autentikasi dari localStorage
            clearAuthData();
            // Redirect ke halaman login
            window.location.href = "/login";
        });
}

// Fungsi untuk menghapus data autentikasi
function clearAuthData() {
    localStorage.removeItem("api_token");
    localStorage.removeItem("user");
    localStorage.removeItem("expires_at");
}
