<?php
// check_env.php - Verify environment variables
echo "<h2>Environment Check</h2>";

$envVars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'APP_ENV'];

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Variable</th><th>Status</th><th>Value</th></tr>";

foreach ($envVars as $var) {
    $value = getenv($var);
    $status = $value ? '✅' : '❌';
    $display = $value ? ($var === 'DB_PASS' ? '***hidden***' : $value) : 'NOT SET';
    echo "<tr><td>$var</td><td>$status</td><td>$display</td></tr>";
}

echo "</table>";

if (defined('DB_HOST')) {
    echo "<br><strong>Config.php values:</strong><br>";
    echo "DB_HOST: " . DB_HOST . "<br>";
    echo "DB_NAME: " . DB_NAME . "<br>";
}
?>
