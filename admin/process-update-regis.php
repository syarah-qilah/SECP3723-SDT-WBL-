<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $original_code = mysqli_real_escape_string($conn, $_POST['original_course_code']);
    $code = mysqli_real_escape_string($conn, $_POST['course_code']);
    $name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $sem = (int) $_POST['semester'];
    $credits = (int) $_POST['credits'];
    $max = (int) $_POST['max_students'];
    $schedule = mysqli_real_escape_string($conn, $_POST['schedule']);
    $lect_id = mysqli_real_escape_string($conn, $_POST['lecturer']);

    // 1. Update Course Table
    $sql = "UPDATE Course SET 
            c_code = '$code', 
            c_name = '$name', 
            semester = $sem, 
            c_credit = $credits, 
            max_student = $max, 
            day_time = '$schedule' 
            WHERE c_code = '$original_code'";

    if (mysqli_query($conn, $sql)) {
        
        // 2. Update Lecturer Assignment
        // First, remove any existing assignment for this course
        $del_link = "DELETE FROM course_lecturer WHERE c_code = '$original_code'"; // Use original code in case code changed
        mysqli_query($conn, $del_link);

        // Then, add the new assignment if a lecturer is selected
        if (!empty($lect_id)) {
            // Use the NEW code in case the user changed the course code
            $new_link = "INSERT INTO course_lecturer (lectID, c_code) VALUES ('$lect_id', '$code')";
            mysqli_query($conn, $new_link);
        }

        $_SESSION['msg'] = "Course details updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating course: " . mysqli_error($conn);
    }

    header("Location: manage-courses.php");
    exit();
}
?>