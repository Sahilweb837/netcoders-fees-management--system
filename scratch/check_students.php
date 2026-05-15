<?php
require_once 'config/database.php';
$res = $conn->query("SELECT COUNT(*) as total FROM students");
$row = $res->fetch_assoc();
echo "Total Students: " . $row['total'] . "\n";

$res = $conn->query("SELECT COUNT(*) as active FROM students WHERE status = 1");
$row = $res->fetch_assoc();
echo "Active Students (status=1): " . $row['active'] . "\n";
?>
