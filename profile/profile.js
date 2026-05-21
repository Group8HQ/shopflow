// profile.js

document.addEventListener('DOMContentLoaded', function () {

    // ── Edit / Cancel toggle ──────────────────────────────────────
    const editBtn   = document.getElementById('edit-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const infoView  = document.getElementById('info-view');
    const infoForm  = document.getElementById('info-form');

    if (editBtn && cancelBtn && infoView && infoForm) {
        editBtn.addEventListener('click', function () {
            infoView.classList.add('hidden');
            infoForm.classList.remove('hidden');
        });

        cancelBtn.addEventListener('click', function () {
            infoForm.classList.add('hidden');
            infoView.classList.remove('hidden');
        });
    }

    // ── Password visibility toggles ──────────────────────────────
    document.querySelectorAll('.toggle-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = btn.getAttribute('data-target');
            const input    = document.getElementById(targetId);

            if (!input) return;

            if (input.type === 'password') {
                input.type   = 'text';
                btn.textContent = '🙈';
            } else {
                input.type   = 'password';
                btn.textContent = '👁';
            }
        });
    });

    // ── Mark active nav item ──────────────────────────────────────
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-item').forEach(function (link) {
        if (currentPath.includes(link.getAttribute('href').split('/').slice(-2, -1)[0])) {
            link.classList.add('active');
        }
    });

    // ── Auto-hide alerts after 4 seconds ─────────────────────────
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.4s';
            alert.style.opacity    = '0';
            setTimeout(function () { alert.remove(); }, 400);
        }, 4000);
    });

});
