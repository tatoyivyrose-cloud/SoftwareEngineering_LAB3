<?php
// ============================================================
// LOGIN FUNCTIONS
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/login_database.php';

function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function redirectByRole($role) {
    switch ($role) {
        case 'patient': header("Location: " . BASE_URL . "patient_files/home.php"); break;
        case 'dentist': header("Location: " . BASE_URL . "dentist_files/home.php"); break;
        case 'admin':   header("Location: " . BASE_URL . "admin_files/home.php");   break;
        case 'staff':   header("Location: " . BASE_URL . "staff_files/home.php");   break;
        default:        header("Location: " . BASE_URL . "login.php");
    }
    exit();
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function refreshCsrfToken() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

function writeLoginAudit($userId, $username, $fullName) {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $newValue = json_encode(['username' => $username, 'name' => $fullName]);

    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (user_id, action, table_name, record_id, old_value, new_value, ip_address, user_agent, created_at)
         VALUES (?, 'User login', 'users', ?, NULL, ?, ?, ?, NOW())"
    );
    $stmt->bind_param("iisss", $userId, $userId, $newValue, $ip, $ua);
    $stmt->execute();
    $stmt->close();
}

function recordFailedAttempt($username) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO login_attempts (username, attempt_time) VALUES (?, NOW())");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();
}

function countRecentAttempts($username) {
    global $conn;
    $window = date('Y-m-d H:i:s', strtotime('-15 minutes'));
    $stmt = $conn->prepare("SELECT COUNT(*) AS attempts FROM login_attempts WHERE username = ? AND attempt_time > ?");
    $stmt->bind_param("ss", $username, $window);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_assoc()['attempts'];
    $stmt->close();
    return (int)$count;
}

function clearFailedAttempts($username) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM login_attempts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();
}

function authenticateUser($username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (verifyPassword($password, $user['password'])) {
            $stmt->close();
            return $user;
        }
    }
    $stmt->close();
    return null;
}