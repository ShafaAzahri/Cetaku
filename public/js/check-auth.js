/**
 * Script untuk memeriksa status autentikasi pada halaman awal
 * dan mengarahkan pengguna ke halaman yang sesuai dengan role-nya
 */

document.addEventListener("DOMContentLoaded", function () {
    // Ambil token dari localStorage
    const token = localStorage.getItem("auth_token");

    // Jika token tersedia, cek validitasnya dan dapatkan info user
    if (token) {
        // Setup token untuk request API
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

        // Cek apakah token sudah expired
        const expires = localStorage.getItem("auth_expires");
        if (expires) {
            const now = new Date();
            const expiryDate = new Date(expires);

            if (now > expiryDate) {
                // Token sudah expired, hapus data autentikasi
                localStorage.removeItem("auth_token");
                localStorage.removeItem("auth_expires");
                localStorage.removeItem("auth_user");
                return;
            }
        }

        // Dapatkan info user dari localStorage
        const userJson = localStorage.getItem("auth_user");
        if (userJson) {
            const user = JSON.parse(userJson);

            // Redirect berdasarkan role
            if (user.role === "superadmin") {
                window.location.href = "/superadmin/dashboard";
            } else if (user.role === "admin") {
                window.location.href = "/admin/dashboard";
            } else {
                window.location.href = "/user/welcome";
            }
        } else {
            // Jika data user tidak ada di localStorage tapi token masih valid
            // Ambil data user dari API
            axios
                .get("/api/auth/user")
                .then((response) => {
                    if (response.data.success) {
                        localStorage.setItem(
                            "auth_user",
                            JSON.stringify(response.data.user)
                        );

                        // Redirect berdasarkan role
                        if (response.data.user.role === "superadmin") {
                            window.location.href = "/superadmin/dashboard";
                        } else if (response.data.user.role === "admin") {
                            window.location.href = "/admin/dashboard";
                        } else {
                            window.location.href = "/user/welcome";
                        }
                    }
                })
                .catch((error) => {
                    console.error("Error fetching user data:", error);
                    // Hapus token jika ada error (token tidak valid)
                    localStorage.removeItem("auth_token");
                    localStorage.removeItem("auth_expires");
                    localStorage.removeItem("auth_user");
                });
        }
    }
});
