<?php
session_start();
require_once '../config/database.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: login.php");
    exit();
}

// Get and sanitize input
$username = sanitize_input($_POST['username'], $conn);
$password = $_POST['password']; // Don't sanitize password before verification

// Query to find user by username or email
$sql = "SELECT * FROM User 
        WHERE (username = '$username' OR email = '$username') 
        AND status = 'Active'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    $_SESSION['error'] = "Database error occurred. Please try again.";
    header("Location: login.php");
    exit();
}

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    
    // Verify password
    if (verify_password($password, $user['password_hash'])) {
        
        // Password correct - Set session variables
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_status'] = $user['status'];
        
        // Get role-specific data
        switch ($user['role']) {
            case 'Student':
                // Get student specific data
                $sql_student = "SELECT * FROM Student WHERE username = '{$user['username']}'";
                $result_student = mysqli_query($conn, $sql_student);
                if ($result_student && mysqli_num_rows($result_student) > 0) {
                    $student = mysqli_fetch_assoc($result_student);
                    $_SESSION['matricno'] = $student['matricno'];
                    $_SESSION['year'] = $student['year'];
                    $_SESSION['program'] = $student['program'];
                }
                
                // Send notification
                send_notification($conn, $user['username'], "Welcome back! You have successfully logged in.", "success");
                
                // Redirect to student dashboard
                header("Location: ../student/dashboard.php");
                break;
                
            case 'Lecturer':
                // Get lecturer specific data
                $sql_lecturer = "SELECT * FROM Lecturer WHERE username = '{$user['username']}'";
                $result_lecturer = mysqli_query($conn, $sql_lecturer);
                if ($result_lecturer && mysqli_num_rows($result_lecturer) > 0) {
                    $lecturer = mysqli_fetch_assoc($result_lecturer);
                    $_SESSION['lectID'] = $lecturer['lectID'];
                    $_SESSION['department'] = $lecturer['department'];
                }
                
                // Send notification
                send_notification($conn, $user['username'], "Welcome back! You have successfully logged in.", "success");
                
                // Redirect to lecturer dashboard
                header("Location: ../lecturer/dashboard.php");
                break;
                
            case 'Admin':
                // Get admin specific data
                $sql_admin = "SELECT * FROM Admin WHERE username = '{$user['username']}'";
                $result_admin = mysqli_query($conn, $sql_admin);
                if ($result_admin && mysqli_num_rows($result_admin) > 0) {
                    $admin = mysqli_fetch_assoc($result_admin);
                    $_SESSION['adminID'] = $admin['adminID'];
                }
                
                // Send notification
                send_notification($conn, $user['username'], "Admin access granted. Welcome back!", "info");
                
                // Redirect to admin dashboard
                header("Location: ../admin/dashboard.php");
                break;
                
            default:
                $_SESSION['error'] = "Invalid user role!";
                header("Location: login.php");
                exit();
        }
        
        exit();
        
    } else {
        // Password incorrect
        $_SESSION['error'] = "Incorrect password!";
        header("Location: login.php");
        exit();
    }
    
} else {
    // User not found
    $_SESSION['error'] = "User not found or account is inactive!";
    header("Location: login.php");
    exit();
}
?>