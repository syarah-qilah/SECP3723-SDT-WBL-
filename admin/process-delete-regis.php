<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_id = mysqli_real_escape_string($conn, $_POST['reg_id']);

    // Delete the registration record
    $sql = "DELETE FROM Registration WHERE regisID = '$reg_id'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "Course dropped successfully.";
    } else {
        $_SESSION['error'] = "Error deleting record: " . mysqli_error($conn);
    }

    header("Location: manage-regis.php");
    exit();
}
?>