<?php
// 1. Load PHPMailer Library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// 2. Define the Function
function sendCredentialsEmail($name, $email, $raw_password, $lecturer_id) {
    
    $mail = new PHPMailer(true);

    try {
        // --- SERVER SETTINGS ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';       
        $mail->SMTPAuth   = true;                   
        $mail->Username   = 'syarahaqilah@graduate.utm.my'; 
        $mail->Password   = 'hwvm vwiv rmnv iuoj';   
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // --- RECIPIENTS ---
        $mail->setFrom('syarahaqilah@graduate.utm.my', 'School Admin'); 
        $mail->addAddress($email, $name);     

        // --- CONTENT ---
        $mail->isHTML(true);
        $mail->Subject = 'Your Lecturer Account Credentials';
        
        $mail->Body    = "
            <h3>Welcome to the Faculty, $name!</h3>
            <p>An account has been created for you.</p>
            <ul>
                <li><strong>Username:</strong> $lecturer_id</li>
                <li><strong>Password:</strong> $raw_password</li>
            </ul>
            <p>Please login and change your password.</p>
        ";

        $mail->send();
        return true; 

    } catch (Exception $e) {
        return false; 
    }
}
?>