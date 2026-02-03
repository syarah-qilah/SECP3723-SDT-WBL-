<?php
require_once '../config/database.php';
require_once '../includes/session.php';
check_login();
check_role('Lecturer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lecturer Page - SMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div style="padding: 2rem; text-align: center;">
        <h1>🚧 Lecturer Page - Under Construction</h1>
        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</body>
</html>