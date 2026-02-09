<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Lecturer') {
    header("Location: ../auth/login.php");
    exit();
}

$lect_id = $_SESSION['key_id'];
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $ic_no = mysqli_real_escape_string($conn, $_POST['ic_no']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $office_room = mysqli_real_escape_string($conn, $_POST['office_room']);

    // 1. Update User Table (Email)
    $sql_user = "UPDATE User SET email = '$email' WHERE username = '$username'";
    mysqli_query($conn, $sql_user);

    // 2. Update Lecturer Table (Details)
    $sql_lect = "UPDATE Lecturer SET ic_no = '$ic_no', address = '$address', office_room = '$office_room' WHERE lectID = '$lect_id'";
    
    if (mysqli_query($conn, $sql_lect)) {
        $_SESSION['msg'] = "Profile details updated successfully!";
    } else {
        $_SESSION['msg'] = "Error updating profile.";
    }

    header("Location: profile.php");
    exit();
}
?>