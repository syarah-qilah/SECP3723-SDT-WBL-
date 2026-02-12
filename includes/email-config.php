<?php
function sendCredentialsEmail($name, $email, $raw_password, $lecturer_id) {
    
    // Get API key from environment variable (Railway) or hardcode for testing
    $api_key = getenv('RESEND_API_KEY') ?: 're_bfsPxkdi_MyTsTzqzuoRWPquYos95bqHa'; // Replace if testing locally
    
    // Prepare email data
    $data = [
        'from' => 'School Admin <syarahaqilah@graduate.utm.my>',
        'to' => [$email],
        'subject' => 'Your Account Credentials',
        'html' => "
            <h3>Welcome to the Faculty, $name!</h3>
            <p>An account has been created for you.</p>
            <ul>
                <li><strong>Username:</strong> $lecturer_id</li>
                <li><strong>Password:</strong> $raw_password</li>
            </ul>
            <p>Please login and change your password.</p>
        "
    ];
    
    // Initialize cURL
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, 'https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    // Execute request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    
    curl_close($ch);
    
    // Check response
    if ($http_code == 200) {
        return true; // Success
    } else {
        // Log error for debugging
        error_log("Resend Error: HTTP $http_code - Response: $response - cURL Error: $curl_error");
        return false;
    }
}
?>