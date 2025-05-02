// Auth utility functions

/**
 * Check if the user is logged in
 * @returns {boolean} True if the user is logged in
 */
function isLoggedIn() {
    return !!sessionStorage.getItem("api_token");
}

/**
 * Get the current user information
 * @returns {Object|null} User information or null if not logged in
 */
function getCurrentUser() {
    const userJson = sessionStorage.getItem("user");
    return userJson ? JSON.parse(userJson) : null;
}

/**
 * Handle logout
 */
function logout() {
    console.log("Logging out...");

    // Submit the logout form
    const logoutForm = document.createElement("form");
    logoutForm.method = "POST";
    logoutForm.action = "/logout";

    // Add CSRF token
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    const csrfInput = document.createElement("input");
    csrfInput.type = "hidden";
    csrfInput.name = "_token";
    csrfInput.value = csrfToken;

    logoutForm.appendChild(csrfInput);
    document.body.appendChild(logoutForm);
    logoutForm.submit();
}

/**
 * Initialize authentication when DOM is loaded
 */
document.addEventListener("DOMContentLoaded", function () {
    // Setup CSRF token for all axios requests
    if (window.axios) {
        window.axios.defaults.headers.common["X-CSRF-TOKEN"] = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
    }

    // Store user data from PHP session to sessionStorage for JS usage
    if (typeof PHP_USER !== "undefined" && PHP_USER) {
        sessionStorage.setItem("user", JSON.stringify(PHP_USER));
    }

    // Store token from PHP session to sessionStorage for JS usage
    if (typeof PHP_TOKEN !== "undefined" && PHP_TOKEN) {
        sessionStorage.setItem("api_token", PHP_TOKEN);
    }
});
