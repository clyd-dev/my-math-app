<?php
// ===== FILE: views/student/register.php =====
require_once '../../config/config.php';
require_once '../../models/Student.php';

if(isset($_SESSION['student_id'])) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $grade = sanitize($_POST['grade']);
    $section = sanitize($_POST['section']);
    $password = $_POST['password'];
    
    if(strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        $studentModel = new Student();
        if($studentModel->register($name, $grade, $section, $password)) {
            $success = 'Registration successful! Please login.';
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}

$pageTitle = 'Student Registration';
$isAdmin = false;
include '../../includes/header.php';
?>

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
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if($success): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                                <br>
                                <a href="login.php" class="btn btn-primary btn-sm mt-2">Login Now</a>
                            </div>
                        <?php else: ?>
                            <div class="text-center mb-4">
                                <div class="teacher-avatar" style="width: 100px; height: 100px; margin: 0 auto; font-size: 50px;">
                                    🎓
                                </div>
                                <p class="text-muted mt-3">Join Math Adventure and start learning!</p>
                            </div>
                            
                            <form method="POST">
                                <div class="form-group">
                                    <label>Full Name *</label>
                                    <input type="text" name="name" class="form-control form-control-lg" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Grade *</label>
                                            <select name="grade" class="form-control form-control-lg" required>
                                                <option value="">Select Grade</option>
                                                <option>Grade 7</option>
                                                <option>Grade 8</option>
                                                <option>Grade 9</option>
                                                <option>Grade 10</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Section *</label>
                                            <input type="text" name="section" class="form-control form-control-lg" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Password * (at least 6 characters)</label>
                                    <input type="password" name="password" class="form-control form-control-lg" required minlength="6">
                                </div>
                                <button type="submit" class="btn btn-gradient-green btn-block btn-lg">
                                    <i class="fas fa-user-plus"></i> Register Now
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <div class="text-center">
                            <p class="mb-2">Already have an account?</p>
                            <a href="login.php" class="btn btn-outline-primary">Login Here</a>
                            <a href="landing.php" class="btn btn-outline-secondary ml-2">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>