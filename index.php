<?php
session_start();

// If logged in, redirect to appropriate dashboard
if (isset($_SESSION['username'])) {
    switch ($_SESSION['user_role']) {
        case 'Student':
            header("Location: student/dashboard.php");
            break;
        case 'Lecturer':
            header("Location: lecturer/dashboard.php");
            break;
        case 'Admin':
            header("Location: admin/dashboard.php");
            break;
        default:
            header("Location: auth/login.php");
    }
} else {
    // Not logged in - redirect to login
    header("Location: auth/login.php");
}
exit();
?>