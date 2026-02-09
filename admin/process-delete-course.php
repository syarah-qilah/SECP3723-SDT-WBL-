<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);

    // 1. Delete links in course_lecturer first (Foreign Key constraint)
    $sql_link = "DELETE FROM course_lecturer WHERE c_code = '$course_id'";
    mysqli_query($conn, $sql_link);

    // 2. Delete student registrations (Optional: depends if you want to keep history)
    $sql_reg = "DELETE FROM Registration WHERE c_code = '$course_id'";
    mysqli_query($conn, $sql_reg);

    // 3. Finally, Delete the Course
    $sql = "DELETE FROM Course WHERE c_code = '$course_id'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "Course deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting course: " . mysqli_error($conn);
    }

    header("Location: manage-courses.php");
    exit();
}
?>