<?php
session_start();
require_once '../config/database.php';
require_once '../includes/security.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    die("Access Denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    
    // 1. Sanitize Inputs
    $email = sanitize_input($_POST['email'], $conn);
    $address = sanitize_input($_POST['address'], $conn);
    $ic = sanitize_input($_POST['ic_number'], $conn);
    
    // 2. Update Database
    $sql = "UPDATE User SET email='$email', address='$address', ic_number='$ic' WHERE username='$username'";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "Profile updated successfully!";
    } else {
        $_SESSION['msg'] = "Error updating profile: " . mysqli_error($conn);
    }
    
    header("Location: profile.php");
    exit();
}
?>