// ============================================================
// REGISTER PAGE — validation + confirmation modal
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    const form        = document.getElementById('registerForm');
    const submitBtn   = document.getElementById('registerSubmitBtn');
    const confirmBtn  = document.getElementById('confirmRegisterBtn');

    if (submitBtn) {
        submitBtn.addEventListener('click', function () {
            if (validateRegisterForm()) {
                const modal = new bootstrap.Modal(document.getElementById('confirmRegistrationModal'));
                modal.show();
            }
        });
    }

    if (confirmBtn && form) {
        confirmBtn.addEventListener('click', function () { form.submit(); });
    }
});

function validateRegisterForm() {
    document.querySelectorAll('.invalid-feedback.dynamic').forEach(el => el.remove());
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    const errors = {};
    const firstName = document.getElementById('first_name');
    const lastName  = document.getElementById('last_name');
    const email     = document.getElementById('email');
    const phone     = document.getElementById('phone');
    const username  = document.getElementById('register_username');
    const password  = document.getElementById('register_password');
    const confirm   = document.getElementById('confirm_password');

    if (!firstName.value.trim()) errors.first_name = 'First name is required';
    else if (firstName.value.length > 50) errors.first_name = 'First name cannot exceed 50 characters';

    if (!lastName.value.trim()) errors.last_name = 'Last name is required';
    else if (lastName.value.length > 50) errors.last_name = 'Last name cannot exceed 50 characters';

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value.trim()) errors.email = 'Email address is required';
    else if (!emailRegex.test(email.value.trim())) errors.email = 'Please enter a valid email address';

    const phoneRegex = /^(\+63|0)[0-9]{10}$/;
    if (!phone.value.trim()) errors.phone = 'Phone number is required';
    else if (!phoneRegex.test(phone.value.trim())) errors.phone = 'Enter a valid Philippine number (+63xxxxxxxxxx or 0xxxxxxxxxx)';

    const usernamePattern = /^[A-Za-z0-9]{3,16}$/;
    if (!username.value.trim()) errors.username = 'Username is required';
    else if (!usernamePattern.test(username.value.trim())) errors.username = 'Must be 3-16 characters (letters & numbers only)';

    const pwd = password.value;
    if (!pwd) errors.password = 'Password is required';
    else {
        if (pwd.length < 8 || pwd.length > 64) errors.password = 'Password must be 8-64 characters';
        else if (!/[A-Z]/.test(pwd)) errors.password = 'Must contain at least one uppercase letter';
        else if (!/[0-9]/.test(pwd)) errors.password = 'Must contain at least one number';
        else if (!/[!@#$%^&*(),.?":{}|<>]/.test(pwd)) errors.password = 'Must contain at least one special character';
    }

    if (pwd !== confirm.value) errors.confirm_password = 'Passwords do not match';

    const fieldMap = {
        first_name: firstName, last_name: lastName,
        email: email, phone: phone, username: username,
        password: password, confirm_password: confirm
    };

    for (const key in errors) {
        const input = fieldMap[key];
        if (!input) continue;
        input.classList.add('is-invalid');
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback dynamic';
        feedback.textContent = errors[key];
        const parent = input.closest('.input-group') || input.parentNode;
        parent.parentNode.insertBefore(feedback, parent.nextSibling);
    }

    return Object.keys(errors).length === 0;
}