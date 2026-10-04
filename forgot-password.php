<?php
require_once 'includes/forgot_functions.php';

if (isLoggedIn()) redirectByRole($_SESSION['role'] ?? '');

$error = '';
$success = '';
$username = '';
$csrf_token = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reset') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $csrf_token = refreshCsrfToken();

        $username       = sanitize($_POST['username'] ?? '');
        $recoveryCode   = sanitize($_POST['recovery_code'] ?? '');
        $newPassword    = $_POST['new_password'] ?? '';
        $confirmNew     = $_POST['confirm_new_password'] ?? '';

        if ($username === '' || $recoveryCode === '') {
            $error = 'Please enter your username and recovery code.';
        } else {
            $userId = validateRecovery($username, $recoveryCode);
            if (!$userId) {
                $error = 'Invalid username or recovery code.';
            } else {
                $pwdErrors = validateNewPassword($newPassword, $confirmNew);
                if (!empty($pwdErrors)) {
                    $error = reset($pwdErrors);
                } elseif (updatePassword($userId, $newPassword)) {
                    $success = 'Password updated. You can now log in.';
                } else {
                    $error = 'Database error. Please try again.';
                }
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
    <title>Forgot Password - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <i class="fas fa-key"></i>
        <h2>Forgot Password</h2>
        <p>Use your recovery code to reset your password</p>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            <div class="mt-2"><a href="login.php" class="forgot-link">Go to Login →</a></div>
        </div>
    <?php endif; ?>

    <?php if ($success === ''): ?>
    <form method="POST">
        <input type="hidden" name="action" value="reset">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username"
                   maxlength="16" value="<?php echo htmlspecialchars($username); ?>"
                   placeholder="Enter username" required autocomplete="off">
        </div>

        <div class="mb-3">
            <label for="recovery_code" class="form-label">Recovery Code</label>
            <input type="text" class="form-control" id="recovery_code" name="recovery_code"
                   maxlength="6" placeholder="6-digit recovery code" required autocomplete="off">
        </div>

        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="new_password" name="new_password"
                       maxlength="64" placeholder="New password" required autocomplete="off">
                <button type="button" class="btn-toggle-password"><i class="fas fa-eye"></i></button>
            </div>
        </div>

        <div class="mb-3">
            <label for="confirm_new_password" class="form-label">Confirm New Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password"
                       maxlength="64" placeholder="Re-enter new password" required autocomplete="off">
                <button type="button" class="btn-toggle-password"><i class="fas fa-eye"></i></button>
            </div>
        </div>

        <button type="submit" class="btn btn-login">
            <i class="fas fa-paper-plane"></i> Reset Password
        </button>
    </form>
    <?php endif; ?>

    <div class="text-center mt-3">
        <a href="login.php" class="forgot-link"><i class="fas fa-arrow-left"></i> Back to Login</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/auth.js"></script>
</body>
</html>