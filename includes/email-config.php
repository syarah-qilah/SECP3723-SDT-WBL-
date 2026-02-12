<?php
// 1. Load PHPMailer Library correctly
// FIX: The Namespace is 'PHPMailer\PHPMailer\Class', not just 'PHPMailer\Class'
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// FIX: Use __DIR__ to find files relative to THIS file, not the admin folder.
// This assumes your PHPMailer folder is inside the 'includes' folder.
require __DIR__ . '/PHPMailer/Exception.php';
require __DIR__ . '/PHPMailer/PHPMailer.php';
require __DIR__ . '/PHPMailer/SMTP.php';

function sendCredentialsEmail($name, $email, $raw_password, $lecturer_id) {
    
    $mail = new PHPMailer(true);

    try {
        // --- SERVER SETTINGS ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;                   
        $mail->Username   = 'syarahaqilah@graduate.utm.my'; 
        $mail->Password   = 'hwvm vwiv rmnv iuoj';   
        
        // Revert to Port 587 (Standard TLS)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // --- THE MAGIC FIX ---
        // This forces the connection to use the old IPv4 standard
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ),
            'socket' => array( 
                'bindto' => '0.0.0.0:0' 
            )
        );

        // --- RECIPIENTS ---
        $mail->setFrom('syarahaqilah@graduate.utm.my', 'School Admin'); 
        $mail->addAddress($email, $name);     

        // --- CONTENT ---
        $mail->isHTML(true);
        $mail->Subject = 'Your Account Credentials';
        
        $mail->Body    = "
            <h3>Welcome to the Faculty, $name!</h3>
            <p>An account has been created for you.</p>
            <ul>
                <li><strong>Username:</strong> $lecturer_id</li>
                <li><strong>Password:</strong> $raw_password</li>
            </ul>
        ";

        $mail->send();
        return true; 

    } catch (Exception $e) {
        echo "<b>MAILER ERROR:</b> " . $mail->ErrorInfo; 
        die(); 
    }
}

?>