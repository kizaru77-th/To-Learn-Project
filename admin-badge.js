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
                badge.style.cssText = 'display:inline-block; margin:-2px 0 6px; padding:2px 7px; border-radius:999px; background:#fff3cd; color:#8a5a00; font-size:10px; font-weight:600; letter-spacing:.2px;';
                logoutButton.insertAdjacentElement('beforebegin', badge);
            };

            addBadge();
            new MutationObserver(addBadge).observe(document.body, { childList: true, subtree: true });
        } catch (error) {
            console.warn('Unable to verify developer admin access.', error);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showAdminBadge);
    } else {
        showAdminBadge();
    }
})();
