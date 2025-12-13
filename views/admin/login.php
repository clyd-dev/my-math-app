<?php
require_once '../../config/config.php';
require_once '../../models/Admin.php';
include '../../includes/header.php';

if(isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/dashboard.php");
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $admin = new Admin();
    if($admin->login($username, $password)) {
        header("Location: " . APP_URL . "/views/admin/dashboard.php");
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Math Quiz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --accent: #9c27b0;
        }
        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            overflow: hidden;
        }
        .login-container {
            position: relative;
            width: 100%;
            max-width: 460px;
            margin: 0 auto;
        }
        .login-card {
            background: white;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.3);
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .login-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 50px 40px;
            text-align: center;
            color: white;
        }
        .login-header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            margin: 0;
            text-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .login-header p {
            margin: 15px 0 0;
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .login-body {
            padding: 50px 40px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-control {
            height: 58px;
            border-radius: 15px;
            border: 2px solid #e0e0e0;
            padding: 0 20px;
            font-size: 1.05rem;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }
        .input-icon {
            position: relative;
        }
        .input-icon i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 1.2rem;
            z-index: 10;
        }
        .input-icon .form-control {
            padding-left: 55px;
        }
        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            height: 60px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            transition: all 0.4s;
            width: 100%;
        }
        .btn-login:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.6);
        }
        .alert {
            border-radius: 15px;
            border: none;
            padding: 15px 20px;
        }
        .credentials {
            text-align: center;
            margin-top: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 15px;
            font-size: 0.95rem;
            color: #666;
        }
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        .shape {
            position: absolute;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }
        .shape:nth-child(1) { width: 80px; height: 80px; top: 10%; left: 10%; animation-delay: 0s; }
        .shape:nth-child(2) { width: 120px; height: 120px; top: 70%; left: 80%; animation-delay: 5s; }
        .shape:nth-child(3) { width: 60px; height: 60px; top: 40%; left: 70%; animation-delay: 10s; }
    </style>
</head>
<body>

<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>

<div class="login-container">
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <h1>Admin Portal</h1>
            <p>Manage your Math Quiz System</p>
        </div>

        <!-- Body -->
        <div class="login-body">
            <h3 class="text-center mb-4" style="color: var(--primary); font-weight: 700;">
                Welcome Back!
            </h3>

            <?php if($error): ?>
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" class="form-control" placeholder="Enter username" required autocomplete="username">
                </div>

                <div class="form-group input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-login text-white">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Login to Dashboard
                </button>
            </form>

            <!-- <div class="credentials">
                <small>
                    <strong>Demo Credentials:</strong><br>
                    Username: <code>admin</code> • Password: <code>admin123</code>
                </small>
            </div> -->
        </div>
    </div>

    <!-- Back to Student Portal -->
    <div class="text-center mt-4">
        <a href="<?php echo APP_URL; ?>/views/student/landing.php" class="text-white opacity-75 hover-opacity-100" 
           style="text-decoration: none; font-size: 0.95rem;">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Student Portal
        </a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>