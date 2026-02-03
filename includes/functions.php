<?php
function format_date($date) {
    return date('d M Y', strtotime($date));
}

function format_datetime($datetime) {
    return date('d M Y, h:i A', strtotime($datetime));
}

function get_status_badge($status) {
    $class = '';
    switch(strtolower($status)) {
        case 'approved':
            $class = 'badge-success';
            break;
        case 'pending':
            $class = 'badge-warning';
            break;
        case 'rejected':
        case 'cancelled':
            $class = 'badge-danger';
            break;
        case 'active':
            $class = 'badge-primary';
            break;
        default:
            $class = 'badge-secondary';
    }
    return "<span class='badge $class'>$status</span>";
}

function get_current_semester() {
    $month = date('n');
    $year = date('Y');
    
    if ($month >= 9 || $month <= 1) {
        $sem = 1;
        $academic_year = ($month >= 9) ? "$year/" . ($year + 1) : ($year - 1) . "/$year";
    } elseif ($month >= 2 && $month <= 6) {
        $sem = 2;
        $academic_year = ($year - 1) . "/$year";
    } else {
        $sem = 3;
        $academic_year = "$year/" . ($year + 1);
    }
    
    return "Semester $sem $academic_year";
}

function get_available_seats($conn, $c_code) {
    $sql = "SELECT max_student FROM Course WHERE c_code = '$c_code'";
    $result = mysqli_query($conn, $sql);
    $course = mysqli_fetch_assoc($result);
    $max = $course['max_student'];
    
    $sql = "SELECT COUNT(*) as enrolled FROM Registration 
            WHERE c_code = '$c_code' AND regisStat = 'Approved'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $enrolled = $row['enrolled'];
    
    return $max - $enrolled;
}

function send_notification($conn, $username, $message, $type = 'info') {
    $message = mysqli_real_escape_string($conn, $message);
    $type = mysqli_real_escape_string($conn, $type);
    
    $sql = "INSERT INTO Notification (username, message, type, notifStat) 
            VALUES ('$username', '$message', '$type', 'Unread')";
    
    return mysqli_query($conn, $sql);
}

function get_unread_notification_count($conn, $username) {
    $sql = "SELECT COUNT(*) as count FROM Notification 
            WHERE username = '$username' AND notifStat = 'Unread'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}
?>