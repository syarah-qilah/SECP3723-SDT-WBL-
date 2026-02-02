<?php
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an admin
check_login();
check_role('Admin');

// Get current user data
$user = get_current_user();
$username = $user['username'];

// Get admin specific data (with error handling)
$sql_admin = "SELECT * FROM Admin WHERE username = '$username'";
$result_admin = mysqli_query($conn, $sql_admin);
$admin = mysqli_fetch_assoc($result_admin);

if (!$admin) {
    // If admin record doesn't exist, create a default array
    $admin = ['adminID' => 'ADMIN'];
}

// Get current semester
$current_semester = get_current_semester();

// Get statistics with error handling
// 1. Total Users
$sql_users = "SELECT COUNT(*) as total FROM User WHERE status = 'Active'";
$result_users = mysqli_query($conn, $sql_users);
$row_users = mysqli_fetch_assoc($result_users);
$total_users = $row_users['total'] ?? 0;

// 2. Total Students
$sql_students = "SELECT COUNT(*) as total FROM Student";
$result_students = mysqli_query($conn, $sql_students);
$row_students = mysqli_fetch_assoc($result_students);
$total_students = $row_students['total'] ?? 0;

// 3. Total Lecturers
$sql_lecturers = "SELECT COUNT(*) as total FROM Lecturer";
$result_lecturers = mysqli_query($conn, $sql_lecturers);
$row_lecturers = mysqli_fetch_assoc($result_lecturers);
$total_lecturers = $row_lecturers['total'] ?? 0;

// 4. Total Courses
$sql_courses = "SELECT COUNT(*) as total FROM Course WHERE c_status = 'Active'";
$result_courses = mysqli_query($conn, $sql_courses);
$row_courses = mysqli_fetch_assoc($result_courses);
$total_courses = $row_courses['total'] ?? 0;

// 5. Pending Registrations
$sql_pending = "SELECT COUNT(*) as total FROM Registration WHERE regisStat = 'Pending'";
$result_pending = mysqli_query($conn, $sql_pending);
$row_pending = mysqli_fetch_assoc($result_pending);
$pending_registrations = $row_pending['total'] ?? 0;

// 6. Total Registrations
$sql_total_reg = "SELECT COUNT(*) as total FROM Registration";
$result_total_reg = mysqli_query($conn, $sql_total_reg);
$row_total_reg = mysqli_fetch_assoc($result_total_reg);
$total_registrations = $row_total_reg['total'] ?? 0;

// Get unread notifications
$unread_notif = get_unread_notification_count($conn, $username);

// Recent registrations for admin review
$sql_recent_reg = "SELECT r.*, s.matricno, u.name as student_name, c.c_code, c.c_name 
                   FROM Registration r
                   JOIN Student s ON r.matricno = s.matricno
                   JOIN User u ON s.username = u.username
                   JOIN Course c ON r.c_code = c.c_code
                   WHERE r.regisStat = 'Pending'
                   ORDER BY r.regisDate DESC
                   LIMIT 5";
$result_recent_reg = mysqli_query($conn, $sql_recent_reg);

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
    <title>Admin Dashboard - SMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/img/logo.svg" alt="SMS Logo" class="logo">
                <h2>SMS</h2>
                <p>Admin Portal</p>
            </div>
            
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item active">
                    <span class="menu-item-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="course-management.php" class="menu-item">
                    <span class="menu-item-icon">📚</span>
                    <span>Course Management</span>
                </a>
                <a href="registration-management.php" class="menu-item">
                    <span class="menu-item-icon">📝</span>
                    <span>Registrations</span>
                </a>
                <a href="student-management.php" class="menu-item">
                    <span class="menu-item-icon">👨‍🎓</span>
                    <span>Students</span>
                </a>
                <a href="lecturer-management.php" class="menu-item">
                    <span class="menu-item-icon">👨‍🏫</span>
                    <span>Lecturers</span>
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
                    <h1>Admin Dashboard 🛡️</h1>
                    <p><?php echo $current_semester; ?> • System Administrator</p>
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
                            <p>Administrator</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Statistics - Row 1 -->
            <div class="dashboard-grid">
                <!-- Total Users -->
                <div class="stat-card info">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Total Users</div>
                        </div>
                        <div class="stat-icon">👥</div>
                    </div>
                    <div class="stat-value"><?php echo $total_users; ?></div>
                    <div class="stat-description">Active system users</div>
                </div>

                <!-- Total Students -->
                <div class="stat-card success">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Students</div>
                        </div>
                        <div class="stat-icon">👨‍🎓</div>
                    </div>
                    <div class="stat-value"><?php echo $total_students; ?></div>
                    <div class="stat-description">Registered students</div>
                </div>

                <!-- Total Lecturers -->
                <div class="stat-card warning">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Lecturers</div>
                        </div>
                        <div class="stat-icon">👨‍🏫</div>
                    </div>
                    <div class="stat-value"><?php echo $total_lecturers; ?></div>
                    <div class="stat-description">Teaching staff</div>
                </div>

                <!-- Total Courses -->
                <div class="stat-card info">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Active Courses</div>
                        </div>
                        <div class="stat-icon">📚</div>
                    </div>
                    <div class="stat-value"><?php echo $total_courses; ?></div>
                    <div class="stat-description">Available courses</div>
                </div>
            </div>

            <!-- Dashboard Statistics - Row 2 -->
            <div class="dashboard-grid">
                <!-- Pending Registrations -->
                <div class="stat-card danger">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Pending Approvals</div>
                        </div>
                        <div class="stat-icon">⏳</div>
                    </div>
                    <div class="stat-value"><?php echo $pending_registrations; ?></div>
                    <div class="stat-description">Require attention</div>
                </div>

                <!-- Total Registrations -->
                <div class="stat-card success">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Total Registrations</div>
                        </div>
                        <div class="stat-icon">📝</div>
                    </div>
                    <div class="stat-value"><?php echo $total_registrations; ?></div>
                    <div class="stat-description">All course registrations</div>
                </div>

                <!-- System Status -->
                <div class="stat-card success">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">System Status</div>
                        </div>
                        <div class="stat-icon">✅</div>
                    </div>
                    <div class="stat-value">Online</div>
                    <div class="stat-description">All systems operational</div>
                </div>

                <!-- Notifications -->
                <div class="stat-card warning">
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

            <!-- Quick Actions -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">Quick Actions</h2>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <a href="course-management.php?action=add" class="btn btn-primary" style="text-align: center;">
                        📚 Add New Course
                    </a>
                    <a href="student-management.php?action=add" class="btn btn-success" style="text-align: center;">
                        👨‍🎓 Add New Student
                    </a>
                    <a href="lecturer-management.php?action=add" class="btn btn-warning" style="text-align: center;">
                        👨‍🏫 Add New Lecturer
                    </a>
                    <a href="registration-management.php" class="btn btn-danger" style="text-align: center;">
                        ⏳ Review Pending (<?php echo $pending_registrations; ?>)
                    </a>
                </div>
            </div>

            <!-- Pending Registrations Requiring Attention -->
            <?php if ($pending_registrations > 0 && $result_recent_reg && mysqli_num_rows($result_recent_reg) > 0): ?>
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">Pending Registrations - Require Review</h2>
                    <div class="card-actions">
                        <a href="registration-management.php" class="btn btn-primary btn-sm">View All Pending</a>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Matric No</th>
                            <th>Course</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($reg = mysqli_fetch_assoc($result_recent_reg)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reg['student_name']); ?></td>
                                <td><strong><?php echo htmlspecialchars($reg['matricno']); ?></strong></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($reg['c_code']); ?></strong><br>
                                    <small style="color: var(--gray-600);"><?php echo htmlspecialchars($reg['c_name']); ?></small>
                                </td>
                                <td><?php echo format_datetime($reg['regisDate']); ?></td>
                                <td><?php echo get_status_badge($reg['regisStat']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon view" title="Approve">✓</button>
                                        <button class="btn-icon delete" title="Reject">✗</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="content-card">
                <div class="empty-state">
                    <div class="empty-state-icon">✅</div>
                    <h3>No Pending Registrations</h3>
                    <p>All course registrations have been processed.</p>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>