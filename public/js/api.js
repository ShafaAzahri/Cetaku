// public/js/api.js
const baseURL = window.location.origin;

// Buat instance Axios untuk API calls
const api = axios.create({
    baseURL: baseURL,
    headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
    },
});

// Tambahkan token ke setiap request
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("api_token");
        if (token) {
            config.headers["Authorization"] = `Bearer ${token}`;
            // Tambahkan CSRF Token jika diperlukan
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");
            if (csrfToken) {
                config.headers["X-CSRF-TOKEN"] = csrfToken;
            }
        }
        return config;
    },
    (error) => {
        console.error("Request error:", error);
        return Promise.reject(error);
    }
);

// Handle response dan error
api.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        console.error("API Error:", error);

        // Handle 401 Unauthorized - token tidak valid atau kedaluwarsa
        if (error.response && error.response.status === 401) {
            console.log(
                "Token tidak valid atau kedaluwarsa. Mengalihkan ke halaman login..."
            );
            // Clear localStorage dan redirect ke login
            localStorage.removeItem("api_token");
            localStorage.removeItem("user");
            localStorage.removeItem("expires_at");
            window.location.href = "/login";
            return Promise.reject(error);
        }

        // Handle 403 Forbidden - tidak memiliki izin
        if (error.response && error.response.status === 403) {
            console.log("Tidak memiliki izin untuk mengakses resource ini.");
            return Promise.reject(error);
        }

        // Handle 422 Validation Error
        if (error.response && error.response.status === 422) {
            console.log("Validation error:", error.response.data.errors);
            return Promise.reject(error);
        }

        // Handle 500 Server Error
        if (error.response && error.response.status >= 500) {
            console.log("Server error. Silakan coba lagi nanti.");
            return Promise.reject(error);
        }

        return Promise.reject(error);
    }
);

// Fungsi untuk memeriksa apakah token masih valid
function isTokenValid() {
    const token = localStorage.getItem("api_token");
    const expiresAt = localStorage.getItem("expires_at");

    if (!token || !expiresAt) {
        return false;
    }

    const now = new Date();
    const expiration = new Date(expiresAt);

    return now < expiration;
}

// Fungsi untuk memeriksa autentikasi
function checkAuth() {
    if (!isTokenValid()) {
        // Clear localStorage dan redirect ke login
        localStorage.removeItem("api_token");
        localStorage.removeItem("user");
        localStorage.removeItem("expires_at");
        window.location.href = "/login";
        return false;
    }

    return true;
}

// Inisialisasi - set token dari localStorage jika ada
document.addEventListener("DOMContentLoaded", function () {
    console.log("API.js loaded - Setting up headers");

    // Check auth on page load
    checkAuth();

    // Set token ke axios default headers jika ada
    const token = localStorage.getItem("api_token");
    if (token) {
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        console.log("Token set in axios defaults");
    }
});
