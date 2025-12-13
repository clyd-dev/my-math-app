<?php
// views/admin/delete-section.php
require_once '../../config/config.php';
require_once '../../controllers/SectionController.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$controller = new SectionController();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id > 0) {
    $result = $controller->delete($id);
    $_SESSION[$result['success'] ? 'success_message' : 'error_message'] = $result['message'];
}

header("Location: sections.php");
exit();
?>