/**
 * Main JavaScript file
 * Contains all the interactive functionality for the website
 */

document.addEventListener("DOMContentLoaded", function () {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="popover"]')
    );
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Navbar scroll behavior
    const navbar = document.querySelector(".navbar");

    if (navbar) {
        window.addEventListener("scroll", function () {
            if (window.scrollY > 50) {
                navbar.classList.add("navbar-scrolled");
            } else {
                navbar.classList.remove("navbar-scrolled");
            }
        });
    }

    // Handle Chat Admin button
    const chatAdminButtons = document.querySelectorAll(
        ".chat-admin, .chat-admin-link"
    );

    chatAdminButtons.forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();

            // Open chat window or redirect to chat page
            // This is a placeholder - replace with your actual chat functionality
            alert("Fitur chat admin akan segera tersedia!");
        });
    });

    // Product card hover effect
    const productCards = document.querySelectorAll(".product-card");

    productCards.forEach((card) => {
        card.addEventListener("mouseenter", function () {
            this.querySelector(".product-img img").style.transform =
                "scale(1.05)";
        });

        card.addEventListener("mouseleave", function () {
            this.querySelector(".product-img img").style.transform = "scale(1)";
        });
    });

    // Handle Order button
    const orderButton = document.querySelector(".btn-order");

    if (orderButton) {
        orderButton.addEventListener("click", function (e) {
            e.preventDefault();

            // Redirect to order page or show modal
            // This is a placeholder - replace with your actual order functionality
            window.location.href = "/products";
        });
    }

    // Animate elements on scroll
    const animateElements = document.querySelectorAll(".animate-on-scroll");

    function checkIfInView() {
        const windowHeight = window.innerHeight;
        const windowTopPosition = window.scrollY;
        const windowBottomPosition = windowTopPosition + windowHeight;

        animateElements.forEach((element) => {
            const elementHeight = element.offsetHeight;
            const elementTopPosition = element.offsetTop;
            const elementBottomPosition = elementTopPosition + elementHeight;

            // Check if element is in view
            if (
                elementBottomPosition >= windowTopPosition &&
                elementTopPosition <= windowBottomPosition
            ) {
                element.classList.add("animate");
            }
        });
    }

    window.addEventListener("scroll", checkIfInView);
    window.addEventListener("resize", checkIfInView);

    // Trigger on initial load
    window.addEventListener("load", checkIfInView);

    // Set up CSRF token for AJAX requests
    const token = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    if (token) {
        // Set for Axios if you're using it
        if (typeof axios !== "undefined") {
            axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
        }

        // Set for jQuery Ajax if you're using it
        if (typeof jQuery !== "undefined") {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": token,
                },
            });
        }
    }
});
