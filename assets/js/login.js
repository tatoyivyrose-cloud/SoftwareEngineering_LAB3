// ============================================================
// LOGIN PAGE SCRIPTS
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // -------- Password visibility toggle --------
    document.querySelectorAll('.btn-toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const group = this.closest('.input-group');
            const input = group ? group.querySelector('input') : null;
            if (!input) return;

            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

});