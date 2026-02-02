<?php
echo "<!DOCTYPE html>
<html>
<head>
    <title>SMS Setup Test</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        h2 { color: #7c3aed; }
        h3 { color: #333; border-bottom: 2px solid #7c3aed; padding-bottom: 5px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h2>🧪 Testing SMS Setup</h2>";

// Test 1: Database Connection
echo "<h3>1. Testing Database Connection...</h3>";
require_once 'config/database.php';
if ($conn) {
    echo "<span class='success'>✅ Database connected successfully!</span><br>";
    echo "Database name: <strong>" . DB_NAME . "</strong><br><br>";
} else {
    echo "<span class='error'>❌ Database connection failed!</span><br><br>";
}

// Test 2: Security Functions
echo "<h3>2. Testing Security Functions...</h3>";
require_once 'includes/security.php';

$test_password = "Test123456";
$hashed = hash_password($test_password);
echo "Original password: <code>$test_password</code><br>";
echo "Hashed password: <code>" . substr($hashed, 0, 40) . "...</code><br>";
echo "Verification: " . (verify_password($test_password, $hashed) ? "<span class='success'>✅ Pass</span>" : "<span class='error'>❌ Fail</span>") . "<br><br>";

// Test 3: Session Functions
echo "<h3>3. Testing Session Functions...</h3>";
require_once 'includes/session.php';
echo "<span class='success'>✅ Session functions loaded</span><br><br>";

// Test 4: Helper Functions
echo "<h3>4. Testing Helper Functions...</h3>";
require_once 'includes/functions.php';
echo "Current Semester: <strong>" . get_current_semester() . "</strong> <span class='success'>✅</span><br>";
echo "Date formatting: <strong>" . format_date(date('Y-m-d')) . "</strong> <span class='success'>✅</span><br><br>";

// Test 5: Check Tables
echo "<h3>5. Checking Database Tables...</h3>";
$tables = ['User', 'Student', 'Lecturer', 'Admin', 'Course', 'Registration', 'Notification', 'Course_Lecturer'];
foreach ($tables as $table) {
    $sql = "SHOW TABLES LIKE '$table'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        echo "<span class='success'>✅ Table '$table' exists</span><br>";
    } else {
        echo "<span class='error'>❌ Table '$table' not found!</span><br>";
    }
}

echo "<br><h3>🎉 Setup Test Complete!</h3>";
echo "<p>If all tests passed, you're ready to move to Phase 4 (Authentication).</p>";

echo "</div></body></html>";
?>
