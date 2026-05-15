<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entity_id = (int)$_POST['entity_id'];
    $username = $conn->real_escape_string($_POST['username']);
    $type = $_POST['type']; // 'student' or 'staff'
    
    $password = password_hash($type . '123', PASSWORD_BCRYPT);
    $role = ($type === 'student') ? 'staff' : 'staff'; // Default role for students/staff login
    
    // Check if user already exists
    $check = $conn->query("SELECT id FROM users WHERE username = '$username'");
    if ($check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'User account already exists for this ID!']);
        exit();
    }

    // Get name
    $name = "";
    if ($type === 'student') {
        $res = $conn->query("SELECT full_name FROM students WHERE id = $entity_id");
        if ($row = $res->fetch_assoc()) $name = $row['full_name'];
    } else {
        $res = $conn->query("SELECT name FROM staff WHERE id = $entity_id");
        if ($row = $res->fetch_assoc()) $name = $row['name'];
    }

    $stmt = $conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $username, $password, $role);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => "Account created!\nUsername: $username\nPassword: " . $type . "123"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error creating account.']);
    }
}
?>
