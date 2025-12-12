<?php
require_once '../../config/config.php';


// Redirect if already logged in
if(isset($_SESSION['student_id'])) {
    redirect('views/student/dashboard.php');
}

$pageTitle = 'Welcome';
$isAdmin = false;
?>
<?php include '../../includes/header.php'; ?>

<div class="landing-container">
    <div class="container py-5">
        <div class="text-center mb-5">
            <div class="d-inline-block bg-white px-5 py-3 rounded-pill shadow-lg mb-4">
                <h1 class="mb-0 text-primary">
                    <i class="fas fa-graduation-cap"></i> Math Adventure Quiz
                </h1>
            </div>
        </div>

        <div class="row align-items-center">
            <!-- Teacher Character Section -->
            <div class="col-lg-5 mb-4">
                <div class="teacher-section">
                    <div class="teacher-avatar">
                        👩‍🏫
                    </div>
                    <h2 class="text-center mb-3">Teacher Lilibeth Bordan</h2>
                    <div class="teacher-message">
                        <p>
                            "Welcome to Math Adventure! 🎉 I'm so excited to have you here. 
                            Let's make learning math fun and exciting together! Ready to start your quiz adventure?"
                        </p>
                    </div>
                    <div class="feature-tags">
                        <div class="feature-tag tag-yellow">⭐ Fun Quizzes</div>
                        <div class="feature-tag tag-green">🏆 Track Progress</div>
                        <div class="feature-tag tag-blue">🎮 Game Style</div>
                    </div>
                </div>
            </div>

            <!-- Action Cards Section -->
            <div class="col-lg-7">
                <div class="actions-section">
                    <!-- Login Card -->
                    <div class="action-card login-card mb-4" onclick="toggleForm('login')">
                        <div class="d-flex align-items-center mb-3">
                            <div class="action-icon icon-purple">
                                <i class="fas fa-sign-in-alt text-white"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="mb-0">Student Login</h3>
                                <p class="mb-0 text-muted">Already have an account?</p>
                            </div>
                        </div>
                        
                        <div class="action-form" id="loginForm" style="display: none;">
                            <form method="POST" action="login.php">
                                <div class="form-group">
                                    <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                                </div>
                                <button type="submit" class="btn btn-gradient-purple btn-block">
                                    <i class="fas fa-sign-in-alt"></i> Login Now
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Register Card -->
                    <div class="action-card register-card mb-4" onclick="toggleForm('register')">
                        <div class="d-flex align-items-center mb-3">
                            <div class="action-icon icon-green">
                                <i class="fas fa-user-plus text-white"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="mb-0">Create Account</h3>
                                <p class="mb-0 text-muted">New student? Register here!</p>
                            </div>
                        </div>
                        
                        <div class="action-form" id="registerForm" style="display: none;">
                            <form method="POST" action="register.php">
                                <div class="form-group">
                                    <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select name="section" class="form-control form-control-lg" placeholder="" required>
                                                <option value="">Section</option>
                                                <option>Diamond</option>
                                                <option>Ruby</option>
                                                <option>Jade</option>
                                                <option>Garnet</option>
                                                <option>Emerald</option>
                                                <option>Topaz</option>
                                                <option>Saphirre</option>
                                                <option>Pearl</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control" placeholder="Create Password" required>
                                </div>
                                <button type="submit" class="btn btn-gradient-green btn-block">
                                    <i class="fas fa-user-plus"></i> Register Now
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Guest Quiz Card -->
                    <div class="action-card guest-card" onclick="toggleForm('guest')">
                        <div class="d-flex align-items-center mb-3">
                            <div class="action-icon icon-blue">
                                <i class="fas fa-link text-white"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="mb-0">Join with Link</h3>
                                <p class="mb-0 text-muted">Have a quiz code?</p>
                            </div>
                        </div>
                        
                        <div class="action-form" id="guestForm" style="display: none;">
                            <form method="GET" action="guest-quiz.php">
                                <div class="form-group">
                                    <input type="text" name="code" id="quizCode" class="form-control" placeholder="Enter Quiz Code (e.g., ABC12345)" required>
                                </div>
                                <button type="submit" class="btn btn-gradient-blue btn-block">
                                    <i class="fas fa-rocket"></i> Join Quiz
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Message -->
        <div class="text-center mt-5">
            <h3 class="text-white" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                Let's make math fun! 🚀 Start your adventure now!
            </h3>
        </div>
    </div>
</div>

<script>
function toggleForm(formType) {
    var formId = formType + 'Form';
    var selectedForm = document.getElementById(formId);

    document.querySelectorAll('.action-form').forEach(function(form) {
        if (form !== selectedForm) {
            form.style.display = 'none';
        }
    });

    // Toggle only the selected form
    selectedForm.style.display = (selectedForm.style.display === 'block') ? 'none' : 'block';
}


// Auto-uppercase quiz code
document.getElementById('quizCode').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Prevent form clicks from closing the form
document.querySelectorAll('.action-form').forEach(function(form){
    form.addEventListener('click', function(e){
        e.stopPropagation();
    });
});

</script>

<div class="text-center mt-4">
    <a href="../admin/login.php"
       style="
           font-size: 13px; 
           color: #ffffff; 
           opacity: 0.6; 
           text-decoration: none;
       "
       onmouseover="this.style.opacity='1'"
       onmouseout="this.style.opacity='0.6'">
        Teacher Login
    </a>
</div>

<?php include '../../includes/footer.php'; ?>