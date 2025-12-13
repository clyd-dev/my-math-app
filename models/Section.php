<?php
// models/Section.php
class Section {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($name, $adminId) {
        $stmt = $this->db->prepare("INSERT INTO sections (name, created_by) VALUES (?, ?)");
        return $stmt->execute([$name, $adminId]);
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM sections ORDER BY name ASC");
        return $stmt->fetchAll();
    }
    
    public function exists($name) {
        $stmt = $this->db->prepare("SELECT id FROM sections WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch() !== false;
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM sections WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>