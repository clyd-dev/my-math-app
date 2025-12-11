<?php
// views/student/login.php - UPDATED WITH CONTROLLER
require_once '../../config/config.php';
require_once '../../controllers/StudentController.php';

$studentController = new StudentController();

// Redirect if already logged in
if($studentController->isLoggedIn()) {
    header("Location: " . APP_URL . "/views/student/dashboard.php");
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $password = $_POST['password'];
    
    $result = $studentController->login($name, $password);
    
    if($result['success']) {
        header("Location: " . APP_URL . "/views/student/dashboard.php");
        exit();
    } else {
        $error = $result['message'];
    }
}

$pageTitle = 'Student Login';
$isAdmin = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/student-style.css">
</head>
<body>

<div class="landing-container">
    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg">
                    <div class="card-header text-white text-center" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <h3 class="mb-0"><i class="fas fa-user"></i> Student Login</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <?php unset($_SESSION['success_message']); ?>
                        <?php endif; ?>
                        
                        <div class="text-center mb-4">
                            <div class="teacher-avatar" style="width: 100px; height: 100px; margin: 0 auto; font-size: 50px;">
                                👨‍🎓
                            </div>
                            <p class="text-muted mt-2">Welcome back! Please login to continue.</p>
                        </div>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Your Name</label>
                                <input type="text" name="name" class="form-control form-control-lg" 
                                       placeholder="Enter your name" required autofocus>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-lock"></i> Password</label>
                                <input type="password" name="password" class="form-control form-control-lg" 
                                       placeholder="Enter your password" required>
                            </div>
                            <button type="submit" class="btn btn-gradient-purple btn-block btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </form>
                        
                        <hr>
                        
                        <div class="text-center">
                            <p class="mb-2">Don't have an account?</p>
                            <a href="<?php echo APP_URL; ?>/views/student/register.php" class="btn btn-outline-primary">
                                <i class="fas fa-user-plus"></i> Create Account
                            </a>
                            <a href="<?php echo APP_URL; ?>/views/student/landing.php" class="btn btn-outline-secondary ml-2">
                                <i class="fas fa-home"></i> Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/student-script.js"></script>
</body>
</html>