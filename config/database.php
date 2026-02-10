<?php
// Railway Database Configuration
define('DB_HOST', 'centerbeam.proxy.rlwy.net');
define('DB_USER', 'root');
define('DB_PASS', 'wYRESbevJrlkSrCSPLdKRCkfeoGtXXxk');
define('DB_NAME', 'railway');

// Railway port
define('DB_PORT', 8080);

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, 'utf8mb4');
?>
