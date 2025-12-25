<?php
// diagnose_db.php - Comprehensive database diagnostics
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Diagnostics</h2>";
echo "<pre>";

// Step 1: Check environment variables
echo "=== STEP 1: Environment Variables ===\n";
$envVars = [
    'DB_HOST' => getenv('DB_HOST') ?: 'db.fr-pari1.bengt.wasmernet.com',
    'DB_PORT' => getenv('DB_PORT') ?: '10272',
    'DB_NAME' => getenv('DB_NAME') ?: 'db9Adrv8bASbEuJEJtRS8rQy',
    'DB_USER' => getenv('DB_USER') ?: 'caeffd9273c18000685af25dc504',
    'DB_PASS' => getenv('DB_PASS') ? '***SET***' : '***NOT SET***'
];

foreach ($envVars as $key => $value) {
    echo "$key: $value\n";
}

// Step 2: Check PHP extensions
echo "\n=== STEP 2: PHP Extensions ===\n";
echo "PDO: " . (extension_loaded('pdo') ? '✅ Loaded' : '❌ Missing') . "\n";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Loaded' : '❌ Missing') . "\n";
echo "MySQLi: " . (extension_loaded('mysqli') ? '✅ Loaded' : '❌ Missing') . "\n";

// Step 3: Test DNS resolution
echo "\n=== STEP 3: DNS Resolution ===\n";
$host = getenv('DB_HOST') ?: 'db.fr-pari1.bengt.wasmernet.com';
$ip = gethostbyname($host);
echo "Host: $host\n";
echo "Resolved IP: $ip\n";
echo "DNS Status: " . ($ip !== $host ? '✅ Resolved' : '❌ Failed') . "\n";

// Step 4: Test socket connection
echo "\n=== STEP 4: Socket Connection Test ===\n";
$port = getenv('DB_PORT') ?: '10272';
$errno = 0;
$errstr = '';
$timeout = 5;

echo "Attempting connection to $host:$port...\n";
$socket = @fsockopen($host, $port, $errno, $errstr, $timeout);

if ($socket) {
    echo "✅ Socket connection successful!\n";
    fclose($socket);
} else {
    echo "❌ Socket connection failed\n";
    echo "Error ($errno): $errstr\n";
}

// Step 5: Test PDO connection (without SSL)
echo "\n=== STEP 5: PDO Connection Test (No SSL) ===\n";
$dbHost = getenv('DB_HOST') ?: 'db.fr-pari1.bengt.wasmernet.com';
$dbPort = getenv('DB_PORT') ?: '10272';
$dbName = getenv('DB_NAME') ?: 'db9Adrv8bASbEuJEJtRS8rQy';
$dbUser = getenv('DB_USER') ?: 'caeffd9273c18000685af25dc504';
$dbPass = getenv('DB_PASS') ?: '0694caef-fd92-7876-8000-06aa1f8d0f1c';

$dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
echo "DSN: $dsn\n";
echo "User: $dbUser\n";

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10,
        PDO::MYSQL_ATTR_CONNECT_TIMEOUT => 10,
    ];
    
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    echo "✅ PDO connection successful (No SSL)!\n";
    
    $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as db");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "MySQL Version: " . $result['version'] . "\n";
    echo "Connected to DB: " . $result['db'] . "\n";
    
} catch(PDOException $e) {
    echo "❌ PDO connection failed (No SSL)\n";
    echo "Error: " . $e->getMessage() . "\n";
    
    // Step 6: Try with SSL
    echo "\n=== STEP 6: PDO Connection Test (With SSL) ===\n";
    try {
        $sslOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 10,
            PDO::MYSQL_ATTR_CONNECT_TIMEOUT => 10,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ];
        
        $pdo = new PDO($dsn, $dbUser, $dbPass, $sslOptions);
        echo "✅ PDO connection successful (With SSL)!\n";
        
        $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as db");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "MySQL Version: " . $result['version'] . "\n";
        echo "Connected to DB: " . $result['db'] . "\n";
        
    } catch(PDOException $e2) {
        echo "❌ PDO connection failed (With SSL)\n";
        echo "Error: " . $e2->getMessage() . "\n";
    }
}

// Step 7: Try MySQLi as alternative
echo "\n=== STEP 7: MySQLi Connection Test ===\n";
if (extension_loaded('mysqli')) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
        echo "✅ MySQLi connection successful!\n";
        echo "Server info: " . $mysqli->server_info . "\n";
        $mysqli->close();
    } catch(Exception $e) {
        echo "❌ MySQLi connection failed\n";
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠️ MySQLi extension not available\n";
}

echo "\n=== Diagnosis Complete ===\n";
echo "</pre>";
?>
