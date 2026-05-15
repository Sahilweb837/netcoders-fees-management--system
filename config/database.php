<?php
ob_start();
/**
 * Professional FMS + Institute ERP Configuration
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'mhqhxuaasp');
define('DB_PASS', '4m3xU8bTVq');
define('DB_NAME', 'mhqhxuaasp');

// Set Timezone
date_default_timezone_set('Asia/Kolkata');

// Application Configuration
define('APP_NAME', 'Netcoder ERP');
define('CURRENCY', '₹');

// Robust Base URL detection
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') 
            ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME']; // e.g. /nfms/config/database.php
$config_dir = dirname($script_name); // e.g. /nfms/config
$root_path = dirname($config_dir); // e.g. /nfms
$base_url = $protocol . "://" . $host . rtrim($root_path, '/\\') . '/';
define('BASE_URL', $base_url);

// Establishing Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sync Database Timezone with PHP
$conn->query("SET time_zone = '+05:30'");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set Charset
$conn->set_charset("utf8mb4");

// Safe Migration Helper
function addColumnIfNotExists($conn, $table, $column, $definition) {
    $check = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE `$table` ADD `$column` $definition");
    }
}

addColumnIfNotExists($conn, 'students', 'discount', 'DECIMAL(10,2) DEFAULT 0.00 AFTER `total_fee`');
addColumnIfNotExists($conn, 'students', 'branch_id', 'INT(11) DEFAULT NULL AFTER `batch_id`');
addColumnIfNotExists($conn, 'attendance', 'fine_amount', 'DECIMAL(10,2) DEFAULT 0.00 AFTER `status`');
addColumnIfNotExists($conn, 'attendance', 'check_in_time', 'TIME DEFAULT NULL AFTER `attendance_date`');
addColumnIfNotExists($conn, 'attendance', 'check_out_time', 'TIME DEFAULT NULL AFTER `check_in_time`');
addColumnIfNotExists($conn, 'users', 'branch_id', 'INT(11) DEFAULT NULL AFTER `role`');
addColumnIfNotExists($conn, 'users', 'last_login', 'DATETIME DEFAULT NULL');
addColumnIfNotExists($conn, 'users', 'last_ip', 'VARCHAR(45) DEFAULT NULL');
addColumnIfNotExists($conn, 'users', 'plain_password', 'VARCHAR(255) DEFAULT NULL');

// Activity Logs Table
$conn->query("CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Global Session Start
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    ini_set('session.gc_maxlifetime', 3600);
    session_set_cookie_params(3600, '/');
    session_start();
}

// Common Functions
function logActivity($action) {
    global $conn;
    $user_id = $_SESSION['user_id'] ?? NULL;
    $ip = $_SERVER['REMOTE_ADDR'];
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $action, $ip, $ua);
    $stmt->execute();
}

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function flash($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
