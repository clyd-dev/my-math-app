<?php
// ===== FILE: views/admin/edit-student.php =====
require_once '../../config/config.php';
require_once '../../models/Student.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/login.php");
    exit();
}

$studentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$studentModel = new Student();
$student = $studentModel->getById($studentId);

if(!$student) {
    $_SESSION['error_message'] = 'Student not found';
    header("Location: " . APP_URL . "/views/admin/students.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $section = sanitize($_POST['section']);
    
    if($studentModel->update($studentId, $name, $section)) {
        $_SESSION['success_message'] = 'Student updated successfully!';
        header("Location: " . APP_URL . "/views/admin/students.php");
        exit();
    } else {
        $error = 'Failed to update student';
    }
}

$pageTitle = 'Edit Student';
include '../../includes/admin-layout.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-edit"></i> Edit Student</h2>
            <a href="<?php echo APP_URL; ?>/views/admin/students.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Students
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">Edit Student Information</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Section *</label>
                            <select name="section" class="form-control" required>
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
                        <div class="form-group">
                            <label>Account Type</label>
                            <input type="text" class="form-control" value="<?php echo $student['is_guest'] ? 'Guest' : 'Registered'; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Registered Date</label>
                            <input type="text" class="form-control" value="<?php echo date('F d, Y', strtotime($student['created_at'])); ?>" disabled>
                        </div>
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-save"></i> Update Student
                        </button>
                        <a href="<?php echo APP_URL; ?>/views/admin/students.php" class="btn btn-secondary btn-lg">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/admin-layout-footer.php'; ?>