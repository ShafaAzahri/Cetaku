// File auth.js
// Versi minimal untuk menghindari redirect loop

// Simpan token di localStorage
function setAuthToken(token, expiresAt, user) {
    localStorage.setItem("api_token", token);
    localStorage.setItem("expires_at", expiresAt);
    localStorage.setItem("user", JSON.stringify(user));
}

// Hapus token dari localStorage
function clearAuthToken() {
    localStorage.removeItem("api_token");
    localStorage.removeItem("expires_at");
    localStorage.removeItem("user");
}

// Cek apakah user sudah login
function isLoggedIn() {
    console.log("isLoggedIn called");
    const token = localStorage.getItem("api_token");
    const expiresAt = localStorage.getItem("expires_at");

    // Debug
    console.log("Token:", token);
    console.log("Expires at:", expiresAt);

    // Cek jika token dan tanggal kedaluwarsa ada
    if (!token || !expiresAt) {
        console.log("No token or expiry found");
        return false;
    }

    // Cek jika token sudah kedaluwarsa
    if (new Date(expiresAt) < new Date()) {
        console.log("Token expired");
        return false;
    }

    return true;
}

// Dapatkan data user dari localStorage
function getCurrentUser() {
    const userString = localStorage.getItem("user");
    if (!userString) {
        return null;
    }
    return JSON.parse(userString);
}

// Fungsi login
async function login(email, password) {
    try {
        const response = await axios.post("/api/auth/login", {
            email: email,
            password: password,
        });

        if (response.data.success) {
            setAuthToken(
                response.data.api_token,
                response.data.expires_at,
                response.data.user
            );

            // Redirect ke halaman yang sesuai
            if (response.data.redirect_url) {
                window.location.href = response.data.redirect_url;
            }

            return true;
        }

        return false;
    } catch (error) {
        console.error("Login error:", error);
        throw error;
    }
}

// Fungsi register
async function register(nama, email, password, password_confirmation) {
    try {
        const response = await axios.post("/api/auth/register", {
            nama: nama,
            email: email,
            password: password,
            password_confirmation: password_confirmation,
        });

        if (response.data.success) {
            setAuthToken(
                response.data.api_token,
                response.data.expires_at,
                response.data.user
            );

            // Redirect ke halaman yang sesuai
            if (response.data.redirect_url) {
                window.location.href = response.data.redirect_url;
            }

            return true;
        }

        return false;
    } catch (error) {
        console.error("Registration error:", error);
        throw error;
    }
}

// Fungsi logout
async function logout() {
    console.log("Logout called");
    try {
        const token = localStorage.getItem("api_token");

        if (token) {
            await axios.post(
                "/api/auth/logout",
                {},
                {
                    headers: {
                        Authorization: "Bearer " + token,
                    },
                }
            );
        }

        clearAuthToken();
        window.location.href = "/login";

        return true;
    } catch (error) {
        console.error("Logout error:", error);

        // Hapus token meskipun API gagal
        clearAuthToken();
        window.location.href = "/login";

        return false;
    }
}
