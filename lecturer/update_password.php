<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Lecturer') {
    header("Location: ../auth/login.php");
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if ($new_pass !== $confirm_pass) {
        $_SESSION['msg'] = "Passwords do not match!";
        header("Location: profile.php");
        exit();
    }

    // Hash and update
    $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
    $sql = "UPDATE User SET password_hash = '$hashed_password' WHERE username = '$username'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "Password updated successfully!";
    } else {
        $_SESSION['msg'] = "Error updating password.";
    }
    
    header("Location: profile.php");
    exit();
}
?>