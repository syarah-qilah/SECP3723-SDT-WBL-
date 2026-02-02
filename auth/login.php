<?php
session_start();

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['username'])) {
    switch ($_SESSION['user_role']) {
        case 'Student':
            header("Location: ../student/dashboard.php");
            break;
        case 'Lecturer':
            header("Location: ../lecturer/dashboard.php");
            break;
        case 'Admin':
            header("Location: ../admin/dashboard.php");
            break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="../assets/img/logo.svg" alt="SMS Logo" class="logo">
                <h1>Student Management System</h1>
                <p>Sign in to your account</p>
            </div>
            
            <?php
            // Display error message if exists
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">';
                echo '⚠️ ' . $_SESSION['error'];
                echo '</div>';
                unset($_SESSION['error']);
            }
            
            // Display success message if exists
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">';
                echo '✓ ' . $_SESSION['success'];
                echo '</div>';
                unset($_SESSION['success']);
            }
            ?>
            
            <form action="login-process.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username / Email</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Enter your username or email"
                        required 
                        autofocus
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password"
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Login
                </button>
            </form>
            
            <div class="login-footer">
                <p>&copy; 2026 UTM Student Management System</p>
                <p style="margin-top: 0.5rem; font-size: 0.8rem;">All Rights Reserved</p>
            </div>
        </div>
    </div>
</body>
</html>