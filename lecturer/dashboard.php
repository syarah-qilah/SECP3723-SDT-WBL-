<?php
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a lecturer
check_login();
check_role('Lecturer');

// Get current user data
$user = get_current_user();
$username = $user['username'];

// Get lecturer specific data
$sql_lecturer = "SELECT * FROM Lecturer WHERE username = '$username'";
$result_lecturer = mysqli_query($conn, $sql_lecturer);
$lecturer = mysqli_fetch_assoc($result_lecturer);

// Get current semester
$current_semester = get_current_semester();

// Get statistics
// 1. Total Assigned Courses
$sql_courses = "SELECT COUNT(*) as total FROM Course_Lecturer 
                WHERE lectID = '{$lecturer['lectID']}'";
$result_courses = mysqli_query($conn, $sql_courses);
$row_courses = mysqli_fetch_assoc($result_courses);
$total_courses = $row_courses['total'];

// 2. Total Students (across all courses)
$sql_students = "SELECT COUNT(DISTINCT r.matricno) as total 
                 FROM Registration r
                 JOIN Course_Lecturer cl ON r.c_code = cl.c_code
                 WHERE cl.lectID = '{$lecturer['lectID']}'
                 AND r.regisStat = 'Approved'";
$result_students = mysqli_query($conn, $sql_students);
$row_students = mysqli_fetch_assoc($result_students);
$total_students = $row_students['total'];

// 3. Get unread notifications
$unread_notif = get_unread_notification_count($conn, $username);

// 4. Get assigned courses with student count
$sql_my_courses = "SELECT c.*, cl.lectID, 
                   (SELECT COUNT(*) FROM Registration r 
                    WHERE r.c_code = c.c_code AND r.regisStat = 'Approved') as enrolled_count
                   FROM Course c
                   JOIN Course_Lecturer cl ON c.c_code = cl.c_code
                   WHERE cl.lectID = '{$lecturer['lectID']}'
                   AND c.c_status = 'Active'
                   ORDER BY c.c_code";
$result_my_courses = mysqli_query($conn, $sql_my_courses);

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
    <title>Lecturer Dashboard - SMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/img/logo.svg" alt="SMS Logo" class="logo">
                <h2>SMS</h2>
                <p>Lecturer Portal</p>
            </div>
            
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item active">
                    <span class="menu-item-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="my-courses.php" class="menu-item">
                    <span class="menu-item-icon">📚</span>
                    <span>My Courses</span>
                </a>
                <a href="student-list.php" class="menu-item">
                    <span class="menu-item-icon">👥</span>
                    <span>Students</span>
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
                    <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>! 👨‍🏫</h1>
                    <p><?php echo $current_semester; ?> • Department: <strong><?php echo htmlspecialchars($lecturer['department']); ?></strong></p>
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
                            <p><?php echo $lecturer['lectID']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Statistics -->
            <div class="dashboard-grid">
                <!-- Total Courses Card -->
                <div class="stat-card info">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Assigned Courses</div>
                        </div>
                        <div class="stat-icon">📚</div>
                    </div>
                    <div class="stat-value"><?php echo $total_courses; ?></div>
                    <div class="stat-description">Courses teaching this semester</div>
                </div>

                <!-- Total Students Card -->
                <div class="stat-card success">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Total Students</div>
                        </div>
                        <div class="stat-icon">👥</div>
                    </div>
                    <div class="stat-value"><?php echo $total_students; ?></div>
                    <div class="stat-description">Students across all courses</div>
                </div>

                <!-- Department Card -->
                <div class="stat-card warning">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Department</div>
                        </div>
                        <div class="stat-icon">🏛️</div>
                    </div>
                    <div class="stat-value"><?php echo htmlspecialchars(substr($lecturer['department'], 0, 10)); ?></div>
                    <div class="stat-description"><?php echo htmlspecialchars($lecturer['department']); ?></div>
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

            <!-- My Courses -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">My Teaching Schedule</h2>
                    <div class="card-actions">
                        <a href="my-courses.php" class="btn btn-primary btn-sm">View All Courses</a>
                    </div>
                </div>

                <?php if (mysqli_num_rows($result_my_courses) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Credit Hours</th>
                                <th>Section</th>
                                <th>Enrolled Students</th>
                                <th>Capacity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($course = mysqli_fetch_assoc($result_my_courses)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($course['c_code']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($course['c_name']); ?></td>
                                    <td><?php echo $course['c_credit']; ?> Credits</td>
                                    <td><?php echo htmlspecialchars($course['section']); ?></td>
                                    <td>
                                        <strong style="color: var(--primary);"><?php echo $course['enrolled_count']; ?></strong> students
                                    </td>
                                    <td><?php echo $course['max_student']; ?> max</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="student-list.php?course=<?php echo $course['c_code']; ?>" class="btn-icon view" title="View Students">
                                                👥
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📚</div>
                        <h3>No Courses Assigned</h3>
                        <p>You don't have any courses assigned for this semester yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>