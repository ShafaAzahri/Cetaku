// public/js/api.js
const API = {
    // Base URL for API requests
    baseUrl: "/api",

    // Get the CSRF token from the meta tag
    getCsrfToken() {
        return document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
    },

    // Get the API token from session storage or session
    getApiToken() {
        return sessionStorage.getItem("api_token") || null;
    },

    // Set default headers for fetch requests
    getHeaders(additionalHeaders = {}) {
        const headers = {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": this.getCsrfToken(),
            ...additionalHeaders,
        };

        const apiToken = this.getApiToken();
        if (apiToken) {
            headers["Authorization"] = `Bearer ${apiToken}`;
        }

        return headers;
    },

    // Generic fetch method with error handling
    async fetch(endpoint, options = {}) {
        try {
            const url = `${this.baseUrl}${endpoint}`;
            const response = await fetch(url, {
                headers: this.getHeaders(options.headers || {}),
                ...options,
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || "An error occurred");
            }

            return data;
        } catch (error) {
            console.error("API Error:", error);
            throw error;
        }
    },

    // GET request
    async get(endpoint) {
        return this.fetch(endpoint);
    },

    // POST request
    async post(endpoint, data) {
        return this.fetch(endpoint, {
            method: "POST",
            body: JSON.stringify(data),
        });
    },

    // PUT request
    async put(endpoint, data) {
        return this.fetch(endpoint, {
            method: "PUT",
            body: JSON.stringify(data),
        });
    },

    // DELETE request
    async delete(endpoint) {
        return this.fetch(endpoint, {
            method: "DELETE",
        });
    },

    // Upload file with multipart/form-data
    async uploadFile(endpoint, formData, method = "POST") {
        return this.fetch(endpoint, {
            method,
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": this.getCsrfToken(),
                // Note: Content-Type is automatically set by the browser when using FormData
            },
            body: formData,
        });
    },
};

// Export for use in other scripts
window.API = API;
