<?php
// ===== FILE: views/admin/view-student.php =====
require_once '../../config/config.php';
require_once '../../models/Student.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/login.php");
    exit();
}

$studentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$studentModel = new Student();
$student = $studentModel->getById($studentId);
$quizHistory = $studentModel->getQuizHistory($studentId);

if(!$student) {
    $_SESSION['error_message'] = 'Student not found';
    header("Location: " . APP_URL . "/views/admin/students.php");
    exit();
}

$pageTitle = 'View Student - ' . $student['name'];
include '../../includes/admin-layout.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="<?php echo APP_URL; ?>/views/admin/students.php" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Students
            </a>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user"></i> Student Information</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div style="width: 100px; height: 100px; margin: 0 auto; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 48px;">
                            👨‍🎓
                        </div>
                    </div>
                    <h5 class="text-center mb-3"><?php echo htmlspecialchars($student['name']); ?></h5>
                    
                    <table class="table table-sm">
                        <tr>
                            <th>Section:</th>
                            <td><?php echo htmlspecialchars($student['section']); ?></td>
                        </tr>
                        <tr>
                            <th>Account Type:</th>
                            <td>
                                <?php if($student['is_guest']): ?>
                                    <span class="badge badge-warning">Guest</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Registered</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Registered:</th>
                            <td><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                        </tr>
                        <tr>
                            <th>Total Quizzes:</th>
                            <td><strong><?php echo count($quizHistory); ?></strong></td>
                        </tr>
                    </table>
                    
                    <div class="mt-3">
                        <a href="<?php echo APP_URL; ?>/views/admin/edit-student.php?id=<?php echo $student['id']; ?>" class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Edit Student
                        </a>
                        <button onclick="deleteStudent(<?php echo $student['id']; ?>, '<?php echo addslashes($student['name']); ?>')" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> Delete Student
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quiz History -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-history"></i> Quiz History</h4>
                </div>
                <div class="card-body">
                    <?php if(empty($quizHistory)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                            <p class="text-muted">This student hasn't taken any quizzes yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Quiz Title</th>
                                        <th>Date Taken</th>
                                        <th>Score</th>
                                        <th>Percentage</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($quizHistory as $history): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($history['title']); ?></td>
                                            <td><?php echo date('M d, Y h:i A', strtotime($history['submitted_at'])); ?></td>
                                            <td>
                                                <strong><?php echo $history['score']; ?>/<?php echo $history['total_questions']; ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $percentage = $history['percentage'];
                                                $badgeClass = 'secondary';
                                                if($percentage >= 90) $badgeClass = 'success';
                                                elseif($percentage >= 75) $badgeClass = 'primary';
                                                elseif($percentage >= 60) $badgeClass = 'warning';
                                                else $badgeClass = 'danger';
                                                ?>
                                                <span class="badge badge-<?php echo $badgeClass; ?>">
                                                    <?php echo number_format($percentage, 1); ?>%
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                if($percentage >= 90) echo '⭐⭐⭐⭐⭐';
                                                elseif($percentage >= 75) echo '⭐⭐⭐⭐';
                                                elseif($percentage >= 60) echo '⭐⭐⭐';
                                                elseif($percentage >= 50) echo '⭐⭐';
                                                else echo '⭐';
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteStudent(id, name) {
    if(confirm('Are you sure you want to delete ' + name + '?\n\nAll their quiz responses will also be deleted. This action cannot be undone.')) {
        window.location.href = '<?php echo APP_URL; ?>/views/admin/delete-student.php?id=' + id;
    }
}
</script>

<?php include '../../includes/admin-layout-footer.php'; ?>