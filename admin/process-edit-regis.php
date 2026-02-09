<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_id = mysqli_real_escape_string($conn, $_POST['reg_id']);
    $new_code = mysqli_real_escape_string($conn, $_POST['course_code']);

    // 1. Check Capacity of the NEW Course
    $course_query = mysqli_query($conn, "SELECT max_student FROM Course WHERE c_code = '$new_code'");
    $course_data = mysqli_fetch_assoc($course_query);
    $max_students = $course_data['max_student'];

    $count_query = mysqli_query($conn, "SELECT COUNT(*) as enrolled FROM Registration WHERE c_code = '$new_code' AND regisStat = 'Approved'");
    $count_data = mysqli_fetch_assoc($count_query);
    $current_enrolled = $count_data['enrolled'];

    // 2. LOGIC CHANGE: Block if Full
    if ($current_enrolled >= $max_students) {
        $_SESSION['error'] = "Update Failed: Course $new_code is FULL ($current_enrolled/$max_students).";
        header("Location: manage-regis.php");
        exit();
    }

    // 3. Update to APPROVED
    $sql = "UPDATE Registration 
            SET c_code = '$new_code', regisStat = 'Approved' 
            WHERE regisID = '$reg_id'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "Registration moved to $new_code successfully.";
    } else {
        $_SESSION['error'] = "Error updating registration: " . mysqli_error($conn);
    }

    header("Location: manage-regis.php");
    exit();
}
?>