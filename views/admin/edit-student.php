<?php
// views/admin/edit-student.php - UPDATED WITH CONTROLLER
require_once '../../config/config.php';
require_once '../../controllers/StudentController.php';

// Initialize controller
$studentController = new StudentController();

// Get student ID from URL
$studentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validate student exists
if(!$studentController->studentExists($studentId)) {
    $_SESSION['error_message'] = 'Student not found';
    header("Location: " . APP_URL . "/views/admin/students.php");
    exit();
}

// Get student data
$student = $studentController->getStudent($studentId);

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $section = sanitize($_POST['section']);
    
    $result = $studentController->updateStudent($studentId, $name, $section);
    
    if($result['success']) {
        $_SESSION['success_message'] = $result['message'];
        header("Location: " . APP_URL . "/views/admin/students.php");
        exit();
    } else {
        $error = $result['message'];
    }
}

$pageTitle = 'Edit Student - ' . $student['name'];
include '../../includes/admin-layout.php';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-white"><i class="fas fa-edit"></i> Edit Student</h2>
            <a href="<?php echo APP_URL; ?>/views/admin/students.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Students
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Edit Form -->
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0"><i class="fas fa-user-edit"></i> Edit Student Information</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Full Name *</label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control form-control-lg" 
                                   value="<?php echo htmlspecialchars($student['name']); ?>" 
                                   required>
                            <small class="form-text text-muted">Enter the student's full name</small>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-door-open"></i> Section *</label>
                            <select name="section" class="form-control form-control-lg" required>
                                <option value="">Select Section</option>
                                <?php 
                                $sections = ['Diamond', 'Ruby', 'Jade', 'Garnet', 'Emerald', 'Topaz', 'Sapphire', 'Pearl'];
                                foreach($sections as $sec): 
                                ?>
                                    <option value="<?php echo $sec; ?>" <?php echo $student['section'] == $sec ? 'selected' : ''; ?>>
                                        <?php echo $sec; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Account Type</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="<?php echo $student['is_guest'] ? 'Guest Account' : 'Registered Account'; ?>" 
                                           disabled>
                                    <small class="form-text text-muted">Account type cannot be changed</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Registration Date</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="<?php echo date('F d, Y h:i A', strtotime($student['created_at'])); ?>" 
                                           disabled>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Student ID</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="#<?php echo str_pad($student['id'], 5, '0', STR_PAD_LEFT); ?>" 
                                   disabled>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save"></i> Update Student
                            </button>
                            <a href="<?php echo APP_URL; ?>/views/admin/students.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Student Info Card -->
        <div class="col-md-4">
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Quick Info</h5>
                </div>
                <div class="card-body text-center">
                    <div style="width: 100px; height: 100px; margin: 0 auto 20px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 48px;">
                        👨‍🎓
                    </div>
                    <h5 class="mb-3"><?php echo htmlspecialchars($student['name']); ?></h5>
                    
                    <?php
                    $quizHistory = $studentController->getQuizHistory($student['id']);
                    $stats = $studentController->getStudentStats($student['id']);
                    ?>
                    
                    <div class="mb-3">
                        <div class="text-muted mb-2">Total Quizzes Taken</div>
                        <h3 class="text-primary"><?php echo $stats['total_quizzes']; ?></h3>
                    </div>
                    
                    <?php if($stats['total_quizzes'] > 0): ?>
                        <div class="mb-3">
                            <div class="text-muted mb-2">Average Score</div>
                            <h4 class="text-success"><?php echo number_format($stats['average_score'], 1); ?>%</h4>
                        </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <a href="<?php echo APP_URL; ?>/views/admin/view-student.php?id=<?php echo $student['id']; ?>" 
                       class="btn btn-info btn-block">
                        <i class="fas fa-eye"></i> View Full Details
                    </a>
                    
                    <button onclick="deleteStudent(<?php echo $student['id']; ?>, '<?php echo addslashes($student['name']); ?>')" 
                            class="btn btn-danger btn-block">
                        <i class="fas fa-trash"></i> Delete Student
                    </button>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <?php if(!empty($quizHistory)): ?>
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Activity</h5>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <?php foreach(array_slice($quizHistory, 0, 5) as $quiz): ?>
                        <div class="border-left border-success pl-3 mb-3 pb-3 border-bottom">
                            <h6 class="mb-1"><?php echo htmlspecialchars($quiz['title']); ?></h6>
                            <small class="text-muted">
                                <?php echo date('M d, Y', strtotime($quiz['submitted_at'])); ?>
                            </small>
                            <div class="mt-1">
                                <span class="badge badge-success">
                                    <?php echo $quiz['score']; ?>/<?php echo $quiz['total_questions']; ?>
                                    (<?php echo number_format($quiz['percentage'], 1); ?>%)
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function deleteStudent(id, name) {
    if(confirm('⚠️ WARNING ⚠️\n\nAre you sure you want to delete ' + name + '?\n\n' +
               'This will permanently delete:\n' +
               '• All quiz responses\n' +
               '• All quiz history\n' +
               '• All student data\n\n' +
               'This action CANNOT be undone!')) {
        window.location.href = '<?php echo APP_URL; ?>/views/admin/delete-student.php?id=' + id;
    }
}
</script>

<?php include '../../includes/admin-layout-footer.php'; ?>