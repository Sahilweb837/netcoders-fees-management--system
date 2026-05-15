<?php
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE username = ? AND status = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            // Update last login info
            $ip = $_SERVER['REMOTE_ADDR'];
            $conn->query("UPDATE users SET last_login = NOW(), last_ip = '$ip' WHERE id = " . $user['id']);
            logActivity("User Login");

            flash("Welcome back, " . $user['name'] . "!");
            redirect('index.php');
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?php echo APP_NAME; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            height: 100vh;
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 1000px;
            height: 600px;
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        .login-left {
            flex: 1.2;
            background: #fdfdfd;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            border-right: 1px solid #f5f5f5;
        }
        .login-left img {
            max-width: 320px;
            margin-bottom: 40px;
            transition: transform 0.5s ease;
        }
        .login-left img:hover { transform: scale(1.02); }
        .login-left h1 { 
            color: #1a1a1a; 
            font-weight: 900; 
            font-size: 2.8rem; 
            margin-bottom: 10px;
            letter-spacing: -1px;
        }
        .login-left p { color: #666; font-size: 1.1rem; }
        .login-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            background: #ffffff;
        }
        .login-form-container { width: 100%; max-width: 350px; }
        .form-control { border-radius: 8px; padding: 12px; border: 1px solid #ddd; }
        .form-control:focus { border-color: #ff5532; box-shadow: 0 0 0 0.2rem rgba(255, 85, 50, 0.25); }
        .btn-primary { 
            background-color: #ff5532; 
            border: none; 
            padding: 12px; 
            border-radius: 8px; 
            font-weight: 700; 
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover { background-color: #1a1a1a; transform: translateY(-2px); }
        .input-group-text { background-color: #f8f9fa; border-right: none; }
        .form-control { border-left: none; }
        
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .login-left { padding: 40px 20px; }
            .login-left img { max-width: 150px; }
            .login-right { width: 100%; padding: 40px 20px; }
        }
    </style>
</head>
<body>

<div class="login-left d-none d-md-flex">
    <img src="<?php echo BASE_URL; ?>image.png" alt="Netcoder Logo">
    <h1><?php echo APP_NAME; ?></h1>
    <p class="lead">Advanced Institute Management & Financial System</p>
    <div class="mt-4">
        <span class="badge rounded-pill bg-light text-dark px-3 py-2">Secure Access</span>
        <span class="badge rounded-pill bg-light text-dark px-3 py-2 ms-2">Real-time Analytics</span>
    </div>
</div>

<div class="login-right">
    <div class="login-form-container">
        <div class="d-md-none text-center mb-4">
            <img src="<?php echo BASE_URL; ?>image.png" alt="Netcoder Logo" style="max-width: 120px;">
            <h2 class="mt-2" style="color: #ff5532; font-weight: 800;"><?php echo APP_NAME; ?></h2>
        </div>
        
        <h2 class="mb-4 text-dark" style="font-weight: 700;">Login</h2>
        <p class="text-muted mb-4">Enter your credentials to access the portal.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2" style="font-size: 0.9rem;">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label small text-uppercase font-weight-bold">Username</label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" name="username" placeholder="Enter username" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label small text-uppercase font-weight-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg shadow">Sign In <i class="fas fa-sign-in-alt ms-2"></i></button>
            </div>
        </form>
        
        <div class="mt-5 text-center">
            <p class="small text-muted">&copy; <?php echo date('Y'); ?> Netcoder Technology.<br>All rights reserved.</p>
        </div>
    </div>
</div>

</body>
</html>
