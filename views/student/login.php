<?php
// ===== FILE: views/student/login.php =====
require_once '../../config/config.php';
require_once '../../models/Student.php';

if(isset($_SESSION['student_id'])) {
    header("Location: " . APP_URL . "/views/student/dashboard.php");
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $password = $_POST['password'];
    
    $studentModel = new Student();
    if($studentModel->login($name, $password)) {
        redirect('views/student/dashboard.php');
    } else {
        $error = 'Invalid name or password';
    }
}

$pageTitle = 'Student Login';
$isAdmin = false;
include '../../includes/header.php';
?>

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
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <div class="text-center mb-4">
                            <div class="teacher-avatar" style="width: 100px; height: 100px; margin: 0 auto; font-size: 50px;">
                                👨‍🎓
                            </div>
                        </div>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label>Your Name</label>
                                <input type="text" name="name" class="form-control form-control-lg" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control form-control-lg" required>
                            </div>
                            <button type="submit" class="btn btn-gradient-purple btn-block btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </form>
                        
                        <hr>
                        
                        <div class="text-center">
                            <p class="mb-2">Don't have an account?</p>
                            <a href="register.php" class="btn btn-outline-primary">Create Account</a>
                            <a href="landing.php" class="btn btn-outline-secondary ml-2">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>