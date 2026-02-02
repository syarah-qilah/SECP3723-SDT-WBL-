<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Admin Dashboard</h1>";
echo "<pre>";

echo "Step 1: Loading config...\n";
require_once '../config/database.php';
echo "✓ Database loaded\n";

echo "\nStep 2: Loading session...\n";
require_once '../includes/session.php';
echo "✓ Session loaded\n";

echo "\nStep 3: Loading functions...\n";
require_once '../includes/functions.php';
echo "✓ Functions loaded\n";

echo "\nStep 4: Check login...\n";
check_login();
echo "✓ User is logged in\n";

echo "\nStep 5: Check role...\n";
check_role('Admin');
echo "✓ User is Admin\n";

echo "\nStep 6: Get current user...\n";
$user = get_current_user();
echo "User data:\n";
print_r($user);

echo "\nStep 7: Get username...\n";
$username = $user['username'];
echo "Username: $username\n";

echo "\nStep 8: Query admin table...\n";
$sql_admin = "SELECT * FROM Admin WHERE username = '$username'";
echo "SQL: $sql_admin\n";

$result_admin = mysqli_query($conn, $sql_admin);
echo "Query executed\n";

if (!$result_admin) {
    echo "❌ Query failed: " . mysqli_error($conn) . "\n";
} else {
    echo "✓ Query successful\n";
    echo "Number of rows: " . mysqli_num_rows($result_admin) . "\n";
}

echo "\nStep 9: Fetch admin data...\n";
$admin = mysqli_fetch_assoc($result_admin);
echo "Admin data:\n";
print_r($admin);

if (!$admin) {
    echo "❌ No admin record found!\n";
    
    echo "\nStep 10: Checking what's in Admin table...\n";
    $check_sql = "SELECT * FROM Admin";
    $check_result = mysqli_query($conn, $check_sql);
    echo "All admins in database:\n";
    while ($row = mysqli_fetch_assoc($check_result)) {
        print_r($row);
    }
} else {
    echo "✓ Admin record found\n";
}

echo "</pre>";

echo "<hr>";
echo "<a href='dashboard.php'>Try Dashboard Again</a> | ";
echo "<a href='../test-login.php'>Check Session</a>";
?>