<script>
    (function () {
        // Show / hide password
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.getAttribute('data-toggle-password'));
                if (!input) return;
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.querySelector('i').className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        });

        // Password strength meter (sign-up page)
        var meter = document.querySelector('[data-strength-for]');
        if (meter) {
            var pwd = document.getElementById(meter.getAttribute('data-strength-for'));
            var bars = meter.querySelectorAll('.bars span');
            var label = meter.querySelector('small');
            var levels = [
                { text: '', color: '' },
                { text: 'Weak', color: '#e4002b' },
                { text: 'Fair', color: '#f59e0b' },
                { text: 'Good', color: '#0b5fa5' },
                { text: 'Strong', color: '#00a86b' }
            ];
            pwd.addEventListener('input', function () {
                var v = pwd.value, score = 0;
                if (v.length >= 5) score++;
                if (v.length >= 8) score++;
                if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
                if (/\d/.test(v) && /[^A-Za-z0-9]/.test(v)) score++;
                if (!v) score = 0; else if (score === 0) score = 1;
                bars.forEach(function (bar, i) { bar.style.background = i < score ? levels[score].color : ''; });
                label.textContent = levels[score].text;
                label.style.color = levels[score].color;
            });
        }

        // Prevent double submit
        document.querySelectorAll('form[data-auth-form]').forEach(function (form) {
            form.addEventListener('submit', function () {
                var btn = form.querySelector('.auth-btn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait…';
                }
            });
        });
    })();
</script>
