<?php 
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

$activePage = 'dashboard'; 

// 2. FETCH DASHBOARD STATS
// Count Total Students
$res_stu = mysqli_query($conn, "SELECT COUNT(*) as total FROM Student");
$count_stu = mysqli_fetch_assoc($res_stu)['total'];

// Count Total Lecturers
$res_lec = mysqli_query($conn, "SELECT COUNT(*) as total FROM Lecturer");
$count_lec = mysqli_fetch_assoc($res_lec)['total'];

// Count Total Courses
$res_course = mysqli_query($conn, "SELECT COUNT(*) as total FROM Course");
$count_course = mysqli_fetch_assoc($res_course)['total'];

// Count Total Registrations
$res_reg = mysqli_query($conn, "SELECT COUNT(*) as total FROM Registration");
$count_reg = mysqli_fetch_assoc($res_reg)['total'];

// 3. FETCH RECENT ACTIVITY (Last 5 Registrations)
$sql_recent = "SELECT r.*, u.name as student_name, c.c_name 
               FROM Registration r 
               JOIN Student s ON r.matricno = s.matricno 
               JOIN User u ON s.username = u.username 
               JOIN Course c ON r.c_code = c.c_code 
               ORDER BY r.regisDate DESC LIMIT 5";
$res_recent = mysqli_query($conn, $sql_recent);

include '../includes/header.php'; 
include 'sidebar.php'; 
?>

<main class="main-content">
    
    <div class="topbar">
        <div class="topbar-left">
            <h1>Admin Dashboard 🚀</h1>
            <p class="text mb-0">Welcome back, <strong style="color: var(--secondary);"><?php echo $_SESSION['name']; ?></strong></p>
        </div>
    
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); padding: 20px; border-radius: 12px; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);">
                <div style="position: relative; z-index: 1;">
                    <h3 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px;"><?php echo $count_stu; ?></h3>
                    <p style="margin: 0; opacity: 0.9;">Total Students</p>
                </div>
                <i class="fas fa-user-graduate" style="position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.2;"></i>
                <a href="manage-student.php" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #34d399); padding: 20px; border-radius: 12px; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
                <div style="position: relative; z-index: 1;">
                    <h3 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px;"><?php echo $count_lec; ?></h3>
                    <p style="margin: 0; opacity: 0.9;">Total Lecturers</p>
                </div>
                <i class="fas fa-chalkboard-teacher" style="position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.2;"></i>
                <a href="manage-lect.php" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #fbbf24); padding: 20px; border-radius: 12px; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);">
                <div style="position: relative; z-index: 1;">
                    <h3 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px;"><?php echo $count_course; ?></h3>
                    <p style="margin: 0; opacity: 0.9;">Active Courses</p>
                </div>
                <i class="fas fa-book" style="position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.2;"></i>
                <a href="manage-courses.php" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #ef4444, #f87171); padding: 20px; border-radius: 12px; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);">
                <div style="position: relative; z-index: 1;">
                    <h3 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px;"><?php echo $count_reg; ?></h3>
                    <p style="margin: 0; opacity: 0.9;">Enrollments</p>
                </div>
                <i class="fas fa-clipboard-list" style="position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.2;"></i>
                <a href="manage-regis.php" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-8 mb-4">
            <div class="content-card h-100">
                <div class="card-header border-0 pb-0">
                    <h3 class="card-title mb-0">Recent Enrollments</h3>
                    <a href="manage-regis.php" class="btn btn-sm btn-tertiary">View All</a>
                </div>
                <div class="table-responsive mt-3">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($res_recent) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($res_recent)): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center" style="gap: 10px;">
                                                <div class="avatar-circle" style="width: 30px; height: 30px; font-size: 0.8rem; background: rgba(255,255,255,0.1);">
                                                    <?php echo strtoupper(substr($row['student_name'], 0, 2)); ?>
                                                </div>
                                                <span class="text-white"><?php echo $row['student_name']; ?></span>
                                            </div>
                                        </td>
                                        <td class="text-muted"><?php echo $row['c_name']; ?></td>
                                        <td>
                                            <?php if($row['regisStat'] == 'Approved'): ?>
                                                <span class="badge badge-success">Approved</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="color: #888; font-size: 0.85rem;">
                                            <?php echo date('d M, h:i A', strtotime($row['regisDate'])); ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No recent activity.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="content-card h-100">
                <div class="card-header border-0 pb-0">
                    <h3 class="card-title mb-0">Quick Actions</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
                    
                    <a href="manage-courses.php" class="btn btn-secondary text-start p-3 d-flex align-items-center" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <div style="background: #f59e0b; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-book-open text-white"></i>
                        </div>
                        <div>
                            <div class="text-white font-weight-bold">Add Course</div>
                            <div class="small text-muted">Create new curriculum</div>
                        </div>
                    </a>

                    <a href="manage-regis.php" class="btn btn-secondary text-start p-3 d-flex align-items-center" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <div style="background: #3b82f6; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-edit text-white"></i>
                        </div>
                        <div>
                            <div class="text-white font-weight-bold">Amend Registration</div>
                            <div class="small text-muted">Add or Drop Student Courses</div>
                        </div>
                    </a>

                </div>
            </div>
        </div>

    </div>

</main>

<?php include '../includes/footer.php'; ?>