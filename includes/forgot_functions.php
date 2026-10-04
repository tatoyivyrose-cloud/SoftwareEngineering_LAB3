<?php
// ============================================================
// FORGOT PASSWORD FUNCTIONS
// ============================================================
require_once __DIR__ . '/login_functions.php';

function validateRecovery($username, $recoveryCode) {
    global $conn;
    $stmt = $conn->prepare("SELECT user_id, recovery_code_hash FROM users WHERE username = ? AND is_active = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row || empty($row['recovery_code_hash'])) return false;
    if (!password_verify($recoveryCode, $row['recovery_code_hash'])) return false;

    return $row['user_id'];
}

function updatePassword($userId, $newPassword) {
    global $conn;
    $hash = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $hash, $userId);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function validateNewPassword($pwd, $confirm) {
    $errors = [];
    if ($pwd === '') $errors['new_password'] = 'Password is required';
    elseif (strlen($pwd) < 8 || strlen($pwd) > 64) $errors['new_password'] = 'Password must be 8-64 characters';
    elseif (!preg_match('/[A-Z]/', $pwd)) $errors['new_password'] = 'Must contain at least one uppercase letter';
    elseif (!preg_match('/[0-9]/', $pwd)) $errors['new_password'] = 'Must contain at least one number';
    elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $pwd)) $errors['new_password'] = 'Must contain at least one special character';

    if ($pwd !== $confirm) $errors['confirm_new_password'] = 'Passwords do not match';
    return $errors;
}