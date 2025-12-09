<?php
// config.php - Database Configuration

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'math_quiz_game');

// Application Configuration
define('APP_NAME', 'Math Adventure Quiz Game');
define('APP_URL', 'http://localhost/math-quiz');

// Session Configuration
define('SESSION_NAME', 'math_quiz_session');
define('SESSION_LIFETIME', 7200); // 2 hours

// Database Connection Class
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                )
            );
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
}

// Helper Functions
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function generateShareCode() {
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']) || isset($_SESSION['student_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function isStudent() {
    return isset($_SESSION['student_id']);
}

// Start session
session_name(SESSION_NAME);
session_start();
?>