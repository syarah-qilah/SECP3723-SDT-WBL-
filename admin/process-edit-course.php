<?php
session_start();
require_once '../config/database.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Capture Form Data
    $old_code = mysqli_real_escape_string($conn, $_POST['original_course_code']);
    $new_code = mysqli_real_escape_string($conn, $_POST['course_code']);
    $name     = mysqli_real_escape_string($conn, $_POST['course_name']);
    $sem      = mysqli_real_escape_string($conn, $_POST['semester']);
    $credit   = mysqli_real_escape_string($conn, $_POST['credits']);
    $max      = mysqli_real_escape_string($conn, $_POST['max_students']);
    $lecturer = mysqli_real_escape_string($conn, $_POST['lecturer']);
    $schedule = mysqli_real_escape_string($conn, $_POST['schedule']);

    mysqli_begin_transaction($conn);

    try {
        // 2. Update the Course Table
        $sql = "UPDATE Course SET 
                c_code = '$new_code', 
                c_name = '$name', 
                semester = '$sem', 
                c_credit = '$credit', 
                max_student = '$max', 
                day_time = '$schedule' 
                WHERE c_code = '$old_code'";
        
        if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));

        // 3. Update the course_lecturer link
        mysqli_query($conn, "DELETE FROM course_lecturer WHERE c_code = '$new_code'");
        if (!empty($lecturer)) {
            mysqli_query($conn, "INSERT INTO course_lecturer (c_code, lectID) VALUES ('$new_code', '$lecturer')");
        }

        mysqli_commit($conn);
        $_SESSION['msg'] = "Course updated successfully!";
        
        // 4. THE FIX: Redirect back to the Details Page with the NEW code
        header("Location: course-details.php?code=" . $new_code);
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Update Failed: " . $e->getMessage();
        header("Location: manage-course.php");
        exit();
    }
}