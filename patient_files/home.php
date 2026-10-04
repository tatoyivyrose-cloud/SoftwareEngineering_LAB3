<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../login.php");
    exit();
}
$current_user_name = $_SESSION['name'] ?? 'Patient';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Pandoy-Dalmino Clinic</title>
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
                <h1 class="mt-2 fw-bold" style="color: #00695C;">Welcome, <?php echo htmlspecialchars($current_user_name); ?>!</h1>
                <p class="text-muted">Manage your appointments and track your dental health</p>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-5">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-2">
                        <div class="card-body p-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex p-2 mb-2">
                                <i class="fas fa-calendar fa-1x text-primary"></i>
                            </div>
                            <h4 class="mb-0 fw-bold">0</h4>
                            <p class="text-muted small mb-0">Total Appointments</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-2">
                        <div class="card-body p-2">
                            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex p-2 mb-2">
                                <i class="fas fa-hourglass-end fa-1x text-success"></i>
                            </div>
                            <h4 class="mb-0 fw-bold">0</h4>
                            <p class="text-muted small mb-0">Upcoming</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-2">
                        <div class="card-body p-2">
                            <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex p-2 mb-2">
                                <i class="fas fa-check-circle fa-1x text-info"></i>
                            </div>
                            <h4 class="mb-0 fw-bold">0</h4>
                            <p class="text-muted small mb-0">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-2">
                        <div class="card-body p-2">
                            <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex p-2 mb-2">
                                <i class="fas fa-credit-card fa-1x text-warning"></i>
                            </div>
                            <h4 class="mb-0 fw-bold">₱ 0.00</h4>
                            <p class="text-muted small mb-0">Pending Payment</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-5">
                <a href="#" class="btn btn-primary btn-md rounded-3 shadow-sm px-3">
                    <i class="fas fa-plus me-1"></i> Book Appointment
                </a>
                <a href="#" class="btn btn-outline-primary btn-md rounded-3 px-3">
                    <i class="fas fa-calendar me-1"></i> View Appointments
                </a>
                <a href="../login.php?logout=1" class="btn btn-outline-danger btn-md rounded-3 px-3">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>

            <!-- Upcoming Appointments Card -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header py-2 border-0" style="background: linear-gradient(135deg, #00695C 0%, #00897B 100%);">
                    <h6 class="mb-0 text-white fw-semibold"><i class="fas fa-calendar-alt me-1"></i> Upcoming Appointments</h6>
                </div>
                <div class="card-body p-0">
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                        <p class="text-muted small mb-2">No upcoming appointments scheduled.</p>
                        <a href="#" class="btn btn-primary btn-sm rounded-2">Book Your First Appointment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-primary {
    background: linear-gradient(135deg, #00695C 0%, #00897B 100%);
    border: none;
    transition: all 0.2s ease;
}
.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 105, 92, 0.3);
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