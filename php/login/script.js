// Route: panel/login/script.js

document.addEventListener("DOMContentLoaded", () => {

    const card = document.querySelector('.login-card');
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const passwordInput = document.getElementById('passwordInput');

    if (card) {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        });
    }

    const toggleBtn = document.getElementById('togglePasswordBtn');
    const iconOpen = document.getElementById('icon-eye-open');
    const iconClosed = document.getElementById('icon-eye-closed');

    if (toggleBtn && passwordInput && iconOpen && iconClosed) {
        toggleBtn.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            iconOpen.style.display = type === 'password' ? 'block' : 'none';
            iconClosed.style.display = type === 'password' ? 'none' : 'block';
        });
    }

    const lockoutElement = document.getElementById('lockout-timer');
    if (lockoutElement) {
        let remainingTime = parseInt(lockoutElement.getAttribute('data-time'));
        const timerInterval = setInterval(() => {
            remainingTime--;
            if (remainingTime <= 0) {
                clearInterval(timerInterval);
                window.location.reload();
            } else {
                const minutes = Math.floor(remainingTime / 60);
                const seconds = remainingTime % 60;
                lockoutElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
        }, 1000);
    }

    if (loginForm && submitBtn) {
        loginForm.addEventListener('submit', () => {
            const icon = submitBtn.querySelector('i');
            const spinner = submitBtn.querySelector('.spinner');
            const text = submitBtn.querySelector('span');

            if (icon) icon.style.display = 'none';
            if (spinner) spinner.style.display = 'block';
            if (text) text.style.opacity = '0.7';

            submitBtn.style.opacity = '0.8';
            submitBtn.style.cursor = 'wait';
            submitBtn.style.pointerEvents = 'none';
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            if (passwordInput && passwordInput.value.trim() !== "") {
                if (submitBtn && submitBtn.style.pointerEvents !== 'none') {
                    loginForm.requestSubmit();
                }
            }
        }
    });

});

if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}