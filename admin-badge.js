(() => {
    function logoutFromServer() {
        localStorage.removeItem('isLoggedIn');
        localStorage.removeItem('username');
        localStorage.removeItem('profilePicture');
        navigator.sendBeacon('logout.php');
        window.location.href = 'index.html';
    }

    // The original pages only clear localStorage. Replace their global logout
    // handlers so the persistent server cookie is cleared as well.
    window.logout = logoutFromServer;
    window.performLogout = logoutFromServer;

    function centerProfileActions() {
        document.querySelectorAll('#auth-section').forEach((authSection) => {
            const adminBadge = authSection.querySelector('[data-developer-admin-badge]');
            const logoutButton = authSection.querySelector('button[onclick="showLogoutConfirm()"]');

            if (adminBadge) {
                adminBadge.style.display = 'table';
                adminBadge.style.margin = '0 auto 6px';
            }

            if (logoutButton) {
                logoutButton.style.display = 'block';
                logoutButton.style.margin = '0 auto';
            }
        });
    }

    async function showAdminBadge() {
        try {
            const response = await fetch('admin-access.php', {
                credentials: 'same-origin',
                cache: 'no-store'
            });
            const access = await response.json();
            if (access.isAdmin !== true) return;

            // This is only a UI marker. Access is still verified by the server.
            window.isDeveloperAdmin = true;
            const addBadge = () => {
                const authSection = document.getElementById('auth-section');
                if (!authSection || authSection.querySelector('[data-developer-admin-badge]')) return;

                const logoutButton = authSection.querySelector('button[onclick="showLogoutConfirm()"]');
                if (!logoutButton) return;

                const badge = document.createElement('div');
                badge.dataset.developerAdminBadge = 'true';
                badge.textContent = 'ผู้ดูแลระบบ';
                badge.style.cssText = 'display:table; margin:0 auto 6px; padding:2px 7px; border-radius:999px; background:#fff3cd; color:#8a5a00; font-size:10px; font-weight:600; letter-spacing:.2px;';
                logoutButton.insertAdjacentElement('beforebegin', badge);
                centerProfileActions();
            };

            addBadge();
        } catch (error) {
            console.warn('Unable to verify developer admin access.', error);
        }
    }

    function initializeProfileUI() {
        centerProfileActions();
        new MutationObserver(centerProfileActions).observe(document.body, { childList: true, subtree: true });
        showAdminBadge();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeProfileUI);
    } else {
        initializeProfileUI();
    }
})();
