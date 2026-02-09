<?php
session_start();
require_once '../config/database.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matric = mysqli_real_escape_string($conn, $_POST['student_id']);
    $code = mysqli_real_escape_string($conn, $_POST['course_code']);
    $sem = mysqli_real_escape_string($conn, $_POST['semester']); 
    $session = mysqli_real_escape_string($conn, $_POST['session']);

    // 1. Check if student is ALREADY registered for this course
    $check_dup = mysqli_query($conn, "SELECT regisID FROM Registration WHERE matricno = '$matric' AND c_code = '$code'");
    if (mysqli_num_rows($check_dup) > 0) {
        $_SESSION['error'] = "Student is already registered for this course!";
        header("Location: manage-regis.php");
        exit();
    }

    // 2. Check Course Capacity
    // Get Max Capacity
    $course_query = mysqli_query($conn, "SELECT max_student FROM Course WHERE c_code = '$code'");
    $course_data = mysqli_fetch_assoc($course_query);
    $max_students = $course_data['max_student'];

    // Get Current Count
    $count_query = mysqli_query($conn, "SELECT COUNT(*) as enrolled FROM Registration WHERE c_code = '$code' AND regisStat = 'Approved'");
    $count_data = mysqli_fetch_assoc($count_query);
    $current_enrolled = $count_data['enrolled'];

    // 3. LOGIC: Block if Full (No Pending Status)
    if ($current_enrolled >= $max_students) {
        $_SESSION['error'] = "Registration Failed: Course $code is FULL ($current_enrolled/$max_students).";
        header("Location: manage-regis.php");
        exit();
    }

    // 4. Insert as APPROVED
    $sql = "INSERT INTO Registration (matricno, c_code, academic_session, semester, regisStat, regisDate) 
            VALUES ('$matric', '$code', '$session', '$sem', 'Approved', NOW())";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "Student registered successfully.";
    } else {
        $_SESSION['error'] = "Database Error: " . mysqli_error($conn);
    }

    header("Location: manage-regis.php");
    exit();
}
?>