<?php
// Copy this template for each placeholder page
// Just change the $page_title, $role, and $role_label

$page_title = "Page Title Here";
$role = "Student"; // or "Lecturer" or "Admin"
$role_label = "Student Portal"; // or "Lecturer Portal" or "Admin Portal"

require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_login();
check_role($role);

$user = get_current_user();
$name_parts = explode(' ', $user['name']);
$initials = strtoupper(substr($name_parts[0], 0, 1));
if (isset($name_parts[1])) {
    $initials .= strtoupper(substr($name_parts[1], 0, 1));
}

// Define menu items based on role
if ($role == 'Student') {
    $menu_items = [
        ['dashboard.php', '📊', 'Dashboard'],
        ['register-course.php', '📝', 'Register Course'],
        ['my-courses.php', '📚', 'My Courses'],
        ['profile.php', '👤', 'My Profile'],
    ];
} elseif ($role == 'Lecturer') {
    $menu_items = [
        ['dashboard.php', '📊', 'Dashboard'],
        ['my-courses.php', '📚', 'My Courses'],
        ['student-list.php', '👥', 'Students'],
        ['profile.php', '👤', 'My Profile'],
    ];
} else { // Admin
    $menu_items = [
        ['dashboard.php', '📊', 'Dashboard'],
        ['course-management.php', '📚', 'Course Management'],
        ['registration-management.php', '📝', 'Registrations'],
        ['student-management.php', '👨‍🎓', 'Students'],
        ['lecturer-management.php', '👨‍🏫', 'Lecturers'],
        ['profile.php', '👤', 'My Profile'],
    ];
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/img/logo.svg" alt="SMS Logo" class="logo">
                <h2>SMS</h2>
                <p><?php echo $role_label; ?></p>
            </div>
            
            <nav class="sidebar-menu">
                <?php foreach ($menu_items as $item): ?>
                    <a href="<?php echo $item[0]; ?>" class="menu-item <?php echo ($current_page == $item[0]) ? 'active' : ''; ?>">
                        <span class="menu-item-icon"><?php echo $item[1]; ?></span>
                        <span><?php echo $item[2]; ?></span>
                    </a>
                <?php endforeach; ?>
                <a href="../auth/logout.php" class="menu-item">
                    <span class="menu-item-icon">🚪</span>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <h1><?php echo $page_title; ?></h1>
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
                    This functionality will be available soon.
                </p>
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </main>
    </div>
</body>
</html>