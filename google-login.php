<?php
require_once 'includes/login_functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
<div class="login-container text-center">
    <div class="login-header">
        <i class="fab fa-google" style="color:#ea4335;"></i>
        <h2>Google Login</h2>
        <p>Coming soon</p>
    </div>
    <div class="alert alert-info">
        Google login is not available yet. Please use your username and password.
    </div>
    <a href="login.php" class="btn btn-login"><i class="fas fa-arrow-left"></i> Back to Login</a>
</div>
</body>
</html>