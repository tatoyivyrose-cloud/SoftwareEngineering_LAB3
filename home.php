<?php
require_once 'includes/login_functions.php';

// If not logged in, send them back to login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css">
    <style>
        .home-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 35px rgba(0,0,0,0.2);
            width: 450px;
        }
    </style>
</head>
<body>
<div class="home-container">
    <h1 style="color: #00695C; margin-bottom: 20px;">Welcome!</h1>
    
    <div class="alert alert-success">
        <h4>Hello, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h4>
        <p class="mb-0">You are logged in as: <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong></p>
    </div>
    
    <p class="text-muted">This is a temporary home page. Patient, Dentist, Admin, and Staff dashboards will be built here later.</p>

    <a href="login.php?logout=1" class="btn btn-login mt-3" style="width: auto; padding: 10px 30px;">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>