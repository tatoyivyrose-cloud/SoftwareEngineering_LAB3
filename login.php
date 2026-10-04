<?php
require_once 'includes/login_functions.php';

// -------- LOGOUT --------
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// -------- ALREADY LOGGED IN --------
if (isLoggedIn()) {
    redirectByRole($_SESSION['role'] ?? '');
}

$login_error = '';
$csrf_token  = generateCsrfToken();

// -------- HANDLE LOGIN --------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {

    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $login_error = 'Invalid request. Please try again.';
    } else {
        $csrf_token = refreshCsrfToken();

        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $login_error = 'Please enter username and password';
        } elseif (countRecentAttempts($username) >= 5) {
            $login_error = 'Too many failed login attempts. Please try again after 15 minutes.';
        } else {
            $user = authenticateUser($username, $password);

            if ($user) {
                // Success
                $_SESSION['user_id']  = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];
                $_SESSION['name']     = $user['first_name'] . ' ' . $user['last_name'];
                session_regenerate_id(true);

                clearFailedAttempts($username);

                $conn->query("UPDATE users SET last_login = NOW() WHERE user_id = " . (int)$user['user_id']);

                writeLoginAudit(
                    $user['user_id'],
                    $user['username'],
                    $user['first_name'] . ' ' . $user['last_name']
                );

                redirectByRole($user['role']);
            } else {
                // Failure
                $login_error = 'Invalid username or password';
                recordFailedAttempt($username);
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
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <i class="fas fa-tooth"></i>
        <h2><?php echo SITE_NAME; ?></h2>
        <p>Login to your account</p>
    </div>

    <?php if ($login_error !== ''): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($login_error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="action" value="login">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username"
                   maxlength="16" placeholder="Enter username" required autofocus autocomplete="off">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password"
                       maxlength="64" placeholder="Enter password" required autocomplete="off">
                <button type="button" class="btn-toggle-password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="forgot-wrapper text-end">
            <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
        </div>

        <button type="submit" class="btn btn-login">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>

    <div class="social-login">
        <p>or continue with</p>
        <div class="d-flex justify-content-center flex-wrap">
            <a href="facebook-login.php" class="btn-social btn-facebook">
                <i class="fab fa-facebook-f"></i> Facebook
            </a>
            <a href="google-login.php" class="btn-social btn-google">
                <i class="fab fa-google"></i> Google
            </a>
        </div>
    </div>

    <div class="text-center mt-3">
        <small class="text-muted">
            Don't have an account?
            <a href="register.php" class="forgot-link">Create one</a>
        </small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/auth.js"></script>
</body>
</html>