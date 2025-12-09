<?php
require_once '../../config/config.php';
require_once '../../models/Admin.php';
include '../../includes/header.php';

if(isset($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $admin = new Admin();
    if($admin->login($username, $password)) {
        redirect('dashboard.php');
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Math Quiz</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/admin-style.css">
    <!-- <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            max-width: 450px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
    </style> -->
</head>
<body>
    <div class="container">
        <div class="login-card p-5">
            <h2 class="text-center mb-4">🎓 Admin Login</h2>
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <p class="text-center mt-3">Default: admin / admin123</p>
        </div>
    </div>
</body>
</html>