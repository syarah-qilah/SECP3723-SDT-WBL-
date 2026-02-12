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

// 2. Define the Function
function sendCredentialsEmail($name, $email, $raw_password, $lecturer_id) {
    
    // Create a new instance
    $mail = new PHPMailer(true);

   try {
        // --- SERVER SETTINGS ---
        $mail->isSMTP();
        
        // FIX 1: Force IPv4 by converting the hostname to an IP address immediately
        $mail->Host       = gethostbyname('smtp.gmail.com'); 
        
        $mail->SMTPAuth   = true;                   
        $mail->Username   = 'syarahaqilah@graduate.utm.my'; 
        $mail->Password   = 'hwvm vwiv rmnv iuoj';   
        
        // FIX 2: Use TLS on Port 587 (Standard for "insecure" IP connections)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // FIX 3: Disable strict SSL checks (Required because we are using an IP, not a domain)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
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
            <p>Please login and change your password.</p>
        ";

        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // FIX: Display the error so we can fix it
        echo "<b>MAILER ERROR:</b> " . $mail->ErrorInfo; 
        // return false; // Keep this commented out until it works
        die(); 
    }
}
?>