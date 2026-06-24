/**
 * Hamburger Menu Toggle Functionality
 * Only JavaScript in the application - handles mobile sidebar toggle
 */

(function () {
    'use strict';

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function () {
        // Create hamburger menu button
        const hamburgerBtn = document.createElement('button');
        hamburgerBtn.className = 'hamburger-menu';
        hamburgerBtn.innerHTML = '☰';
        hamburgerBtn.setAttribute('aria-label', 'Menu');
        document.body.insertBefore(hamburgerBtn, document.body.firstChild);

        // Get sidebar element
        const sidebar = document.querySelector('.sidebar');

        // Create overlay for mobile
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);

        // Function to toggle sidebar
        function toggleSidebar() {
            if (sidebar) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');

                // Prevent body scroll when sidebar is open on mobile
                if (sidebar.classList.contains('mobile-open')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
        }

        // Function to close sidebar
        function closeSidebar() {
            if (sidebar && sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        // Event listener for hamburger button
        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', toggleSidebar);
        }

        // Close sidebar when clicking on overlay
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar when window is resized above mobile breakpoint
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });

        // Close sidebar when clicking on a menu link (optional - improves UX)
        if (sidebar) {
            const menuLinks = sidebar.querySelectorAll('a');
            menuLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });
        }
    });
})();
