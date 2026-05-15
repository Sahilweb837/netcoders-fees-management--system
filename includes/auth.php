<?php
/**
 * Authentication Helper
 */

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function checkLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "login.php");
        exit();
    }
}

function checkRole($allowedRoles = []) {
    checkLogin();
    if (!in_array($_SESSION['role'], $allowedRoles)) {
        flash("You do not have permission to access this page.", "danger");
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
}

function getLoggedInUser() {
    global $conn;
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    return null;
}
?>
