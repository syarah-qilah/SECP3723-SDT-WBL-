<?php
session_start();

// 1. Include Database
require_once '../config/database.php';

// 2. INCLUDE EMAIL CONFIG (Added this line)
require_once '../includes/email-config.php'; 

// 3. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 4. Get Form Data
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $matric = mysqli_real_escape_string($conn, $_POST['matric_no']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $faculty = mysqli_real_escape_string($conn, $_POST['faculty']);
    $program = mysqli_real_escape_string($conn, $_POST['program']);
    
    // 5. Credentials (Username = Matric No)
    $username = $matric; 
    
    // Keep raw password for email
    $raw_password = $_POST['password']; 
    
    // Hash the password for security
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

    // 6. Check for Duplicate Matric/Username
    $check = mysqli_query($conn, "SELECT username FROM User WHERE username = '$username'");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = "Error: Student with ID '$matric' already exists!";
        header("Location: manage-student.php");
        exit();
    }

    // 7. Insert into USER table (Login Info)
    $sql_user = "INSERT INTO User (username, password_hash, name, role, email, faculty) 
                 VALUES ('$username', '$hashed_password', '$name', 'Student', '$email', '$faculty')";
    
    if (mysqli_query($conn, $sql_user)) {
        
        // 8. Insert into STUDENT table (Profile Info)
        $sql_student = "INSERT INTO Student (matricno, username, program, year) 
                        VALUES ('$matric', '$username', '$program', 1)";
        
        if (mysqli_query($conn, $sql_student)) {
            
            // --- NEW: SEND EMAIL HERE ---
            // We pass the Name, Email, Password, and Matric No (as the ID)
            $emailSent = sendCredentialsEmail($name, $email, $raw_password, $matric);

            if($emailSent) {
                $_SESSION['msg'] = "Student <strong>$name</strong> ($matric) added & Email Sent!";
            } else {
                $_SESSION['msg'] = "Student added, but <strong>Email Failed</strong> to send.";
            }
            // -----------------------------

        } else {
            // Rollback: Delete the User if Student insert fails
            mysqli_query($conn, "DELETE FROM User WHERE username = '$username'");
            $_SESSION['error'] = "Database Error (Student Table): " . mysqli_error($conn);
        }

    } else {
        $_SESSION['error'] = "Database Error (User Table): " . mysqli_error($conn);
    }

    // 9. Redirect back to list
    header("Location: manage-student.php");
    exit();
}
?>