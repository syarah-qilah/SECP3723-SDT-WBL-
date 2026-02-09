<?php 
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. CHECK IF ID IS PROVIDED
if (!isset($_GET['id'])) {
    header("Location: manage-student.php");
    exit();
}

$matric = mysqli_real_escape_string($conn, $_GET['id']);

// 3. FETCH STUDENT PROFILE
// We join Student + User tables to get full details
$stu_sql = "SELECT s.*, u.name, u.email, u.faculty 
            FROM Student s 
            JOIN User u ON s.username = u.username 
            WHERE s.matricno = '$matric'";
$stu_res = mysqli_query($conn, $stu_sql);
$student = mysqli_fetch_assoc($stu_res);

// If student doesn't exist, go back
if (!$student) {
    header("Location: manage-student.php");
    exit();
}

// 4. FETCH REGISTERED COURSES
$course_sql = "SELECT r.*, c.c_name, c.section, c.c_credit 
               FROM Registration r 
               JOIN Course c ON r.c_code = c.c_code 
               WHERE r.matricno = '$matric' 
               ORDER BY r.regisDate DESC";
$course_res = mysqli_query($conn, $course_sql);

$activePage = 'students';
include '../includes/header.php'; 
include 'sidebar.php'; 
?>

<main class="main-content">
    
    <div class="topbar">
        <div class="topbar-left">
            <a href="manage-student.php" class="btn btn-secondary text-white" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to Student List
            </a>
        </div>
        <div class="topbar-right">
            <h2 class="mb-0 text-end" style="font-size: 1.2rem; color: #ccc;">Student Profile</h2>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-4 mb-4">
            <div class="content-card h-100 text-center" style="background: linear-gradient(145deg, #1e1e24, #1a1a20);">
                
                <div style="margin-top: 20px; margin-bottom: 20px;">
                    <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: white; margin: 0 auto; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);">
                        <?php echo strtoupper(substr($student['name'], 0, 2)); ?>
                    </div>
                </div>
                
                <h3 class="text-white mb-1"><?php echo $student['name']; ?></h3>
                <p style="color: #818cf8; font-weight: bold; letter-spacing: 1px;"><?php echo $student['matricno']; ?></p>

                <hr style="border-color: rgba(255,255,255,0.1); margin: 25px 0;">

                <div class="text-start px-3">
                    <div class="mb-3">
                        <label class="small text d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Program</label>
                        <div class="text-white" style="font-size: 1.1rem;"><?php echo $student['program']; ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="small text d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Faculty</label>
                        <div class="text-white"><?php echo $student['faculty']; ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="small text d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Email Address</label>
                        <div class="text-white" style="word-break: break-all;"><?php echo $student['email']; ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small text d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Current Year</label>
                        <div class="text-white">Year <?php echo $student['year']; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="content-card h-100">
                <div class="card-header border-0 pb-3">
                    <h3 class="card-title mb-0"><i class="fas fa-book-open me-2" style="color: #8b5cf6;"></i>Registered Courses</h3>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Course Name</th>
                                <th>Section</th>
                                <th>Credits</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($course_res) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($course_res)): ?>
                                    <tr>
                                        <td><span class="badge" style="background: rgba(255,255,255,0.1); color: #fff;"><?php echo $row['c_code']; ?></span></td>
                                        <td class="text-white"><?php echo $row['c_name']; ?></td>
                                        <td><?php echo $row['section']; ?></td>
                                        <td><?php echo $row['c_credit']; ?></td>
                                        <td>
                                            <?php if($row['regisStat'] == 'Approved'): ?>
                                                <span class="badge badge-success">Approved</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning"><?php echo $row['regisStat']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div style="color: #555; font-size: 3rem; margin-bottom: 10px;"><i class="fas fa-folder-open"></i></div>
                                        <p class="text">No courses registered yet.</p>
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