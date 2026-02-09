<?php 
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage-lect.php");
    exit();
}

$lect_id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. FETCH LECTURER PROFILE
$l_sql = "SELECT l.*, u.name, u.email, u.faculty 
          FROM Lecturer l 
          JOIN User u ON l.username = u.username 
          WHERE l.lectID = '$lect_id'";
$l_res = mysqli_query($conn, $l_sql);
$lecturer = mysqli_fetch_assoc($l_res);

if (!$lecturer) {
    header("Location: manage-lect.php");
    exit();
}

// 2. FETCH ASSIGNED COURSES
$c_sql = "SELECT c.*, 
          (SELECT COUNT(*) FROM Registration WHERE c_code = c.c_code AND regisStat='Approved') as student_count
          FROM Course c 
          JOIN course_lecturer cl ON c.c_code = cl.c_code 
          WHERE cl.lectID = '$lect_id' 
          ORDER BY c.semester DESC";
$c_res = mysqli_query($conn, $c_sql);

$activePage = 'lecturers';
include '../includes/header.php'; 
include 'sidebar.php'; 
?>

<main class="main-content">
    
    <div class="topbar">
        <div class="topbar-left">
            <a href="manage-lect.php" class="btn btn-secondary text-white" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to Lecturers
            </a>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-4 mb-4">
            <div class="content-card h-100 text-center" style="background: linear-gradient(145deg, #1e1e24, #1a1a20);">
                <div style="margin-top: 20px; margin-bottom: 20px;">
                    <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #10b981, #34d399); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: white; margin: 0 auto; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
                        <?php echo strtoupper(substr($lecturer['name'], 0, 2)); ?>
                    </div>
                </div>
                
                <h3 class="text-white mb-1"><?php echo $lecturer['name']; ?></h3>
                <p style="color: #6ee7b7; font-weight: bold; letter-spacing: 1px;"><?php echo $lecturer['lectID']; ?></p>

                <hr style="border-color: rgba(255,255,255,0.1); margin: 25px 0;">

                <div class="text-start px-3">
                    <div class="mb-3">
                        <label class="small text-muted d-block text-uppercase">Department</label>
                        <div class="text-white"><?php echo $lecturer['department']; ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block text-uppercase">Office Location</label>
                        <div class="text-white"><?php echo $lecturer['office_room'] ?? 'Not Assigned'; ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block text-uppercase">Email</label>
                        <div class="text-white"><?php echo $lecturer['email']; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="content-card h-100">
                <div class="card-header border-0 pb-3">
                    <h3 class="card-title mb-0"><i class="fas fa-chalkboard-teacher me-2" style="color: #10b981;"></i>Assigned Courses</h3>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Course Name</th>
                                <th>Students</th>
                                <th>Session</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($c_res) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($c_res)): ?>
                                    <tr>
                                        <td><span class="badge" style="background: rgba(255,255,255,0.1); color: #fff;"><?php echo $row['c_code']; ?></span></td>
                                        <td class="text-white"><?php echo $row['c_name']; ?></td>
                                        <td>
                                            <i class="fas fa-user-graduate text-muted me-2"></i> <?php echo $row['student_count']; ?>
                                        </td>
                                        <td><?php echo $row['academic_session']; ?> (Sem <?php echo $row['semester']; ?>)</td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div style="color: #555; font-size: 3rem; margin-bottom: 10px;"><i class="fas fa-folder-open"></i></div>
                                        <p class="text-muted">No courses assigned yet.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

</main>

<?php include '../includes/footer.php'; ?>