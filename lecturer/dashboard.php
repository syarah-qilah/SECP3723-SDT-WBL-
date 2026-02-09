<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// 1. SECURITY CHECK: Lecturer Only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Lecturer') {
    header("Location: ../auth/login.php");
    exit();
}

$lect_id = $_SESSION['key_id']; // L10234
$lecturer_name = $_SESSION['name'];
$current_semester_label = get_current_semester(); // e.g. "Semester 2 2025/2026"

// 2. STAT: Total Courses Taught (Active only)
$res_courses = mysqli_query($conn, "SELECT COUNT(*) as count FROM course_lecturer cl 
    JOIN Course c ON cl.c_code = c.c_code 
    WHERE cl.lectID = '$lect_id' AND c.semester = 2"); // Assuming Sem 2 is active
$count_courses = mysqli_fetch_assoc($res_courses)['count'];

// 3. STAT: Total Students (Active Enrolled)
$res_total_stu = mysqli_query($conn, "SELECT COUNT(r.regisID) as count 
    FROM Registration r 
    JOIN course_lecturer cl ON r.c_code = cl.c_code 
    WHERE cl.lectID = '$lect_id' AND r.regisStat = 'Approved'");
$count_total_students = mysqli_fetch_assoc($res_total_stu)['count'];

// 4. STAT: Pending Approvals (For Notification)
$res_pending = mysqli_query($conn, "SELECT COUNT(r.regisID) as count 
    FROM Registration r 
    JOIN course_lecturer cl ON r.c_code = cl.c_code 
    WHERE cl.lectID = '$lect_id' AND r.regisStat = 'Pending'");
$count_pending = mysqli_fetch_assoc($res_pending)['count'];

// 5. FETCH MY ASSIGNED CLASSES (Limit 5 for dashboard view)
$sql_classes = "SELECT c.c_code, c.c_name, c.section, c.max_student,
                (SELECT COUNT(*) FROM Registration WHERE c_code = c.c_code AND regisStat = 'Approved') as current_enrollment
                FROM Course c
                JOIN course_lecturer cl ON c.c_code = cl.c_code
                WHERE cl.lectID = '$lect_id'
                LIMIT 5";
$classes_result = mysqli_query($conn, $sql_classes);

$activePage = 'dashboard'; // For Sidebar Active State
include '../includes/header.php'; 
?>

<?php include 'sidebar.php'; ?>

<main class="main-content">
    
    <div class="topbar">
        <div class="topbar-left">
            <h1>Welcome back, <?php echo $lecturer_name; ?>! 🎓</h1>
            <p class="text mb-0"><?php echo $current_semester_label; ?> • Status: <strong style="color: #10b981;">Active</strong></p>
        </div>
        
        <div class="topbar-right">
            <div class="notification-bell" style="position: relative; cursor: pointer;">
                <i class="fas fa-bell fa-lg text-white"></i>
                <?php if($count_pending > 0): ?>
                    <span style="position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                        <?php echo $count_pending; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="dashboard-grid mb-4">
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.1)); border-left: 4px solid #3b82f6;">
            <div class="stat-value text-white"><?php echo $count_courses; ?></div>
            <div class="stat-description text">Active Courses</div>
            <i class="fas fa-book-open position-absolute" style="top: 20px; right: 20px; opacity: 0.2; font-size: 2rem; color: #3b82f6;"></i>
        </div>

        <div class="stat-card" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1)); border-left: 4px solid #10b981;">
            <div class="stat-value text-white"><?php echo $count_total_students; ?></div>
            <div class="stat-description text">Total Students</div>
            <i class="fas fa-users position-absolute" style="top: 20px; right: 20px; opacity: 0.2; font-size: 2rem; color: #10b981;"></i>
        </div>

    
    </div>

    <div class="row">
        
        <div class="col-md-6 mb-4">
            <div class="content-card h-100">
                <div class="card-header border-0 pb-2">
                    <h2 class="card-title mb-0"><i class="fas fa-bolt text-warning me-2"></i> Quick Actions</h2>
                </div>
                <div class="d-grid gap-3 p-2">
                    <a href="my-courses.php" class="btn btn-primary d-flex align-items-center justify-content-center py-3" style="background: #8b5cf6; border: none;">
                        <i class="fas fa-list-check me-2"></i> View Courses
                    </a>
                    
                    <a href="student-list.php" class="btn btn-secondary d-flex align-items-center justify-content-center py-3" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.1); color: white;">
                        <i class="fas fa-users me-2"></i> View Student List
                    </a>

                    <a href="profile.php" class="btn btn-tertiary d-flex align-items-center justify-content-center py-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.05); color: #ccc;">
                        <i class="fas fa-user-edit me-2"></i> Update Profile
                    </a>
                </div>
            </div>
        </div>

    

</main>

<?php include '../includes/footer.php'; ?>