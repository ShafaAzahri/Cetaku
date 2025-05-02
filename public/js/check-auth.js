/**
 * Script to check authentication on page load
 * This script should be included after auth.js
 */

document.addEventListener("DOMContentLoaded", function () {
    console.log("Checking authentication...");

    // Check if authentication is required
    const authRequiredMeta = document.querySelector(
        'meta[name="auth-required"]'
    );
    if (
        !authRequiredMeta ||
        authRequiredMeta.getAttribute("content") !== "true"
    ) {
        console.log("Authentication not required for this page");
        return;
    }

    // Check if user is logged in
    if (!isLoggedIn()) {
        console.log("User not logged in, redirecting to login");
        window.location.href = "/login";
        return;
    }

    // Check if role is required
    const requiredRoleMeta = document.querySelector(
        'meta[name="required-role"]'
    );
    if (requiredRoleMeta) {
        const requiredRole = requiredRoleMeta.getAttribute("content");
        const user = getCurrentUser();

        if (!user || !user.role || user.role !== requiredRole) {
            console.log(
                `Required role: ${requiredRole}, User role: ${
                    user?.role || "none"
                }`
            );
            console.log(
                "User does not have required role, redirecting to login"
            );
            window.location.href = "/login";
            return;
        }
    }

    console.log("Authentication check passed");
});
