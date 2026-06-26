/**
 * Hamburger Menu + Dark Mode Toggle
 * Handles mobile sidebar toggle and theme switching
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-backdrop');
        const hamburgerBtn = document.querySelector('.btn-burger');

        // Function to open sidebar
        function openSidebar() {
            if (sidebar) {
                sidebar.classList.add('show');
                if (overlay) overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        // Function to close sidebar
        function closeSidebar() {
            if (sidebar) {
                sidebar.classList.remove('show');
                if (overlay) overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        // Hamburger button click
        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', openSidebar);
        }

        // Close sidebar when clicking on overlay
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar when clicking on a menu link (mobile)
        if (sidebar) {
            sidebar.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });
        }

        // Close sidebar when window is resized above mobile breakpoint
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });

        // Close sidebar on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });
    });
})();