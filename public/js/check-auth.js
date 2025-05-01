/**
 * Authentication Check and Route Protection
 *
 * This script runs on page load to:
 * 1. Check if user is authenticated
 * 2. Redirect users based on their role if needed
 * 3. Update UI elements with user information
 */

console.log("Check-auth.js loaded successfully");

document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM loaded in check-auth.js");

    // First check if token is expired
    const expiresAt = localStorage.getItem("expires_at");
    if (expiresAt && new Date(expiresAt) < new Date()) {
        console.log("Token expired, logging out");
        clearAuthData();
        window.location.href = "/login";
        return;
    }

    // Check authentication status
    if (isLoggedIn()) {
        const user = getCurrentUser();
        if (!user) {
            console.log("No user data found even though token exists");
            clearAuthData();
            window.location.href = "/login";
            return;
        }

        console.log("User logged in:", user.nama);
        console.log("Complete user object:", JSON.stringify(user));
        console.log("User role is:", user.role);

        // Update UI elements with user's name
        updateUIWithUserInfo(user);

        // Check current path to avoid unnecessary redirects
        const currentPath = window.location.pathname;

        // Handle role-based access and redirections
        if (
            currentPath === "/login" ||
            currentPath === "/register" ||
            currentPath === "/"
        ) {
            // If user is already logged in and on auth pages, redirect based on role
            handleRoleBasedRedirect(user.role);
        } else {
            // Otherwise, just verify they have access to current page
            checkAccessToCurrentPage(user.role, currentPath);
        }
    } else {
        // User is not logged in
        const publicPaths = ["/login", "/register", "/password/reset"];
        const currentPath = window.location.pathname;

        // If trying to access a protected route, redirect to login
        if (!publicPaths.includes(currentPath) && currentPath !== "/") {
            console.log("Unauthorized access attempt, redirecting to login");
            window.location.href = "/login";
        }
    }
});

/**
 * Update UI elements with user information
 *
 * @param {Object} user User object
 */
function updateUIWithUserInfo(user) {
    // Update user name in UI
    const userNameElements = document.querySelectorAll(".user-name");
    userNameElements.forEach((el) => {
        el.textContent = user.nama;
    });

    // Update user avatar if applicable
    const avatarElements = document.querySelectorAll(
        ".user-avatar, #user-avatar"
    );
    avatarElements.forEach((el) => {
        const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(
            user.nama
        )}&background=4361ee&color=fff`;
        if (el.tagName.toLowerCase() === "img") {
            el.src = avatarUrl;
        } else {
            el.style.backgroundImage = `url(${avatarUrl})`;
        }
    });
}

/**
 * Redirect user based on their role
 *
 * @param {string} role User role
 */
function handleRoleBasedRedirect(role) {
    console.log("Handling redirect for role:", role);

    if (role === "super_admin") {
        console.log("Redirecting to superadmin dashboard");
        window.location.href = "/superadmin/dashboard";
    } else if (role === "admin") {
        console.log("Redirecting to admin dashboard");
        window.location.href = "/admin/dashboard";
    } else {
        console.log("Redirecting to user welcome");
        window.location.href = "/user/welcome";
    }
}

/**
 * Check if user has access to current page based on their role
 *
 * @param {string} role User role
 * @param {string} currentPath Current URL path
 */
function checkAccessToCurrentPage(role, currentPath) {
    // Check if user is trying to access a page they don't have permission for
    if (currentPath.startsWith("/superadmin/") && role !== "super_admin") {
        console.log("Unauthorized access to superadmin area");
        handleRoleBasedRedirect(role);
    } else if (
        currentPath.startsWith("/admin/") &&
        role !== "super_admin" &&
        role !== "admin"
    ) {
        console.log("Unauthorized access to admin area");
        handleRoleBasedRedirect(role);
    }
}
