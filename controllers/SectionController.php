<?php
// controllers/SectionController.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Section.php';

class SectionController {
    private $sectionModel;
    
    public function __construct() {
        $this->sectionModel = new Section();
    }
    
    public function create($name) {
        if (empty($name)) {
            return ['success' => false, 'message' => 'Section name is required'];
        }
        if ($this->sectionModel->exists($name)) {
            return ['success' => false, 'message' => 'Section already exists'];
        }
        if ($this->sectionModel->create($name, $_SESSION['admin_id'])) {
            return ['success' => true, 'message' => 'Section created successfully'];
        }
        return ['success' => false, 'message' => 'Failed to create section'];
    }
    
    public function getAll() {
        return $this->sectionModel->getAll();
    }
    
    public function delete($id) {
        if ($this->sectionModel->delete($id)) {
            return ['success' => true, 'message' => 'Section deleted successfully'];
        }
        return ['success' => false, 'message' => 'Failed to delete section'];
    }
}
?>