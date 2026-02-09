<?php 
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// 1. SECURITY: Lecturer Only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Lecturer') {
    header("Location: ../auth/login.php");
    exit();
}

$lect_id = $_SESSION['key_id'];
$activePage = 'student-list';

// 2. FETCH LECTURER'S COURSES (For the Dropdown)
$courses_sql = "SELECT c.c_code, c.c_name, c.section 
                FROM Course c 
                JOIN course_lecturer cl ON c.c_code = cl.c_code 
                WHERE LOWER(cl.lectID) = LOWER('$lect_id')";
$courses_res = mysqli_query($conn, $courses_sql);

// 3. DETERMINE SELECTED COURSE
$selected_course_code = "";
$selected_course_name = "Select a Course";
$selected_section = "";

if (isset($_GET['course_id'])) {
    $selected_course_code = $_GET['course_id'];
} elseif (mysqli_num_rows($courses_res) > 0) {
    $first_row = mysqli_fetch_assoc($courses_res);
    $selected_course_code = $first_row['c_code'];
    mysqli_data_seek($courses_res, 0); 
}

// 4. FETCH STUDENTS FOR SELECTED COURSE
$students_res = null;
$enrollment_count = 0;

if (!empty($selected_course_code)) {
    // Get Course Details
    $course_details_sql = "SELECT * FROM Course WHERE c_code = '$selected_course_code'";
    $cd_result = mysqli_query($conn, $course_details_sql);
    if($cd_row = mysqli_fetch_assoc($cd_result)){
        $selected_course_name = $cd_row['c_name'];
        $selected_section = $cd_row['section'];
    }

    // Get Student List
    $stu_sql = "SELECT r.regisID, r.matricno, r.regisStat, r.regisDate,
                       s.program, s.year, 
                       u.name, u.email 
                FROM Registration r
                JOIN Student s ON r.matricno = s.matricno
                JOIN User u ON s.username = u.username
                WHERE r.c_code = '$selected_course_code'
                ORDER BY u.name ASC"; 
    $students_res = mysqli_query($conn, $stu_sql);
    $enrollment_count = mysqli_num_rows($students_res);
}

include '../includes/header.php'; 
?>

<?php include 'sidebar.php'; ?>

<main class="main-content">
    
    <div class="topbar">
        <div class="topbar-left">
            <h1>Student List 📋</h1>
            <p class="text mb-0">View enrolled students</p>
        </div>
    </div>

    <div class="content-card mb-4">
        <label class="text small mb-2 d-block">Select Course to View Student List :</label>
        <form method="GET" action="">
            <select name="course_id" class="glass-select w-100" style="font-size: 1.1rem; padding: 15px 25px;" onchange="this.form.submit()">
                <?php if(mysqli_num_rows($courses_res) > 0): ?>
                    <?php while($c = mysqli_fetch_assoc($courses_res)): ?>
                        <?php $isSelected = ($c['c_code'] == $selected_course_code) ? 'selected' : ''; ?>
                        <option value="<?php echo $c['c_code']; ?>" <?php echo $isSelected; ?>>
                            <?php echo $c['c_code']; ?> - <?php echo $c['c_name']; ?> (Section <?php echo $c['section']; ?>)
                        </option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option>No courses assigned</option>
                <?php endif; ?>
            </select>
        </form>
    </div>

    <div class="content-card mb-0" style="border-bottom-left-radius: 0; border-bottom-right-radius: 0; border-left: 5px solid #f472b6;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-1"><?php echo $selected_course_name; ?></h2>
                <span class="badge" style="background: rgba(244, 114, 182, 0.1); color: #f472b6; border: 1px solid rgba(244, 114, 182, 0.2);">
                    <?php echo $selected_course_code; ?>
                </span>
                <span class="ms-3 text small"><?php echo $enrollment_count; ?> enrolled students</span>
            </div>
            <div class="course-icon-large">
                <i class="fas fa-laptop-code"></i>
            </div>
        </div>
    </div>

    <div class="content-card" style="border-top-left-radius: 0; border-top-right-radius: 0; padding: 0;">
        <div class="table-responsive">
            <table class="data-table roster-table">
                <thead>
                    <tr>
                        <th class="ps-4">Student</th>
                        <th>Student ID</th>
                        <th>Programme</th>
                        <th>Status</th>
                        <th class="text-center">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($enrollment_count > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($students_res)): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-small" style="background: #3b82f6;">
                                            <?php echo strtoupper(substr($row['name'], 0, 2)); ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white"><?php echo $row['name']; ?></div>
                                            <div class="text smaller">Enrolled: <?php echo date('d M Y', strtotime($row['regisDate'])); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="id-badge"><?php echo $row['matricno']; ?></span></td>
                                <td><?php echo $row['program']; ?></td>
                                <td class="text">
                                    <?php echo get_status_badge($row['regisStat']); ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-light btn-view-profile" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#studentDetailModal"
                                        data-name="<?php echo $row['name']; ?>"
                                        data-id="<?php echo $row['matricno']; ?>"
                                        data-email="<?php echo $row['email']; ?>"
                                        data-dept="<?php echo $row['program']; ?>"
                                        data-year="<?php echo $row['year']; ?>">
                                        <i class="fas fa-eye me-1"></i> View
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No students found for this course.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<div class="modal fade" id="studentDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content student-modal-glass" style="background: #111421; border: 1px solid rgba(255,255,255,0.1); border-radius: 24px;">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-4 w-100 p-3">
                    <div class="profile-avatar-glow" style="width: 70px; height: 70px; background: linear-gradient(135deg, #f472b6, #8b5cf6); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white; box-shadow: 0 0 20px rgba(244, 114, 182, 0.3);">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h2 class="text-white mb-0" id="m-name">Student Name</h2>
                        <span class="text-primary fw-bold" id="m-id-top" style="color: #f472b6 !important;">ID</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-item-card" style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                            <label class="text small d-block mb-1">Email Address</label>
                            <p class="text-white mb-0" id="m-email">email@graduate.utm.my</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-card" style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                            <label class="text small d-block mb-1">Student ID</label>
                            <p class="text-white mb-0" id="m-id-main">ID</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-card" style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                            <label class="text small d-block mb-1">Programme</label>
                            <p class="text-white mb-0" id="m-dept">Software Engineering</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-card" style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                            <label class="text small d-block mb-1">Year</label>
                            <p class="text-white mb-0" id="m-year">Year 1</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var studentModal = document.getElementById('studentDetailModal');
    studentModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var modal = this;
        modal.querySelector('#m-name').textContent = button.getAttribute('data-name');
        modal.querySelector('#m-id-top').textContent = button.getAttribute('data-id');
        modal.querySelector('#m-id-main').textContent = button.getAttribute('data-id');
        modal.querySelector('#m-email').textContent = button.getAttribute('data-email');
        modal.querySelector('#m-dept').textContent = button.getAttribute('data-dept');
        modal.querySelector('#m-year').textContent = "Year " + button.getAttribute('data-year');
    });
</script>

<?php include '../includes/footer.php'; ?>