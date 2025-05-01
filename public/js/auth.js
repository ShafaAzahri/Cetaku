/**
 * Authentication Utility Functions
 *
 * This file contains utility functions for authentication in the front-end
 * including login, logout, and checking authentication state.
 */

// Setup CSRF token for all requests
if (document.querySelector('meta[name="csrf-token"]')) {
    axios.defaults.headers.common["X-CSRF-TOKEN"] = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
}

/**
 * Check if user is currently logged in
 *
 * @returns {boolean} True if user is logged in, false otherwise
 */
function isLoggedIn() {
    const token = localStorage.getItem("api_token");
    const expiresAt = localStorage.getItem("expires_at");

    // Debug logging
    console.log("isLoggedIn called");
    console.log("Token:", token);
    console.log("Expires at:", expiresAt);

    return token && new Date(expiresAt) > new Date();
}

/**
 * Get the current authenticated user
 *
 * @returns {Object|null} User object if authenticated, null otherwise
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        const userStr = localStorage.getItem("user");
        if (userStr) {
            return JSON.parse(userStr);
        }
    }
    return null;
}

/**
 * Handle user login
 *
 * @param {string} email User email
 * @param {string} password User password
 * @returns {Promise<boolean>} True if login successful, false otherwise
 */
async function login(email, password) {
    try {
        console.log("Attempting login for:", email);
        const response = await axios.post("/api/auth/login", {
            email: email,
            password: password,
        });

        console.log("Login response:", response.data);

        if (response.data.success) {
            // Store the token and user info in localStorage
            localStorage.setItem("api_token", response.data.api_token);
            localStorage.setItem("user", JSON.stringify(response.data.user));
            localStorage.setItem("expires_at", response.data.expires_at);

            // Clean up any old or redundant storage keys
            cleanupLocalStorage();

            // Redirect to the appropriate page
            window.location.href = response.data.redirect_url;
            return true;
        } else {
            console.error("Login failed:", response.data.message);
            return false;
        }
    } catch (error) {
        console.error("Login error:", error);

        // More detailed error logging
        if (error.response) {
            console.error("Error data:", error.response.data);
            console.error("Error status:", error.response.status);
        } else if (error.request) {
            console.error("Error request:", error.request);
        } else {
            console.error("Error message:", error.message);
        }

        throw error;
    }
}

/**
 * Handle user registration
 *
 * @param {string} nama User full name
 * @param {string} email User email
 * @param {string} password User password
 * @param {string} passwordConfirmation Password confirmation
 * @returns {Promise<boolean>} True if registration successful, false otherwise
 */
async function register(nama, email, password, passwordConfirmation) {
    try {
        const response = await axios.post("/api/auth/register", {
            nama: nama,
            email: email,
            password: password,
            password_confirmation: passwordConfirmation,
        });

        if (response.data.success) {
            // Store the token and user info in localStorage
            localStorage.setItem("api_token", response.data.api_token);
            localStorage.setItem("user", JSON.stringify(response.data.user));
            localStorage.setItem("expires_at", response.data.expires_at);

            // Clean up any old or redundant storage keys
            cleanupLocalStorage();

            // Redirect to the appropriate page
            window.location.href = response.data.redirect_url;
            return true;
        } else {
            return false;
        }
    } catch (error) {
        console.error("Registration error:", error);
        throw error;
    }
}

/**
 * Handle user logout
 *
 * @returns {Promise<boolean>} True if logout successful, false otherwise
 */
async function logout() {
    try {
        const token = localStorage.getItem("api_token");
        if (!token) {
            return false;
        }

        const response = await axios.post(
            "/api/auth/logout",
            {},
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );

        if (response.data.success) {
            // Clear all auth data from localStorage
            clearAuthData();

            // Redirect to login page
            window.location.href = response.data.redirect_url || "/login";
            return true;
        } else {
            return false;
        }
    } catch (error) {
        console.error("Logout error:", error);

        // Force logout even if API call fails
        clearAuthData();
        window.location.href = "/login";
        return false;
    }
}

/**
 * Clear all authentication data from localStorage
 */
function clearAuthData() {
    localStorage.removeItem("api_token");
    localStorage.removeItem("user");
    localStorage.removeItem("expires_at");

    // Also clear any other auth-related keys
    cleanupLocalStorage();
}

/**
 * Clean up redundant or deprecated localStorage keys
 */
function cleanupLocalStorage() {
    const keysToRemove = [
        "user_role",
        "user_name",
        "auth_user",
        "auth_token",
        "auth_expires",
    ];

    keysToRemove.forEach((key) => {
        localStorage.removeItem(key);
    });
}
// Tambahkan ke auth.js
function checkTokenExpiration() {
    const expiresAt = localStorage.getItem("expires_at");

    if (!expiresAt) {
        console.warn("Tanggal kedaluwarsa token tidak ditemukan");
        return false;
    }

    const now = new Date();
    const expiration = new Date(expiresAt);

    return now < expiration;
}

function handleApiError(error) {
    console.error("API Error:", error);

    // Jika error adalah 401 Unauthorized, kemungkinan token tidak valid
    if (error.response && error.response.status === 401) {
        console.log(
            "Token tidak valid atau kedaluwarsa. Mengalihkan ke halaman login..."
        );
        logout();
    }
}
