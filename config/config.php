<?php
// config.php - Database Configuration for Wasmer.io

// Database Configuration - Read from environment or fallback to defaults
define('DB_HOST', getenv('DB_HOST') ?: 'db.fr-pari1.bengt.wasmernet.com:10272');
define('DB_USER', getenv('DB_USER') ?: 'caeffd9273c18000685af25dc504');
define('DB_PASS', getenv('DB_PASS') ?: '0694caef-fd92-7876-8000-06aa1f8d0f1c');
define('DB_NAME', getenv('DB_NAME') ?: 'db9Adrv8bASbEuJEJtRS8rQy');

// Application Configuration
$isWasmer = isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'wasmer.io') !== false;
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';

define('APP_NAME', 'Math Adventure Quiz Game');
define('APP_URL', $isWasmer 
    ? $protocol . $_SERVER['HTTP_HOST'] 
    : 'http://localhost/my-math-app'
);
define('APP_ENV', getenv('APP_ENV') ?: 'development');

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
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                )
            );
        } catch(PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            
            if (APP_ENV === 'development') {
                die("Connection failed: " . $e->getMessage());
            } else {
                die("Database connection error. Please contact support.");
            }
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
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    if (!preg_match('/^https?:/', $url)) {
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
    
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');
    
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
    
    if (isset($_SESSION['LAST_ACTIVITY']) && 
        (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_LIFETIME)) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['LAST_ACTIVITY'] = time();
}

// Hide errors in production
if (APP_ENV === 'production') {
    ini_set('display_errors', 0);
    error_reporting(0);
}
?>
