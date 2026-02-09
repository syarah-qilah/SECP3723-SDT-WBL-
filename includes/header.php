<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="/smsCopy/assets/css/main.css">

    <?php
        // Get the current URL path
        $current_url = $_SERVER['REQUEST_URI'];

        // Check: Is the user inside the 'student' folder?
        if (strpos($current_url, '/student/') !== false) {
            echo '<link rel="stylesheet" href="/smsCopy/assets/css/student.css">';
        } 
        // Check: Is the user inside the 'lecturer' folder?
        elseif (strpos($current_url, '/lecturer/') !== false) {
            echo '<link rel="stylesheet" href="/smsCopy/assets/css/lecturer.css">';
        }
        // Check: Is the user inside the 'admin' folder?
        elseif (strpos($current_url, '/admin/') !== false) {
            echo '<link rel="stylesheet" href="/smsCopy/assets/css/admin.css">';
        }
    ?>

</head>
<body>
    <div class="wrapper">