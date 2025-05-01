// File check-auth.js
console.log("Check-auth.js loaded successfully");

document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM loaded in check-auth.js");

    // Dapatkan token dari localStorage
    const token = localStorage.getItem("api_token");

    // Jika tidak ada token, redirect ke login
    if (!token) {
        console.log("No token found in check-auth.js");
        // Nonaktifkan redirect untuk debugging
        // window.location.href = '/login';
        return;
    }

    // Cek tanggal kedaluwarsa
    const expiresAt = localStorage.getItem("expires_at");
    if (expiresAt && new Date(expiresAt) < new Date()) {
        console.log("Token expired in check-auth.js");
        localStorage.removeItem("api_token");
        localStorage.removeItem("expires_at");
        localStorage.removeItem("user");
        // Nonaktifkan redirect untuk debugging
        // window.location.href = '/login';
        return;
    }

    // Dapatkan data user dari localStorage
    const userString = localStorage.getItem("user");
    if (userString) {
        const user = JSON.parse(userString);
        console.log("User logged in:", user.nama);

        // Tampilkan nama user di sidebar
        const userNameElements = document.querySelectorAll(".user-name");
        userNameElements.forEach((el) => {
            el.textContent = user.nama;
        });
    }
});
