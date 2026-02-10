<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = mysqli_real_escape_string($conn, $_POST['course_code']);
    $name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $sem = (int) $_POST['semester'];
    $credits = (int) $_POST['credits'];
    $max = (int) $_POST['max_students'];
    $schedule = mysqli_real_escape_string($conn, $_POST['schedule']);
    $lect_id = mysqli_real_escape_string($conn, $_POST['lecturer']);

    // 1. Insert into Course Table
  
    $sql = "INSERT INTO Course (c_code, c_name, semester, c_credit, max_student, day_time, section, academic_session) 
            VALUES ('$code', '$name', $sem, $credits, $max, '$schedule', '01', '2025/2026')";

    if (mysqli_query($conn, $sql)) {
        
        // 2. Assign Lecturer (if selected)
        if (!empty($lect_id)) {
            $sql_link = "INSERT INTO course_lecturer (lectID, c_code) VALUES ('$lect_id', '$code')";
            mysqli_query($conn, $sql_link);
        }

        $_SESSION['msg'] = "Course '$name' added successfully!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }

    header("Location: manage-courses.php");
    exit();
}
?>