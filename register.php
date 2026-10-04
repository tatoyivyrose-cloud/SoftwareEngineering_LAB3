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
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>
<div class="login-container register-container">
    <div class="login-header">
        <i class="fas fa-tooth"></i>
        <h2><?php echo SITE_NAME; ?></h2>
        <p>Create your account</p>
    </div>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
        <li class="nav-item"><a class="nav-link active" href="register.php"><i class="fas fa-user-plus"></i> Create Account</a></li>
    </ul>

    <?php if (!empty($field_errors['general'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($field_errors['general']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" id="registerForm">
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">First Name *</label>
                <input type="text" class="form-control <?php echo isset($field_errors['first_name']) ? 'is-invalid' : ''; ?>"
                       id="first_name" name="first_name" maxlength="50"
                       value="<?php echo htmlspecialchars($values['first_name']); ?>"
                       placeholder="First name" required autocomplete="off">
                <?php if (isset($field_errors['first_name'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($field_errors['first_name']); ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Last Name *</label>
                <input type="text" class="form-control <?php echo isset($field_errors['last_name']) ? 'is-invalid' : ''; ?>"
                       id="last_name" name="last_name" maxlength="50"
                       value="<?php echo htmlspecialchars($values['last_name']); ?>"
                       placeholder="Last name" required autocomplete="off">
                <?php if (isset($field_errors['last_name'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($field_errors['last_name']); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email *</label>
            <input type="email" class="form-control <?php echo isset($field_errors['email']) ? 'is-invalid' : ''; ?>"
                   id="email" name="email"
                   value="<?php echo htmlspecialchars($values['email']); ?>"
                   placeholder="yourname@domain.com" required autocomplete="off">
            <?php if (isset($field_errors['email'])): ?>
                <div class="invalid-feedback"><?php echo htmlspecialchars($field_errors['email']); ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number *</label>
            <input type="tel" class="form-control <?php echo isset($field_errors['phone']) ? 'is-invalid' : ''; ?>"
                   id="phone" name="phone"
                   value="<?php echo htmlspecialchars($values['phone']); ?>"
                   placeholder="+639171234567 or 09171234567" required autocomplete="off">
            <?php if (isset($field_errors['phone'])): ?>
                <div class="invalid-feedback"><?php echo htmlspecialchars($field_errors['phone']); ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="dob" class="form-label">Date of Birth (optional)</label>
            <input type="date" class="form-control" id="dob" name="dob"
                   value="<?php echo htmlspecialchars($values['dob']); ?>"
                   max="<?php echo date('Y-m-d'); ?>" autocomplete="off">
        </div>

        <div class="mb-3">
            <label for="gender" class="form-label">Gender (optional)</label>
            <select class="form-control" id="gender" name="gender">
                <option value="">Select Gender</option>
                <option value="Male"   <?php echo $values['gender'] === 'Male'   ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo $values['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                <option value="Other"  <?php echo $values['gender'] === 'Other'  ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="register_username" class="form-label">Username *</label>
            <input type="text" class="form-control <?php echo isset($field_errors['username']) ? 'is-invalid' : ''; ?>"
                   id="register_username" name="username" maxlength="16"
                   value="<?php echo htmlspecialchars($values['username']); ?>"
                   placeholder="Letters & numbers only (3-16)" required autocomplete="off">
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
                       placeholder="Min 8 chars, 1 uppercase, 1 number, 1 special" required autocomplete="off">
                <button type="button" class="btn-toggle-password"><i class="fas fa-eye"></i></button>
            </div>
            <?php if (isset($field_errors['password'])): ?>
                <div class="invalid-feedback"><?php echo htmlspecialchars($field_errors['password']); ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password *</label>
            <div class="input-group">
                <input type="password" class="form-control <?php echo isset($field_errors['confirm_password']) ? 'is-invalid' : ''; ?>"
                       id="confirm_password" name="confirm_password" maxlength="64"
                       value="<?php echo htmlspecialchars($values['confirm_password']); ?>"
                       placeholder="Re-enter password" required autocomplete="off">
                <button type="button" class="btn-toggle-password"><i class="fas fa-eye"></i></button>
            </div>
            <?php if (isset($field_errors['confirm_password'])): ?>
                <div class="invalid-feedback"><?php echo htmlspecialchars($field_errors['confirm_password']); ?></div>
            <?php endif; ?>
        </div>

        <button type="button" class="btn btn-login" id="registerSubmitBtn">
            <i class="fas fa-user-plus"></i> Create Account
        </button>
    </form>
</div>

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
</body>
</html>