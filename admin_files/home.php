<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
$current_user_name = $_SESSION['name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pandoy-Dalmino Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Page Header -->
            <div class="text-center mb-4">
                <i class="fas fa-chart-line fa-3x" style="color: #00695C;"></i>
                <h1 class="mt-2 fw-bold" style="color: #00695C;">Admin Dashboard</h1>
                <p class="text-muted">Overview of your dental clinic — Welcome, <?php echo htmlspecialchars($current_user_name); ?></p>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-5">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-3">
                        <div class="card-body">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex p-3 mb-3">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                            <h3 class="mb-1 fw-bold">0</h3>
                            <p class="text-muted mb-0">Total Users</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-3">
                        <div class="card-body">
                            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex p-3 mb-3">
                                <i class="fas fa-calendar-check fa-2x text-success"></i>
                            </div>
                            <h3 class="mb-1 fw-bold">0</h3>
                            <p class="text-muted mb-0">Appointments</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-3">
                        <div class="card-body">
                            <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex p-3 mb-3">
                                <i class="fas fa-file-medical fa-2x text-info"></i>
                            </div>
                            <h3 class="mb-1 fw-bold">0</h3>
                            <p class="text-muted mb-0">Treatments</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-3">
                        <div class="card-body">
                            <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex p-3 mb-3">
                                <i class="fas fa-credit-card fa-2x text-warning"></i>
                            </div>
                            <h3 class="mb-1 fw-bold">₱ 0.00</h3>
                            <p class="text-muted mb-0">Revenue</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
                <a href="#" class="btn btn-primary btn-lg rounded-3 shadow-sm px-4">
                    <i class="fas fa-users me-2"></i> Manage Users
                </a>
                <a href="#" class="btn btn-outline-primary btn-lg rounded-3 px-4">
                    <i class="fas fa-calendar me-2"></i> Appointments
                </a>
                <a href="#" class="btn btn-outline-primary btn-lg rounded-3 px-4">
                    <i class="fas fa-chart-bar me-2"></i> Reports
                </a>
                <a href="#" class="btn btn-outline-primary btn-lg rounded-3 px-4">
                    <i class="fas fa-cog me-2"></i> Settings
                </a>
                <a href="../login.php?logout=1" class="btn btn-outline-danger btn-lg rounded-3 px-4">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.15) !important;
}
.btn-primary {
    background: linear-gradient(135deg, #00695C 0%, #00897B 100%);
    border: none;
    transition: all 0.2s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 105, 92, 0.3);
}
.btn-outline-primary {
    border-color: #00695C;
    color: #00695C;
}
.btn-outline-primary:hover {
    background-color: #00695C;
    border-color: #00695C;
    color: white;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>