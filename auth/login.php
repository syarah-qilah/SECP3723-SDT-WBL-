<?php
session_start();
// Adjust these paths if your file is in a subfolder. 
// Assuming login.php is in the root folder based on your upload.
require_once '../config/database.php';
require_once '../includes/security.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Sanitize Input
    // We use 'username' here because that matches your database column
    $username = sanitize_input($_POST['username'], $conn);
    $password = $_POST['password'];

    // 2. Query the Main USER Table
    // Your PDF confirms the column is 'username' and status is 'Active'
    $sql = "SELECT * FROM User WHERE username = '$username' AND status = 'Active'";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        // 3. Verify Password
        if (verify_password($password, $row['password_hash'])) {
            
            // Set Base Session Variables
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['name'];

            // 4. ROLE SPECIFIC LOOKUP
            if ($row['role'] == 'Student') {
                $stu_sql = "SELECT matricno FROM Student WHERE username = '$username'";
                $stu_res = mysqli_query($conn, $stu_sql);
                if($stu_row = mysqli_fetch_assoc($stu_res)) {
                    $_SESSION['key_id'] = $stu_row['matricno']; 
                    header("Location: ../student/dashboard.php");
                    exit();
                } else {
                    $error = "Student ID not found.";
                }

            } elseif ($row['role'] == 'Lecturer') {
                $lec_sql = "SELECT lectID FROM Lecturer WHERE username = '$username'";
                $lec_res = mysqli_query($conn, $lec_sql);
                if($lec_row = mysqli_fetch_assoc($lec_res)) {
                    $_SESSION['key_id'] = $lec_row['lectID']; 
                    header("Location: ../lecturer/dashboard.php");
                    exit();
                } else {
                    $error = "Lecturer ID not found.";
                }

            } elseif ($row['role'] == 'Admin') {
                $_SESSION['key_id'] = $row['username'];
                header("Location: ../admin/dashboard.php");
                exit();
            }
            
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found or account inactive.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EduManage</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>

    <div class="login-container">
        
        <div class="login-card">
            
            <div class="login-header">
                <div class="logo-circle">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h2>Welcome Back!</h2>
                <p>Sign in to continue your learning journey.</p>
            </div>

            <?php if($error): ?>
                <div style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="login-form">
                
                <div class="form-group">
                    <label>Username</label> <div class="input-icon-wrapper">
                        <i class="fas fa-user"></i> <input type="text" name="username" class="form-control" placeholder="e.g. A20EC0001" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary login-btn">
                    Login <i class="fas fa-arrow-right"></i>
                </button>
            </form>
            
            <div class="login-footer">
                <p>&copy; 2026 EduManage System</p>
            </div>
        </div>
    </div>

    <div class="demo-box" style="position: fixed; bottom: 20px; right: 20px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h4><i class="fas fa-key"></i> Test Accounts</h4>
        <div class="demo-item">
            <span class="badge badge-warning" style="background:orange; color:white; padding:2px 5px; border-radius:4px;">Student</span> 
            <span>A20EC0001 / (YourHash)</span>
        </div>
        <div class="demo-item">
            <span class="badge badge-info" style="background:blue; color:white; padding:2px 5px; border-radius:4px;">Lecturer</span> 
            <span>lect001 / (YourHash)</span>
        </div>
    </div>

</body>
</html>