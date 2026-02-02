<?php
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a student
check_login();
check_role('Student');

// Get current user data
$user = get_current_user();
$username = $user['username'];

// Get student specific data
$sql_student = "SELECT * FROM Student WHERE username = '$username'";
$result_student = mysqli_query($conn, $sql_student);
$student = mysqli_fetch_assoc($result_student);

// Get current semester
$current_semester = get_current_semester();

// Get statistics
// 1. Total Registered Courses (Current Semester)
$sql_total_courses = "SELECT COUNT(*) as total FROM Registration 
                      WHERE matricno = '{$student['matricno']}' 
                      AND regisStat = 'Approved'";
$result_total = mysqli_query($conn, $sql_total_courses);
$row_total = mysqli_fetch_assoc($result_total);
$total_courses = $row_total['total'];

// 2. Pending Registrations
$sql_pending = "SELECT COUNT(*) as pending FROM Registration 
                WHERE matricno = '{$student['matricno']}' 
                AND regisStat = 'Pending'";
$result_pending = mysqli_query($conn, $sql_pending);
$row_pending = mysqli_fetch_assoc($result_pending);
$pending_count = $row_pending['pending'];

// 3. Get unread notifications
$unread_notif = get_unread_notification_count($conn, $username);

// 4. Get recent registrations
$sql_recent = "SELECT r.*, c.c_name, c.c_code, c.c_credit 
               FROM Registration r
               JOIN Course c ON r.c_code = c.c_code
               WHERE r.matricno = '{$student['matricno']}'
               ORDER BY r.regisDate DESC
               LIMIT 5";
$result_recent = mysqli_query($conn, $sql_recent);

// Get user initials for avatar
$name_parts = explode(' ', $user['name']);
$initials = strtoupper(substr($name_parts[0], 0, 1));
if (isset($name_parts[1])) {
    $initials .= strtoupper(substr($name_parts[1], 0, 1));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - SMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/img/logo.svg" alt="SMS Logo" class="logo">
                <h2>SMS</h2>
                <p>Student Portal</p>
            </div>
            
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item active">
                    <span class="menu-item-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="register-course.php" class="menu-item">
                    <span class="menu-item-icon">📝</span>
                    <span>Register Course</span>
                </a>
                <a href="my-courses.php" class="menu-item">
                    <span class="menu-item-icon">📚</span>
                    <span>My Courses</span>
                </a>
                <a href="profile.php" class="menu-item">
                    <span class="menu-item-icon">👤</span>
                    <span>My Profile</span>
                </a>
                <a href="../auth/logout.php" class="menu-item">
                    <span class="menu-item-icon">🚪</span>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <div class="topbar-left">
                    <h1>Welcome back, <?php echo htmlspecialchars($user['name']); ?>! 👋</h1>
                    <p><?php echo $current_semester; ?> • Status: <strong style="color: var(--success);">Active</strong></p>
                </div>
                
                <div class="topbar-right">
                    <div class="notification-bell">
                        <span>🔔</span>
                        <?php if ($unread_notif > 0): ?>
                            <span class="notification-badge"><?php echo $unread_notif; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="user-profile">
                        <div class="user-avatar"><?php echo $initials; ?></div>
                        <div class="user-info">
                            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                            <p><?php echo $student['matricno']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Statistics -->
            <div class="dashboard-grid">
                <!-- Total Courses Card -->
                <div class="stat-card success">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Registered Courses</div>
                        </div>
                        <div class="stat-icon">📚</div>
                    </div>
                    <div class="stat-value"><?php echo $total_courses; ?></div>
                    <div class="stat-description">Active courses this semester</div>
                </div>

                <!-- Pending Card -->
                <div class="stat-card warning">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Pending Approval</div>
                        </div>
                        <div class="stat-icon">⏳</div>
                    </div>
                    <div class="stat-value"><?php echo $pending_count; ?></div>
                    <div class="stat-description">Awaiting confirmation</div>
                </div>

                <!-- Current Year Card -->
                <div class="stat-card info">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Current Year</div>
                        </div>
                        <div class="stat-icon">🎓</div>
                    </div>
                    <div class="stat-value">Year <?php echo $student['year']; ?></div>
                    <div class="stat-description"><?php echo htmlspecialchars($student['program']); ?></div>
                </div>

                <!-- Notifications Card -->
                <div class="stat-card danger">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Notifications</div>
                        </div>
                        <div class="stat-icon">🔔</div>
                    </div>
                    <div class="stat-value"><?php echo $unread_notif; ?></div>
                    <div class="stat-description">Unread messages</div>
                </div>
            </div>

            <!-- Recent Registrations -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">Recent Registrations</h2>
                    <div class="card-actions">
                        <a href="register-course.php" class="btn btn-primary btn-sm">+ Register New Course</a>
                    </div>
                </div>

                <?php if (mysqli_num_rows($result_recent) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Credit Hours</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($reg = mysqli_fetch_assoc($result_recent)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($reg['c_code']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($reg['c_name']); ?></td>
                                    <td><?php echo $reg['c_credit']; ?> Credits</td>
                                    <td><?php echo format_datetime($reg['regisDate']); ?></td>
                                    <td><?php echo get_status_badge($reg['regisStat']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📝</div>
                        <h3>No Registrations Yet</h3>
                        <p>You haven't registered for any courses yet. Start by browsing available courses!</p>
                        <a href="register-course.php" class="btn btn-primary">Browse Courses</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>