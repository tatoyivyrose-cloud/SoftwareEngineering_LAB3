<?php
// ============================================================
// REGISTRATION FUNCTIONS
// ============================================================
require_once __DIR__ . '/login_functions.php';

function validateRegistration($data) {
    global $conn;
    $errors = [];

    $first = $data['first_name'] ?? '';
    $last  = $data['last_name']  ?? '';
    $email = $data['email']      ?? '';
    $phone = $data['phone']      ?? '';
    $user  = $data['username']   ?? '';
    $pwd   = $data['password']   ?? '';
    $conf  = $data['confirm_password'] ?? '';

    if ($first === '')      $errors['first_name'] = 'First name is required';
    elseif (strlen($first) > 50) $errors['first_name'] = 'First name cannot exceed 50 characters';

    if ($last === '')       $errors['last_name'] = 'Last name is required';
    elseif (strlen($last) > 50)  $errors['last_name'] = 'Last name cannot exceed 50 characters';

    if ($email === '')      $errors['email'] = 'Email address is required';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email address';

    if ($phone === '')      $errors['phone'] = 'Phone number is required';
    elseif (!preg_match('/^(\+63|0)[0-9]{10}$/', $phone)) $errors['phone'] = 'Enter a valid Philippine number (+63xxxxxxxxxx or 0xxxxxxxxxx)';

    if ($user === '')       $errors['username'] = 'Username is required';
    elseif (!preg_match('/^[A-Za-z0-9]{3,16}$/', $user)) $errors['username'] = 'Must be 3-16 characters (letters & numbers only)';
    else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) $errors['username'] = 'Username already taken.';
        $stmt->close();
    }

    if ($pwd === '')        $errors['password'] = 'Password is required';
    elseif (strlen($pwd) < 8 || strlen($pwd) > 64) $errors['password'] = 'Password must be 8-64 characters';
    elseif (!preg_match('/[A-Z]/', $pwd)) $errors['password'] = 'Must contain at least one uppercase letter';
    elseif (!preg_match('/[0-9]/', $pwd)) $errors['password'] = 'Must contain at least one number';
    elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $pwd)) $errors['password'] = 'Must contain at least one special character';

    if ($pwd !== $conf)     $errors['confirm_password'] = 'Passwords do not match';

    return $errors;
}

function createUser($data) {
    global $conn;

    $plainRecovery  = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $hashedRecovery = password_hash($plainRecovery, PASSWORD_BCRYPT);
    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

    $firstName = $data['first_name'];
    $lastName  = $data['last_name'];
    $email     = $data['email'];
    $phone     = $data['phone'];
    $username  = $data['username'];
    $gender    = $data['gender'] !== ''  ? $data['gender']  : null;
    $dob       = $data['dob'] !== ''     ? $data['dob']     : null;
    $address   = $data['address'] !== '' ? $data['address'] : null;
    $role      = 'patient';

    $stmt = $conn->prepare(
        "INSERT INTO users
        (first_name, last_name, email, phone, username, password, role, gender, date_of_birth, address, is_active, recovery_code_hash, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, NOW())"
    );
    $stmt->bind_param("sssssssssss",
        $firstName, $lastName, $email, $phone, $username,
        $hashedPassword, $role, $gender, $dob, $address, $hashedRecovery);

    $ok = $stmt->execute();
    $userId = $ok ? $stmt->insert_id : 0;
    $stmt->close();

    return $ok ? ['user_id' => $userId, 'recovery_code' => $plainRecovery] : false;
}