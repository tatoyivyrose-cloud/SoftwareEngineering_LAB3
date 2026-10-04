<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dentist') {
    header("Location: ../login.php");
    exit();
}
$current_user_name = $_SESSION['name'] ?? 'Dentist';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dentist Dashboard - Pandoy-Dalmino Clinic</title>
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
                <h1 class="mt-2 fw-bold" style="color: #00695C;">Welcome, Dr. <?php echo htmlspecialchars($current_user_name); ?>!</h1>
                <p class="text-muted">Manage your appointments and patients</p>
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
                            <p class="text-muted mb-0">Total Patients</p>
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
                            <p class="text-muted mb-0">Today's Appointments</p>
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
                            <p class="text-muted mb-0">Total Treatments</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-3">
                        <div class="card-body">
                            <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex p-3 mb-3">
                                <i class="fas fa-hourglass-half fa-2x text-warning"></i>
                            </div>
                            <h3 class="mb-1 fw-bold">0</h3>
                            <p class="text-muted mb-0">Pending Confirmations</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
                <a href="#" class="btn btn-primary btn-lg rounded-3 shadow-sm px-4">
                    <i class="fas fa-calendar me-2"></i> View All Appointments
                </a>
                <a href="#" class="btn btn-outline-primary btn-lg rounded-3 px-4">
                    <i class="fas fa-clock me-2"></i> Manage Schedule
                </a>
                <a href="#" class="btn btn-outline-primary btn-lg rounded-3 px-4">
                    <i class="fas fa-users me-2"></i> View Patients
                </a>
                <a href="../login.php?logout=1" class="btn btn-outline-danger btn-lg rounded-3 px-4">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>

            <!-- Upcoming Appointments Table -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header py-3 border-0" style="background: linear-gradient(135deg, #00695C 0%, #00897B 100%);">
                    <h5 class="mb-0 text-white fw-semibold"><i class="fas fa-calendar-alt me-2"></i> Upcoming Appointments</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4">Booked On</th>
                                    <th class="py-3">Appointment Date</th>
                                    <th class="py-3">Time</th>
                                    <th class="py-3">Service / Reason</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">No upcoming appointments found.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover:not(.list-group-item) {
    transform: translateY(-5px);
    box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.15) !important;
}
.table th { font-weight: 600; border-bottom: none; }
.table td { vertical-align: middle; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>