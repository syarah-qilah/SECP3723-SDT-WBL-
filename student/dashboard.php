
<?php
session_start();
require_once '../config/database.php';

// 1. SECURITY CHECK: Is the user logged in AND a Student?
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    // If not, kick them back to login
    header("Location: ../auth/login.php");
    exit();
}

// 2. GET STUDENT INFO
$matric_no = $_SESSION['key_id']; // Retrieved from login
$student_name = $_SESSION['name'];

// 3. FETCH REGISTERED COURSES (For the "My Classes" table)
// We join Registration and Course tables to get the course names
// Update this line at the top
$sql = "SELECT r.c_code, c.c_name, c.section, c.c_credit, r.regisStat, r.regisDate 
        FROM Registration r 
        JOIN Course c ON r.c_code = c.c_code 
        WHERE r.matricno = '$matric_no'";

$result = mysqli_query($conn, $sql);
// ---  FETCH NOTIFICATIONS ---
// Get the last 5 notifications for this specific student
$notif_sql = "SELECT * FROM notification
              WHERE username = '$matric_no' 
              ORDER BY notifID DESC LIMIT 5";
$notif_result = mysqli_query($conn, $notif_sql);

// --- COUNT PENDING COURSES FOR STAT CARD ---
$pending_sql = "SELECT COUNT(*) as count FROM Registration WHERE matricno = '$matric_no' AND regisStat = 'Pending'";
$pending_res = mysqli_query($conn, $pending_sql);
$pending_data = mysqli_fetch_assoc($pending_res);
$pending_count = $pending_data['count'];
?>
<?php 
include '../includes/header.php'; 
?>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        
        <div class="topbar">
            <div class="topbar-left">
                <h1>Welcome, <?php echo $student_name; ?>! 👋</h1>
                <p class="text mb-0">Semester 2 2025/2026 • Status: <strong style="color: var(--success);">Active</strong></p>
            </div>
            
            <div class="topbar-right">
                <div class="notification-bell" style="position: relative; cursor: pointer;">
                    <i class="fas fa-bell fa-lg text"></i>
                    <span style="position: absolute; top: -5px; right: -5px; background: var(--danger); color: white; border-radius: 50%; width: 15px; height: 15px; font-size: 10px; display: flex; align-items: center; justify-content: center;">3</span>
                </div>
                
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="stat-card success">
                <div class="stat-header">
                    <div class="stat-title">Registered Courses</div>
                    <i class="fas fa-book-open fa-2x stat-icon"></i>
                </div>
                <div class="stat-value">6</div>
                <div class="stat-description">Active courses this semester</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <div class="stat-title">Pending Approval</div>
                    <i class="fas fa-clock fa-2x stat-icon"></i>
                </div>
                <div class="stat-value"><?php echo $pending_count; ?></div>
                <div class="stat-description">Awaiting confirmation</div>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <div class="stat-title">Current Year</div>
                    <i class="fas fa-user-graduate fa-2x stat-icon"></i>
                </div>
                <div class="stat-value">Year 3</div>
                <div class="stat-description">Software Engineering</div>
            </div>

            <div class="stat-card danger">
                <div class="stat-header">
                    <div class="stat-title">Notifications</div>
                    <i class="fas fa-bell fa-2x stat-icon"></i>
                </div>
                <div class="stat-value">3</div>
                <div class="stat-description">Unread messages</div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="content-card h-100">
                    <div class="card-header">
                        <h2 class="card-title">⚡ Quick Actions</h2>
                    </div>
                    <div class="d-grid gap-3">
                        <a href="register-course.php" class="btn btn-primary" style="justify-content: center;">
                            <i class="fas fa-edit"></i> Register New Course
                        </a>
                        <a href="my-courses.php" class="btn btn-secondary" style="justify-content: center; background: var(--secondary); border: none;">
                            <i class="fas fa-book"></i> View My Courses
                        </a>
                        <a href="profile.php" class="btn btn-tertiary" style="justify-content: center; background: #3f56a0;">
                            <i class="fas fa-user-cog"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>

        
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Recent Registrations</h2>
                <a href="my-courses.php" class="btn btn-primary btn-sm">View All</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Credits</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['c_code']; ?></td>
                
                <td><?php echo $row['c_name']; ?></td>
                
                <td><?php echo $row['c_credit']; ?></td> 

                <td><?php echo date('d M Y', strtotime($row['regisDate'])); ?></td>
                <td>
                    <?php 
                    $status = $row['regisStat'];
                    $badge_color = 'badge-secondary'; // Default
                    
                    if ($status == 'Approved') $badge_color = 'badge-success'; // Green
                    if ($status == 'Pending') $badge_color = 'badge-warning'; // Yellow
                    if ($status == 'Rejected') $badge_color = 'badge-danger'; // Red
                    ?>
                    <span class="badge <?php echo $badge_color; ?>">
                        <?php echo $status; ?>
                    </span>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" class="text-center">You have not registered for any courses yet.</td>
        </tr>
    <?php endif; ?>
</tbody>
            </table>
        </div>

    </main>
</div>


<?php include '../includes/footer.php'; ?>