<?php
// LOGIN DATABASE CONFIGURATION
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'dental_clinic');

define('BASE_URL', 'http://localhost/00/');
define('SITE_NAME', 'Pandoy-Dalmino Clinic');

date_default_timezone_set('Asia/Kolkata');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");