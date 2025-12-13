<?php
// views/student/register.php - UPDATED WITH CONTROLLER
require_once '../../config/config.php';
require_once '../../controllers/StudentController.php';
require_once '../../models/Section.php';  // ADD THIS

$studentController = new StudentController();

// Redirect if already logged in
if($studentController->isLoggedIn()) {
    header("Location: " . APP_URL . "/views/student/dashboard.php");
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $section = sanitize($_POST['section']);
    $password = $_POST['password'];
    
    $result = $studentController->register($name, $section, $password);
    
    if($result['success']) {
        $_SESSION['success_message'] = $result['message'];
        header("Location: " . APP_URL . "/views/student/login.php");
        exit();
    } else {
        $error = $result['message'];
    }
}

$pageTitle = 'Student Registration';
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
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header text-white text-center" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <h3 class="mb-0"><i class="fas fa-user-plus"></i> Create Your Account</h3>
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
                        
                        <div class="text-center mb-4">
                            <div class="teacher-avatar" style="width: 100px; height: 100px; margin: 0 auto; font-size: 50px;">
                                🎓
                            </div>
                            <p class="text-muted mt-3">Join Math Adventure and start learning!</p>
                        </div>
                        
                        <form method="POST" id="registerForm">
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Full Name *</label>
                                <input type="text" name="name" class="form-control form-control-lg" 
                                       placeholder="Enter your full name" required minlength="2" autofocus>
                                <small class="form-text text-muted">At least 2 characters</small>
                            </div>
                            
                         <div class="form-group">
    <label>Section *</label>
    <select name="section" class="form-control form-control-lg" required>
        <option value="">Choose your section</option>
        <?php
        $sectionModel = new Section();
        foreach($sectionModel->getAll() as $s) {
            echo '<option value="'.htmlspecialchars($s['name']).'">'.htmlspecialchars($s['name']).'</option>';
        }
        ?>
    </select>
</div>
                            
                            <div class="form-group">
                                <label><i class="fas fa-lock"></i> Password * (minimum 6 characters)</label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg" 
                                       placeholder="Create a password" required minlength="6">
                                <small class="form-text text-muted">Must be at least 6 characters long</small>
                            </div>
                            
                            <div class="form-group">
                                <label><i class="fas fa-lock"></i> Confirm Password *</label>
                                <input type="password" name="confirm_password" id="confirm_password" 
                                       class="form-control form-control-lg" placeholder="Confirm your password" required>
                                <small id="password-match" class="form-text"></small>
                            </div>
                            
                            <button type="submit" class="btn btn-gradient-green btn-block btn-lg">
                                <i class="fas fa-user-plus"></i> Register Now
                            </button>
                        </form>
                        
                        <hr>
                        
                        <div class="text-center">
                            <p class="mb-2">Already have an account?</p>
                            <a href="<?php echo APP_URL; ?>/views/student/login.php" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt"></i> Login Here
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
<script>
// Password confirmation validation
$(document).ready(function() {
    $('#confirm_password').on('keyup', function() {
        var password = $('#password').val();
        var confirmPassword = $(this).val();
        
        if (confirmPassword) {
            if (password === confirmPassword) {
                $('#password-match').html('<span class="text-success"><i class="fas fa-check"></i> Passwords match</span>');
            } else {
                $('#password-match').html('<span class="text-danger"><i class="fas fa-times"></i> Passwords do not match</span>');
            }
        } else {
            $('#password-match').html('');
        }
    });
    
    // Validate on submit
    $('#registerForm').on('submit', function(e) {
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }
    });
});
</script>
</body>
</html>