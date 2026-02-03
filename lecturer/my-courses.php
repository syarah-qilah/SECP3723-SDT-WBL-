<?php
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_login();
check_role('Lecturer');

$user = get_current_user();
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
    <title>My Courses - SMS</title>
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
                <a href="dashboard.php" class="menu-item">
                    <span class="menu-item-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="register-course.php" class="menu-item">
                    <span class="menu-item-icon">📝</span>
                    <span>Register Course</span>
                </a>
                <a href="my-courses.php" class="menu-item active">
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
            <div class="topbar">
                <div class="topbar-left">
                    <h1>My Courses</h1>
                </div>
                <div class="topbar-right">
                    <div class="user-profile">
                        <div class="user-avatar"><?php echo $initials; ?></div>
                        <div class="user-info">
                            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card" style="text-align: center; padding: 3rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🚧</div>
                <h2 style="color: var(--primary); margin-bottom: 1rem;">Page Under Construction</h2>
                <p style="color: var(--gray-600); margin-bottom: 2rem;">
                    Course viewing functionality will be available soon.
                </p>
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </main>
    </div>
</body>
</html>