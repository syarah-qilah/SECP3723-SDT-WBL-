<?php
// includes/functions.php

/**
 * Format date to Day Month Year (e.g. 07 Feb 2026)
 */
function format_date($date) {
    return date('d M Y', strtotime($date));
}

/**
 * Format datetime to Day Month Year, Time (e.g. 07 Feb 2026, 10:00 AM)
 */
function format_datetime($datetime) {
    return date('d M Y, h:i A', strtotime($datetime));
}

/**
 * Return an HTML badge based on status keyword
 */
function get_status_badge($status) {
    $class = '';
    switch(strtolower($status)) {
        case 'approved':
            $class = 'badge-success'; // Green
            break;
        case 'pending':
            $class = 'badge-warning'; // Yellow
            break;
        case 'rejected':
        case 'cancelled':
            $class = 'badge-danger'; // Red
            break;
        case 'active':
            $class = 'badge-primary'; // Blue
            break;
        default:
            $class = 'badge-secondary'; // Grey
    }
    return "<span class='badge $class'>$status</span>";
}

/**
 * Calculate the current academic semester based on the current month
 */
function get_current_semester() {
    $month = date('n'); // 1-12
    $year = date('Y');
    
    // Logic: 
    // Sept - Jan = Sem 1
    // Feb - June = Sem 2
    // July - Aug = Sem 3 (Short Sem)
    
    if ($month >= 9 || $month <= 1) {
        $sem = 1;
        // If Sept-Dec, year is Y/(Y+1). If Jan, year is (Y-1)/Y
        $academic_year = ($month >= 9) ? "$year/" . ($year + 1) : ($year - 1) . "/$year";
    } elseif ($month >= 2 && $month <= 6) {
        $sem = 2;
        $academic_year = ($year - 1) . "/$year";
    } else {
        $sem = 3;
        $academic_year = "$year/" . ($year + 1);
    }
    
    return "Semester $sem $academic_year"; // e.g., "Semester 2 2025/2026"
}

/**
 * Check how many seats are left in a course
 */
function get_available_seats($conn, $c_code) {
    // 1. Get Max Capacity
    $sql = "SELECT max_student FROM Course WHERE c_code = '$c_code'";
    $result = mysqli_query($conn, $sql);
    $course = mysqli_fetch_assoc($result);
    $max = $course['max_student'];
    
    // 2. Count Approved Registrations
    $sql = "SELECT COUNT(*) as enrolled FROM Registration 
            WHERE c_code = '$c_code' AND regisStat = 'Approved'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $enrolled = $row['enrolled'];
    
    return $max - $enrolled;
}

/**
 * Send a notification to a user
 */
function send_notification($conn, $username, $message, $type = 'info') {
    $message = mysqli_real_escape_string($conn, $message);
    $type = mysqli_real_escape_string($conn, $type);
    
    $sql = "INSERT INTO Notification (username, message, type, notifStat) 
            VALUES ('$username', '$message', '$type', 'Unread')";
    
    return mysqli_query($conn, $sql);
}

/**
 * Get count of unread notifications
 */
function get_unread_notification_count($conn, $username) {
    $sql = "SELECT COUNT(*) as count FROM Notification 
            WHERE username = '$username' AND notifStat = 'Unread'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}
?>