<?php
// Session Management for SMS
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function get_base_path() {
    return '/smsCopy'; // Change this if your folder name is different
}

function check_login() {
    if (!isset($_SESSION['username'])) {
        header("Location: " . get_base_path() . "/auth/login.php");
        exit();
    }
}

function check_role($required_role) {
    check_login();
    
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != $required_role) {
        $base = get_base_path();
        
        if (isset($_SESSION['user_role'])) {
            switch ($_SESSION['user_role']) {
                case 'Student':
                    header("Location: {$base}/student/dashboard.php");
                    exit();
                case 'Lecturer':
                    header("Location: {$base}/lecturer/dashboard.php");
                    exit();
                case 'Admin':
                    header("Location: {$base}/admin/dashboard.php");
                    exit();
            }
        }
        
        header("Location: {$base}/auth/login.php");
        exit();
    }
}

function get_current_user() {
    if (!isset($_SESSION['username'])) {
        return null;
    }
    
    $user = array(
        'username' => $_SESSION['username'],
        'name' => $_SESSION['user_name'],
        'role' => $_SESSION['user_role'],
        'email' => $_SESSION['user_email']
    );
    
    if (isset($_SESSION['user_status'])) {
        $user['status'] = $_SESSION['user_status'];
    }
    
    return $user;
}

function get_student_data() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'Student') {
        return null;
    }
    
    return array(
        'matricno' => isset($_SESSION['matricno']) ? $_SESSION['matricno'] : null,
        'year' => isset($_SESSION['year']) ? $_SESSION['year'] : null,
        'program' => isset($_SESSION['program']) ? $_SESSION['program'] : null
    );
}

function get_lecturer_data() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'Lecturer') {
        return null;
    }
    
    return array(
        'lectID' => isset($_SESSION['lectID']) ? $_SESSION['lectID'] : null,
        'department' => isset($_SESSION['department']) ? $_SESSION['department'] : null
    );
}

function get_admin_data() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'Admin') {
        return null;
    }
    
    return array(
        'adminID' => isset($_SESSION['adminID']) ? $_SESSION['adminID'] : null
    );
}

function logout_user() {
    session_unset();
    session_destroy();
    header("Location: " . get_base_path() . "/auth/login.php");
    exit();
}

function set_notification($message, $type = 'info') {
    $_SESSION['notification'] = array(
        'message' => $message,
        'type' => $type
    );
}

function get_notification() {
    if (isset($_SESSION['notification'])) {
        $notification = $_SESSION['notification'];
        unset($_SESSION['notification']);
        return $notification;
    }
    return null;
}
?>