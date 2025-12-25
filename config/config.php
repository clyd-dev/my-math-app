<?php
// config.php - Database Configuration

// Database Configuration
define('DB_HOST', 'db.fr-pari1.bengt.wasmernet.com');
define('DB_USER', 'caeffd9273c18000685af25dc504');
define('DB_PASS', '0694caef-fd92-7876-8000-06aa1f8d0f1c');
define('DB_NAME', 'db9Adrv8bASbEuJEJtRS8rQy');

// Application Configuration
define('APP_NAME', 'Math Adventure Quiz Game');
define('APP_URL', 'http://localhost/my-math-app');

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
    if (!preg_match('/^http/', $url)) {
        $url = APP_URL . '/' . ltrim($url, '/');
    }
    header("Location: $url");
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
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}
?>
