<?php
require_once 'config/database.php';
require_once 'includes/security.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Setup Users - SMS</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #7c3aed; }
        .success { color: green; padding: 10px; background: #d1fae5; border-radius: 5px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #fee2e2; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #7c3aed; color: white; }
        .btn { display: inline-block; padding: 10px 20px; background: #7c3aed; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #6d28d9; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h2>🔧 User Setup Script</h2>";

// Password to hash
$password = "Test123456";
$password_hash = hash_password($password);

echo "<p><strong>Default Password:</strong> <code>$password</code></p>";
echo "<p><strong>Password Hash:</strong> <code style='font-size:0.8em;'>" . substr($password_hash, 0, 50) . "...</code></p>";
echo "<hr>";

// Clear existing test data (optional)
if (isset($_GET['reset'])) {
    mysqli_query($conn, "DELETE FROM Registration");
    mysqli_query($conn, "DELETE FROM Notification");
    mysqli_query($conn, "DELETE FROM Student");
    mysqli_query($conn, "DELETE FROM Lecturer");
    mysqli_query($conn, "DELETE FROM Admin");
    mysqli_query($conn, "DELETE FROM User");
    echo "<div class='success'>✅ All user data cleared!</div>";
}

// Insert/Update Users
$users = [
    ['admin', 'System Administrator', 'admin@utm.my', 'Admin', 'Admin Department'],
    ['lect001', 'Dr. Ahmad Lecturer', 'ahmad.lect@utm.my', 'Lecturer', 'Faculty of Computing'],
    ['lect002', 'Dr. Sarah Professor', 'sarah.lect@utm.my', 'Lecturer', 'Faculty of Computing'],
    ['A20EC0001', 'Syarah Qilah', 'syarah@graduate.utm.my', 'Student', 'Faculty of Computing'],
    ['A20EC0002', 'Ahmad Student', 'ahmad@graduate.utm.my', 'Student', 'Faculty of Computing']
];

echo "<h3>Creating Users...</h3>";

foreach ($users as $user) {
    $username = $user[0];
    $name = $user[1];
    $email = $user[2];
    $role = $user[3];
    $faculty = $user[4];
    
    // Check if user exists
    $check = mysqli_query($conn, "SELECT username FROM User WHERE username = '$username'");
    
    if (mysqli_num_rows($check) > 0) {
        // Update existing user
        $sql = "UPDATE User SET 
                name = '$name',
                email = '$email',
                password_hash = '$password_hash',
                role = '$role',
                status = 'Active',
                faculty = '$faculty'
                WHERE username = '$username'";
        
        if (mysqli_query($conn, $sql)) {
            echo "<div class='success'>✅ Updated: $username ($name) - $role</div>";
        } else {
            echo "<div class='error'>❌ Error updating $username: " . mysqli_error($conn) . "</div>";
        }
    } else {
        // Insert new user
        $sql = "INSERT INTO User (username, name, email, password_hash, role, status, faculty) 
                VALUES ('$username', '$name', '$email', '$password_hash', '$role', 'Active', '$faculty')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<div class='success'>✅ Created: $username ($name) - $role</div>";
        } else {
            echo "<div class='error'>❌ Error creating $username: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Insert role-specific data
echo "<h3>Creating Role-Specific Records...</h3>";

// Admin
$sql = "INSERT INTO Admin (adminID, username) VALUES ('ADM001', 'admin')
        ON DUPLICATE KEY UPDATE adminID = 'ADM001'";
if (mysqli_query($conn, $sql)) {
    echo "<div class='success'>✅ Admin record created</div>";
}

// Lecturers
$lecturers = [
    ['LECT001', 'lect001', 'Software Engineering'],
    ['LECT002', 'lect002', 'Data Engineering']
];

foreach ($lecturers as $lect) {
    $sql = "INSERT INTO Lecturer (lectID, username, department) 
            VALUES ('{$lect[0]}', '{$lect[1]}', '{$lect[2]}')
            ON DUPLICATE KEY UPDATE department = '{$lect[2]}'";
    if (mysqli_query($conn, $sql)) {
        echo "<div class='success'>✅ Lecturer {$lect[1]} created</div>";
    }
}

// Students
$students = [
    ['A20EC0001', 'A20EC0001', 3, 'Bachelor of Computer Science (Software Engineering)'],
    ['A20EC0002', 'A20EC0002', 2, 'Bachelor of Computer Science (Data Engineering)']
];

foreach ($students as $student) {
    $sql = "INSERT INTO Student (matricno, username, year, program) 
            VALUES ('{$student[0]}', '{$student[1]}', {$student[2]}, '{$student[3]}')
            ON DUPLICATE KEY UPDATE year = {$student[2]}, program = '{$student[3]}'";
    if (mysqli_query($conn, $sql)) {
        echo "<div class='success'>✅ Student {$student[1]} created</div>";
    }
}

// Display all users
echo "<h3>Current Users in Database:</h3>";
echo "<table>
<tr>
    <th>Username</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Status</th>
</tr>";

$result = mysqli_query($conn, "SELECT username, name, email, role, status FROM User ORDER BY role, username");
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
        <td><strong>{$row['username']}</strong></td>
        <td>{$row['name']}</td>
        <td>{$row['email']}</td>
        <td><span style='background: #ede9fe; color: #5b21b6; padding: 3px 8px; border-radius: 3px; font-size: 0.85em;'>{$row['role']}</span></td>
        <td>{$row['status']}</td>
    </tr>";
}
echo "</table>";

echo "<p style='margin-top: 30px; padding: 15px; background: #dbeafe; border-radius: 5px;'>
    <strong>📝 Test Credentials:</strong><br>
    All users have password: <code style='background: white; padding: 2px 6px; border-radius: 3px;'>Test123456</code>
</p>";

echo "<a href='auth/login.php' class='btn'>Go to Login Page →</a>";
echo " <a href='?reset=1' class='btn' style='background: #ef4444;' onclick='return confirm(\"This will delete ALL user data. Continue?\")'>Reset All Data</a>";

echo "</div></body></html>";
?>