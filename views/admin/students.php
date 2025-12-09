<?php
require_once '../../config/config.php';
require_once '../../models/Student.php';

if(!isset($_SESSION['admin_id'])) {
    redirect('views/admin/login.php');
}

$studentModel = new Student();
$students = $studentModel->getAll();

$pageTitle = 'Manage Students';
$isAdmin = true;

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $section = sanitize($_POST['section']);
    $password = $_POST['password'];
    
    if(strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        $studentModel = new Student();
        if($studentModel->register($name, $section,  $password)) {
            $success = 'Registration successful!';
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}
include '../../includes/admin-layout.php';
?>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper" style="margin-top: 70px;">
        <div class="container mt-4">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="text-white"><i class="fas fa-users"></i> Manage Students</h2>
                    <button class="btn btn-success" data-toggle="modal" data-target="#addStudentModal">
                        <i class="fas fa-plus"></i> Add New Student
                    </button>
                </div>
            </div>
            
            <?php if($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Students Table -->
            <div class="card shadow-lg">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="studentsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Section</th>
                                    <th>Type</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($students)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No students yet. Add your first student!</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($students as $index => $student): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($student['section']); ?></td>
                                            <td>
                                                <?php if($student['is_guest']): ?>
                                                    <span class="badge badge-warning">Guest</span>
                                                <?php else: ?>
                                                    <span class="badge badge-success">Registered</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="viewStudent(<?php echo $student['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning" onclick="editStudent(<?php echo $student['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteStudent(<?php echo $student['id']; ?>, '<?php echo addslashes($student['name']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Add New Student</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Section *</label>
                        <select name="section" class="form-control form-control-lg" required>
                            <option value="">Select</option>
                            <option>Diamond</option>
                            <option>Ruby</option>
                            <option>Jade</option>
                            <option>Garnet</option>
                            <option>Emerald</option>
                            <option>Topaz</option>
                            <option>Sapphire</option>
                            <option>Pearl</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password (Optional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank if student will register">
                        <small class="form-text text-muted">If blank, student can set password during registration</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function viewStudent(id) {
    window.location.href = 'view-student.php?id=' + id;
}

function editStudent(id) {
    window.location.href = 'edit-student.php?id=' + id;
}

function deleteStudent(id, name) {
    if(confirm('Are you sure you want to delete ' + name + '? All their quiz responses will also be deleted.')) {
        window.location.href = 'delete-student.php?id=' + id;
    }
}
</script>

<?php include '../../includes/admin-layout-footer.php'; ?>