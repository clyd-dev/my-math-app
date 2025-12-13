<?php
// views/admin/sections.php
require_once '../../config/config.php';
require_once '../../controllers/SectionController.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/login.php");
    exit();
}

$sectionController = new SectionController();
$sections = $sectionController->getAll();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_section'])) {
    $name = sanitize($_POST['name']);
    $result = $sectionController->create($name);
    if($result['success']) {
        $_SESSION['success_message'] = $result['message'];
    } else {
        $_SESSION['error_message'] = $result['message'];
    }
    header("Location: sections.php");
    exit();
}

$pageTitle = 'Section Management';
include '../../includes/admin-layout.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-white">Manage Sections</h2>
            <p class="text-white-50">Create and manage student sections</p>
        </div>
        <div class="col-md-4 text-right">
            <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#addSectionModal">
                Add New Section
            </button>
        </div>
    </div>

    <div class="card shadow-lg">
        <div class="card-body">
            <?php if(empty($sections)): ?>
                <p class="text-center text-muted">No sections created yet.</p>
            <?php else: ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Section Name</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sections as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($s['created_at'])); ?></td>
                            <td>
                                <a href="delete-section.php?id=<?php echo $s['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Delete section <?php echo addslashes($s['name']); ?>?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addSectionModal">
    <div class="modal-dialog">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add New Section</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <input type="text" name="name" class="form-control" placeholder="e.g., Grade 7 - Diamond" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_section" class="btn btn-success">Add Section</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/admin-layout-footer.php'; ?>