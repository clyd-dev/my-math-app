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
?>
<?php include '../../includes/header.php'; ?>

<div class="container-scroller">
    <!-- Navbar -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row" style="background: linear-gradient(135deg, #667eea, #764ba2);">
        <div class="navbar-brand-wrapper d-flex justify-content-center">
            <a class="navbar-brand text-white" href="dashboard.php">
                <i class="fas fa-graduation-cap"></i> Math Quiz Admin
            </a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item">
                    <span class="text-white mr-3">Welcome, <?php echo $_SESSION['admin_name']; ?></span>
                    <a href="logout.php" class="btn btn-sm btn-light">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid page-body-wrapper" style="margin-top: 70px;">
        <div class="container mt-4">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-white"><i class="fas fa-users"></i> Manage Students</h2>
                            <p class="text-white-50">Add, edit, or remove students from the system</p>
                        </div>
                        <div>
                            <a href="dashboard.php" class="btn btn-light mr-2">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                            <button class="btn btn-success" data-toggle="modal" data-target="#addStudentModal">
                                <i class="fas fa-plus"></i> Add New Student
                            </button>
                        </div>
                    </div>
                </div>
            </div>

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
            <form method="POST" action="/student/register.php">
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

<?php include '../../includes/footer.php'; ?>