<?php
// views/admin/students.php - FIXED: Section dropdown in "Add Student" modal
require_once '../../config/config.php';
require_once '../../controllers/StudentController.php';
require_once '../../models/Section.php';   // ← THIS WAS MISSING!

$studentController = new StudentController();
$sectionModel = new Section();               // Now we can get sections
$sections = $sectionModel->getAll();

// Handle Add Student Form Submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $name = sanitize($_POST['name']);
    $section = sanitize($_POST['section']);
    $password = isset($_POST['password']) && !empty($_POST['password']) ? $_POST['password'] : null;
    
    if($password) {
        $result = $studentController->register($name, $section, $password);
    } else {
        $result = $studentController->createGuest($name, $section);
    }
    
    if($result['success']) {
        $_SESSION['success_message'] = $result['message'];
    } else {
        $_SESSION['error_message'] = $result['message'];
    }
    
    header("Location: students.php");
    exit();
}

$students = $studentController->getAllStudents();
$totalStudents = $studentController->countStudents();
$registeredCount = $studentController->countRegisteredStudents();
$guestCount = $studentController->countGuestStudents();

$pageTitle = 'Manage Students';
include '../../includes/admin-layout.php';
?>
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-white"><i class="fas fa-users"></i> Manage Students</h2>
            <p class="text-white-50">Add, edit, view, or remove students from the system</p>
        </div>
        <div class="col-md-4 text-right">
            <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#addStudentModal">
                <i class="fas fa-plus"></i> Add New Student
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <h5>Total Students</h5>
                    <h2 class="mb-0"><?php echo $totalStudents; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <h5>Registered</h5>
                    <h2 class="mb-0"><?php echo $registeredCount; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <h5>Guest Accounts</h5>
                    <h2 class="mb-0"><?php echo $guestCount; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow-lg">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-table"></i> All Students</h4>
                <div>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search students..." onkeyup="searchTable()">
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if(empty($students)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-5x text-muted mb-3"></i>
                    <h4 class="text-muted">No Students Yet</h4>
                    <p class="text-muted">Add your first student to get started!</p>
                    <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#addStudentModal">
                        <i class="fas fa-plus"></i> Add Student
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="studentsTable">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Section</th>
                                <th>Account Type</th>
                                <th>Registered Date</th>
                                <th>Quiz Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($students as $index => $student): ?>
                                <?php 
                                $quizHistory = $studentController->getQuizHistory($student['id']);
                                $quizCount = count($quizHistory);
                                ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?php echo htmlspecialchars($student['section']); ?></span>
                                    </td>
                                    <td>
                                        <?php if($student['is_guest']): ?>
                                            <span class="badge badge-warning"><i class="fas fa-user"></i> Guest</span>
                                        <?php else: ?>
                                            <span class="badge badge-success"><i class="fas fa-user-check"></i> Registered</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                                    <td>
                                        <span class="badge badge-primary"><?php echo $quizCount; ?> quiz<?php echo $quizCount != 1 ? 'zes' : ''; ?></span>
                                    </td>
                                    <td>
                                        <a href="<?php echo APP_URL; ?>/views/admin/view-student.php?id=<?php echo $student['id']; ?>" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/views/admin/edit-student.php?id=<?php echo $student['id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteStudent(<?php echo $student['id']; ?>, '<?php echo addslashes($student['name']); ?>')" 
                                                class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal">
    <div class="modal-dialog modal-lg">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add New Student</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Section *</label>
                                <select name="section" class="form-control" required>
                                    <option value="">Choose Section</option>
                                    <?php foreach($sections as $sec): ?>
                                        <option value="<?php echo htmlspecialchars($sec['name']); ?>">
                                            <?php echo htmlspecialchars($sec['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password (leave blank = guest account)</label>
                        <input type="password" name="password" class="form-control" placeholder="Min 6 characters">
                        <small class="text-muted">If blank → student can login without password (guest mode)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_student" class="btn btn-success">Add Student</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
// Search functionality
function searchTable() {
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("studentsTable");
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
        td = tr[i].getElementsByTagName("td");
        for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break;
                }
            }
        }
    }
}

// Delete student with confirmation
function deleteStudent(id, name) {
    if(confirm('⚠️ WARNING ⚠️\n\nAre you sure you want to delete ' + name + '?\n\n' +
               'This will also delete:\n' +
               '• All their quiz responses\n' +
               '• All their quiz history\n' +
               '• All their data\n\n' +
               'This action CANNOT be undone!')) {
        window.location.href = '<?php echo APP_URL; ?>/views/admin/delete-student.php?id=' + id;
    }
}
</script>

<?php include '../../includes/admin-layout-footer.php'; ?>