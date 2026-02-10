<?php
session_start();

// 1. Include Database & Email Configuration
require_once '../config/database.php';

require_once '../includes/email-config.php'; 

// 2. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 3. Get Data from Form
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $lect_id = mysqli_real_escape_string($conn, $_POST['lect_id']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $faculty = mysqli_real_escape_string($conn, $_POST['faculty']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $office = mysqli_real_escape_string($conn, $_POST['office']);
    
 
    $username = $lect_id; 
    
 
    $raw_password = $_POST['password']; 
    
    
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

  
    $checkQuery = "SELECT * FROM User WHERE username='$username' OR email='$email'";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if(mysqli_num_rows($checkResult) > 0) {
        $_SESSION['error'] = "Error: Lecturer ID or Email already exists!";
        header("Location: manage-lect.php");
        exit();
    }

    $sql_user = "INSERT INTO User (username, password_hash, name, role, email, faculty) 
                 VALUES ('$username', '$hashed_password', '$name', 'Lecturer', '$email', '$faculty')";
    
    if (mysqli_query($conn, $sql_user)) {
        
   
        $sql_lect = "INSERT INTO Lecturer (lectID, username, department, office_room, position) 
                     VALUES ('$lect_id', '$username', '$department', '$office', 'Lecturer')";
        
        if (mysqli_query($conn, $sql_lect)) {
            
          
            $emailSent = sendCredentialsEmail($name, $email, $raw_password, $lect_id);

            if($emailSent) {
                $_SESSION['msg'] = "Lecturer <strong>$name</strong> added & Email Sent!";
            } else {
                $_SESSION['msg'] = "Lecturer added, but <strong>Email Failed</strong> to send.";
            }
           

        } else {
         
            mysqli_query($conn, "DELETE FROM User WHERE username = '$username'");
            $_SESSION['error'] = "Database Error (Lecturer Table): " . mysqli_error($conn);
        }

    } else {
        $_SESSION['error'] = "Database Error (User Table): " . mysqli_error($conn);
    }

    header("Location: manage-lect.php");
    exit();
}
?>