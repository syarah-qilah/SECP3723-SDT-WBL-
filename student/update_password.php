<?php
session_start();
require_once '../config/database.php';
require_once '../includes/security.php';

if (!isset($_SESSION['role'])) { die("Access Denied"); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $new = $_POST['new_pass'];
    $confirm = $_POST['confirm_pass'];
    
    if ($new === $confirm) {
        // 1. Hash the new password
        $hash = password_hash($new, PASSWORD_DEFAULT);
        
        // 2. Update DB
        $sql = "UPDATE User SET password_hash='$hash' WHERE username='$username'";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['msg'] = "Password changed successfully!";
        } else {
            $_SESSION['msg'] = "Error changing password.";
        }
    } else {
        $_SESSION['msg'] = "Passwords do not match!";
    }
    
    header("Location: profile.php");
    exit();
}
?>