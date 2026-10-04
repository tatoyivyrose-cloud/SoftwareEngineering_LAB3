<?php
require_once 'includes/register_functions.php';

if (isLoggedIn()) redirectByRole($_SESSION['role'] ?? '');

$field_errors = [];
$values = [
    'first_name' => '', 'last_name' => '', 'email' => '',
    'phone' => '', 'username' => '', 'gender' => '',
    'dob' => '', 'address' => '',
    'password' => '', 'confirm_password' => ''
];

$csrf_token = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $field_errors['general'] = 'Invalid request. Please try again.';
    } else {
        $csrf_token = refreshCsrfToken();

        foreach ($values as $key => $_) {
            $values[$key] = ($key === 'password' || $key === 'confirm_password')
                ? ($_POST[$key] ?? '')
                : sanitize($_POST[$key] ?? '');
        }

        $field_errors = validateRegistration($values);

        if (empty($field_errors)) {
            $created = createUser($values);
            if ($created) {
                writeLoginAudit(
                    $created['user_id'], $values['username'],
                    $values['first_name'] . ' ' . $values['last_name']
                );
                $_SESSION['temp_recovery'] = $created['recovery_code'];
                header("Location: register-success.php");
                exit();
            } else {
                $field_errors['general'] = 'Database error. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth.css">

    <style>
        :root {
            --auth-card-height: 640px;   /* was 560 — bumped so step 4 fits */
        }

        .login-container.auth-card {
            display: flex;
            flex-direction: column;
            height: var(--auth-card-height);
            min-height: var(--auth-card-height);
            max-height: none;
            overflow: hidden;
        }

        .login-container.auth-card .login-header { flex: 0 0 auto; }

        .auth-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;   /* was hidden — safety net if content ever overflows */
            overflow-x: hidden;
            padding: 4px 2px;
        }

        .step-heading {
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #00695C;
            margin-bottom: 14px;
        }

        .auth-body .form-label {
            margin-bottom: 4px;
            font-size: .875rem;
        }
        .auth-body .form-control {
            padding: 8px 12px;
            font-size: .95rem;
        }
        .auth-body .mb-3 { margin-bottom: 14px !important; }

        .auth-footer {
            flex: 0 0 auto;
            padding-top: 12px;
            margin-top: 8px;
            border-top: 1px solid rgba(0, 0, 0, .08);
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .step-indicator .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #cfd8dc;
            transition: all .25s ease;
        }
        .step-indicator .dot.active {
            background: #00695C;
            width: 22px;
            border-radius: 4px;
        }
        .step-indicator .dot.done { background: #4db6ac; }

        .auth-footer .nav-buttons {
            display: flex;
            gap: 8px;
        }
        .auth-footer .nav-buttons .btn-login {
            flex: 1 1 auto;
            width: auto;
        }
        .auth-footer .nav-buttons .btn-back {
            flex: 0 0 auto;
            padding: 10px 16px;
            border-radius: 8px;
            border: 1px solid #cfd8dc;
            background: #fff;
            color: #455a64;
            font-weight: 500;
            transition: background .15s ease;
        }
        .auth-footer .nav-buttons .btn-back:hover { background: #eceff1; }
        .auth-footer .auth-footer-link {
            text-align: center;
            margin-top: 12px;
        }

        /* Error toasts (5s auto-dismiss) — centered over the form */
        .toast-stack {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1080;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            width: min(340px, calc(100vw - 40px));
            pointer-events: none;
        }
        .error-toast {
            pointer-events: auto;
            width: 100%;
            background: #fff;
            border-left: 4px solid #dc3545;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
            padding: 12px 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            transform-origin: center center;
            animation: toastIn .22s cubic-bezier(.34, 1.4, .64, 1) forwards;
        }
        .error-toast.hide { animation: toastOut .2s ease forwards; }
        .error-toast i { color: #dc3545; font-size: 1.1rem; margin-top: 2px; }
        .error-toast .msg { flex: 1 1 auto; font-size: .9rem; color: #333; line-height: 1.35; }
        .error-toast .msg strong {
            display: block; font-size: .85rem; color: #b02a37; margin-bottom: 2px;
        }
        @keyframes toastIn {
            from { opacity: 0; transform: scale(.85); }
            to   { opacity: 1; transform: scale(1); }
        }
        @keyframes toastOut {
            from { opacity: 1; transform: scale(1); }
            to   { opacity: 0; transform: scale(.85); }
        }
    </style>
</head>
<body>
<div class="login-container auth-card">

    <div class="login-header">
        <i class="fas fa-tooth"></i>
        <h2><?php echo SITE_NAME; ?></h2>
        <p>Create your account</p>
    </div>

    <div class="auth-body">
        <form method="POST" id="registerForm" novalidate autocomplete="off">
            <input type="hidden" name="action" value="register">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <!-- ================= STEP 1: Name ================= -->
            <div class="step" data-step="1">
                <div class="step-heading">Personal Information</div>

                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name *</label>
                    <input type="text" class="form-control <?php echo isset($field_errors['first_name']) ? 'is-invalid' : ''; ?>"
                           id="first_name" name="first_name" maxlength="50"
                           value="<?php echo htmlspecialchars($values['first_name']); ?>"
                           placeholder="First name" required
                           autocomplete="nope-first-name" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <?php if (isset($field_errors['first_name'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($field_errors['first_name']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name *</label>
                    <input type="text" class="form-control <?php echo isset($field_errors['last_name']) ? 'is-invalid' : ''; ?>"
                           id="last_name" name="last_name" maxlength="50"
                           value="<?php echo htmlspecialchars($values['last_name']); ?>"
                           placeholder="Last name" required
                           autocomplete="nope-last-name" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <?php if (isset($field_errors['last_name'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($field_errors['last_name']); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ================= STEP 2: Contact ================= -->
            <div class="step" data-step="2" hidden>
                <div class="step-heading">Contact Information</div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control <?php echo isset($field_errors['email']) ? 'is-invalid' : ''; ?>"
                           id="email" name="email"
                           value="<?php echo htmlspecialchars($values['email']); ?>"
                           placeholder="yourname@domain.com" required
                           autocomplete="nope-email" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <?php if (isset($field_errors['email'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($field_errors['email']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number *</label>
                    <input type="tel" class="form-control <?php echo isset($field_errors['phone']) ? 'is-invalid' : ''; ?>"
                           id="phone" name="phone"
                           value="<?php echo htmlspecialchars($values['phone']); ?>"
                           placeholder="+639171234567 or 09171234567" required
                           autocomplete="nope-phone" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <?php if (isset($field_errors['phone'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($field_errors['phone']); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ================= STEP 3: DOB + Gender ================= -->
            <div class="step" data-step="3" hidden>
                <div class="step-heading">Additional Details</div>

                <div class="mb-3">
                    <label for="dob" class="form-label">Date of Birth (optional)</label>
                    <input type="date" class="form-control" id="dob" name="dob"
                           value="<?php echo htmlspecialchars($values['dob']); ?>"
                           max="<?php echo date('Y-m-d'); ?>"
                           autocomplete="nope-dob">
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label">Gender (optional)</label>
                    <select class="form-control" id="gender" name="gender" autocomplete="nope-gender">
                        <option value="">Select Gender</option>
                        <option value="Male"   <?php echo $values['gender'] === 'Male'   ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $values['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other"  <?php echo $values['gender'] === 'Other'  ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
            </div>

            <!-- ================= STEP 4: Username + Passwords ================= -->
            <div class="step" data-step="4" hidden>
                <div class="step-heading">Account Security</div>

                <div class="mb-3">
                    <label for="register_username" class="form-label">Username *</label>
                    <input type="text" class="form-control <?php echo isset($field_errors['username']) ? 'is-invalid' : ''; ?>"
                           id="register_username" name="username" maxlength="16"
                           value="<?php echo htmlspecialchars($values['username']); ?>"
                           placeholder="Letters & numbers only (3-16)" required
                           pattern="[A-Za-z0-9]{3,16}"
                           title="3-16 characters, letters and numbers only"
                           autocomplete="nope-username" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <?php if (isset($field_errors['username'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($field_errors['username']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="register_password" class="form-label">Password *</label>
                    <div class="input-group">
                        <input type="password" class="form-control <?php echo isset($field_errors['password']) ? 'is-invalid' : ''; ?>"
                               id="register_password" name="password" maxlength="64"
                               value="<?php echo htmlspecialchars($values['password']); ?>"
                               placeholder="Min 8 chars, 1 uppercase, 1 number, 1 special" required
                               autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false">
                        <button type="button" class="btn-toggle-password"><i class="fas fa-eye"></i></button>
                    </div>
                    <?php if (isset($field_errors['password'])): ?>
                        <div class="invalid-feedback d-block"><?php echo htmlspecialchars($field_errors['password']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                    <div class="input-group">
                        <input type="password" class="form-control <?php echo isset($field_errors['confirm_password']) ? 'is-invalid' : ''; ?>"
                               id="confirm_password" name="confirm_password" maxlength="64"
                               value="<?php echo htmlspecialchars($values['confirm_password']); ?>"
                               placeholder="Re-enter password" required
                               autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false">
                        <button type="button" class="btn-toggle-password"><i class="fas fa-eye"></i></button>
                    </div>
                    <?php if (isset($field_errors['confirm_password'])): ?>
                        <div class="invalid-feedback d-block"><?php echo htmlspecialchars($field_errors['confirm_password']); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <div class="auth-footer">

        <div class="step-indicator">
            <span class="dot active" data-step="1"></span>
            <span class="dot" data-step="2"></span>
            <span class="dot" data-step="3"></span>
            <span class="dot" data-step="4"></span>
        </div>

        <div class="nav-buttons">
            <button type="button" class="btn-back" id="prevBtn" hidden>
                <i class="fas fa-arrow-left"></i>
            </button>
            <button type="button" class="btn btn-login" id="nextBtn">
                Next <i class="fas fa-arrow-right"></i>
            </button>
            <button type="button" class="btn btn-login" id="registerSubmitBtn" hidden>
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </div>

        <div class="auth-footer-link">
            <small class="text-muted">
                Already have an account?
                <a href="login.php" class="forgot-link">Login</a>
            </small>
        </div>
    </div>
</div>

<!-- Toast stack -->
<div class="toast-stack" id="toastStack"></div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmRegistrationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-shield-alt me-2" style="color:#00695C;"></i> Confirm Account Creation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-user-plus fa-4x" style="color:#00695C; margin-bottom:15px;"></i>
                <p class="lead">Are you sure you want to create this account?</p>
                <p class="text-muted">Please review your details before confirming.</p>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary modal-confirm-btn" id="confirmRegisterBtn">
                    <i class="fas fa-check me-1"></i> Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/auth.js"></script>
<script src="assets/js/register.js"></script>

<script>
/* ============================================================
   Register wizard — 4 steps, no scroll, no autofill dropdown.
   ============================================================ */
(function () {
    const form      = document.getElementById('registerForm');
    const steps     = form.querySelectorAll('.step');
    const dots      = document.querySelectorAll('.step-indicator .dot');
    const prevBtn   = document.getElementById('prevBtn');
    const nextBtn   = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('registerSubmitBtn');
    const total     = steps.length;
    let current     = 1;

    /* ==========================================================
       AGGRESSIVE AUTOFILL KILLER  (v2 — also kills Chrome's
       saved-values dropdown, LastPass, Bitwarden, Edge, etc.)

       How the dropdown is defeated:
       1. Force `autocomplete="new-password"` on every field.
          Chrome refuses to offer saved values for fields it
          thinks are "create a new password" inputs — this is
          the single most effective trick.
       2. Add password-manager opt-out hints
          (data-lpignore / data-form-type / data-1p-ignore).
       3. Keep the readonly-on-blur trick so Chrome's autofill
          pass never sees an editable field on page load.
       ========================================================== */
    function armAutofillBlocker(el) {
        // Skip hidden inputs (they can't be focused anyway)
        if (el.type === 'hidden') return;

        // 1) Autocomplete overrides
        el.setAttribute('autocomplete', 'new-password');

        // 2) Password-manager opt-out hints
        el.setAttribute('data-lpignore', 'true');        // LastPass
        el.setAttribute('data-form-type', 'other');      // Dashlane
        el.setAttribute('data-1p-ignore', 'true');       // 1Password
        el.setAttribute('data-bwignore', 'true');        // Bitwarden

        // 3) readonly-on-load, cleared on focus, restored on blur
        el.setAttribute('readonly', 'readonly');

        el.addEventListener('focus', function () {
            const self = this;
            // setTimeout(0) → runs after Chrome's synchronous focus pass
            setTimeout(function () { self.removeAttribute('readonly'); }, 0);
        });

        el.addEventListener('blur', function () {
            this.setAttribute('readonly', 'readonly');
        });
    }

    form.querySelectorAll('input, select').forEach(armAutofillBlocker);

    // Belt-and-suspenders: if Chrome still injected a saved value
    // right after page load, wipe it (except anything PHP echoed back).
    window.addEventListener('load', function () {
        form.querySelectorAll('input').forEach(function (el) {
            if (el.type === 'hidden') return;
            // Only clear if the value looks like a Chrome suggestion
            // and NOT something the server rendered from $_POST.
            if (!el.defaultValue && el.value) el.value = '';
        });
    });

    /* ---------- Toast helper ---------- */
    function showToast(title, message, duration = 5000) {
        const stack = document.getElementById('toastStack');
        const toast = document.createElement('div');
        toast.className = 'error-toast';
        toast.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <div class="msg">
                <strong>${title}</strong>
                ${message}
            </div>
        `;
        stack.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('hide');
            toast.addEventListener('animationend', () => toast.remove(), { once: true });
        }, duration);
    }

    /* ---------- Step navigation ---------- */
    function showStep(n) {
        current = Math.max(1, Math.min(total, n));
        steps.forEach((el, i) => { el.hidden = (i + 1 !== current); });
        dots.forEach((d, i) => {
            d.classList.toggle('active', i + 1 === current);
            d.classList.toggle('done',   i + 1 <  current);
        });
        prevBtn.hidden   = (current === 1);
        nextBtn.hidden   = (current === total);
        submitBtn.hidden = (current !== total);
    }

    /* ---------- Per-step validation ----------
       IMPORTANT: the autofill blocker leaves inputs as `readonly`.
       Browsers *skip constraint validation* on readonly fields, so
       `el.checkValidity()` would return true even for empty inputs.
       We temporarily remove readonly here so validation actually
       runs, then restore it so the next focus is still autofill-safe.
       ------------------------------------------ */
    function validateStep(n) {
        const step = steps[n - 1];
        let ok = true;
        let messages = [];

        // 1) Temporarily unlock readonly fields inside this step
        const locked = [];
        step.querySelectorAll('[readonly]').forEach(el => {
            locked.push(el);
            el.removeAttribute('readonly');
        });

        // 2) Run native constraint validation on each visible field
        step.querySelectorAll('input, select, textarea').forEach(el => {
            if (el.disabled || el.type === 'hidden') return;

            let label = 'Field';
            const labelEl = step.querySelector('label[for="' + el.id + '"]');
            if (labelEl) {
                label = labelEl.textContent.replace('*', '').trim();
            } else if (el.previousElementSibling && el.previousElementSibling.tagName === 'LABEL') {
                label = el.previousElementSibling.textContent.replace('*', '').trim();
            }

            if (!el.checkValidity()) {
                el.classList.add('is-invalid');
                messages.push(`${label} is invalid.`);
                ok = false;
            } else {
                el.classList.remove('is-invalid');
            }
        });

        // 3) Restore readonly so autofill protection stays on
        locked.forEach(el => el.setAttribute('readonly', 'readonly'));

        // 4) Extra checks for step 4 (password strength + match)
        if (n === 4) {
            const pw  = document.getElementById('register_password');
            const cpw = document.getElementById('confirm_password');

            if (pw && pw.value.length > 0) {
                if (pw.value.length < 8) {
                    pw.classList.add('is-invalid');
                    messages.push('Password must be at least 8 characters.');
                    ok = false;
                } else if (!/[A-Z]/.test(pw.value)) {
                    pw.classList.add('is-invalid');
                    messages.push('Password must contain at least one uppercase letter.');
                    ok = false;
                } else if (!/[0-9]/.test(pw.value)) {
                    pw.classList.add('is-invalid');
                    messages.push('Password must contain at least one number.');
                    ok = false;
                } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(pw.value)) {
                    pw.classList.add('is-invalid');
                    messages.push('Password must contain at least one special character.');
                    ok = false;
                }
            }

            if (pw && cpw && pw.value !== cpw.value) {
                cpw.classList.add('is-invalid');
                messages.push('Passwords do not match.');
                ok = false;
            }
        }

        if (!ok) {
            showToast('Please check your input', messages.join('<br>'), 5000);
            const bad = step.querySelector('.is-invalid');
            if (bad) bad.focus();
        }
        return ok;
    }

    nextBtn.addEventListener('click', () => {
        if (validateStep(current)) showStep(current + 1);
    });
    prevBtn.addEventListener('click', () => showStep(current - 1));

    window.addEventListener('DOMContentLoaded', () => {
        const firstInvalid = form.querySelector('.step .is-invalid');
        if (firstInvalid) {
            const stepEl = firstInvalid.closest('.step');
            const n = parseInt(stepEl.dataset.step, 10);
            showStep(n || 1);
            const feedback = firstInvalid.parentElement.querySelector('.invalid-feedback');
            if (feedback && feedback.textContent.trim()) {
                showToast('Registration error', feedback.textContent.trim(), 5000);
            }
        } else {
            showStep(1);
        }

        const generalAlert = document.querySelector('.alert.alert-danger');
        if (generalAlert) {
            const text = generalAlert.textContent.trim();
            showToast('Error', text, 5000);
            generalAlert.remove();
        }
    });
})();
</script>
</body>
</html>
