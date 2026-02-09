<?php 
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// 1. SECURITY CHECK: Admin Only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

$activePage = 'registrations'; 

// 2. FETCH REGISTRATIONS
$sql = "SELECT r.*, u.name as student_name, c.c_name, c.section 
        FROM Registration r 
        JOIN Student s ON r.matricno = s.matricno 
        JOIN User u ON s.username = u.username 
        JOIN Course c ON r.c_code = c.c_code 
        ORDER BY r.regisDate DESC";
$reg_res = mysqli_query($conn, $sql);

// 3. FETCH DATA FOR ADD MODAL
// Students
$stu_sql = "SELECT s.matricno, u.name FROM Student s JOIN User u ON s.username = u.username ORDER BY u.name ASC";
$stu_res = mysqli_query($conn, $stu_sql);
$students = [];
while($row = mysqli_fetch_assoc($stu_res)) {
    $students[] = $row;
}

// Courses
$course_sql = "SELECT c_code, c_name, section FROM Course ORDER BY c_code ASC";
$course_res = mysqli_query($conn, $course_sql);
$courses = [];
while($row = mysqli_fetch_assoc($course_res)) {
    $courses[] = $row;
}

include '../includes/header.php'; 
include 'sidebar.php'; 
?>

<main class="main-content">
    
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="topbar">
        <div class="topbar-left">
            <h1>Registration Management 📝</h1>
            <p class="text mb-0">Admin Portal • <strong style="color: var(--secondary);">System Administrator</strong></p>
        </div>
        
        <div class="topbar-right">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus-circle me-2"></i> Add Registration
            </button>
        </div>
    </div>

    <div class="content-card">
        
        <div class="card-header" style="flex-wrap: wrap; gap: 15px;">
            <h2 class="card-title">Student Enrollments</h2>
            
            <div style="display: flex; gap: 10px; flex-grow: 1; justify-content: flex-end;">
               <input type="text" id="searchInput" onkeyup="searchTable()" class="form-control" placeholder="Search Student or Course..." 
       style="max-width: 300px; color: #ffffff; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table" id="regTable">
                <thead>
                    <tr>
                        <th>Student Info</th>
                        <th>Course Requested</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($reg_res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($reg_res)): ?>
                            <?php 
                                $date = date('d M Y', strtotime($row['regisDate']));
                            ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="course-icon-box" style="width: 40px; height: 40px; font-size: 1rem; background: rgba(255, 255, 255, 0.1);">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <div>
                                            <div style="color: white; font-weight: 600;"><?php echo $row['student_name']; ?></div>
                                            <div style="font-size: 0.8rem; color: var(--text);">ID: <?php echo $row['matricno']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #e2e8f0;"><?php echo $row['c_name']; ?></div>
                                    <div style="font-size: 0.8rem; color: #94a3b8;"><?php echo $row['c_code']; ?> (Sec <?php echo $row['section']; ?>)</div>
                                </td>
                                <td><span style="color: #ccc; font-size: 0.9rem;"><?php echo $date; ?></span></td>
                                <td><span class="badge badge-success">Approved</span></td>
                                <td style="text-align: right;">
                                    <button class="btn" style="padding: 5px 15px; font-size: 0.8rem; background: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3);" 
                                        onclick="openCancelModal('<?php echo $row['regisID']; ?>', '<?php echo addslashes($row['student_name']); ?>', '<?php echo $row['c_code']; ?>')">
                                        <i class="fas fa-times me-1"></i> Drop
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text">No registrations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="addRegModal" class="modal-overlay">
    <div class="modal-wrapper">
        <div class="modal-header">
            <h3>Add New Registration</h3>
            <button class="close-modal-btn" onclick="closeAddModal()">&times;</button>
        </div>

        <form action="process-add-regis.php" method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label>Student *</label>
                    <select name="student_id" class="form-control-dark" required>
                        <option value="" disabled selected>Select Student...</option>
                        <?php foreach($students as $s): ?>
                            <option value="<?php echo $s['matricno']; ?>"><?php echo $s['name']; ?> (<?php echo $s['matricno']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Course *</label>
                    <select name="course_code" class="form-control-dark" required>
                        <option value="" disabled selected>Select Course...</option>
                        <?php foreach($courses as $c): ?>
                            <option value="<?php echo $c['c_code']; ?>"><?php echo $c['c_code']; ?> - <?php echo $c['c_name']; ?> (Sec <?php echo $c['section']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Semester</label>
                        <input type="text" name="semester" class="form-control-dark" value="2" readonly>
                    </div>
                    <div class="form-group">
                        <label>Academic Session</label>
                        <input type="text" name="session" class="form-control-dark" value="2025/2026" readonly>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-block">Register Student</button>
                <button type="button" class="btn btn-secondary btn-block" onclick="closeAddModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="cancelModal" class="modal-overlay">
    <div class="modal-wrapper" style="max-width: 400px; text-align: center;">
        <div class="modal-body" style="padding-top: 30px;">
            <div style="width: 60px; height: 60px; background: rgba(239, 68, 68, 0.2); border-radius: 50%; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 20px;">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 style="margin-bottom: 10px;">Drop Course?</h3>
            <p style="color: #ccc; font-size: 0.9rem; line-height: 1.6;">
                Are you sure you want to drop <strong style="color: white;" id="cancel_student_name">Student</strong> from 
                <strong style="color: #fca5a5;" id="cancel_course_name">Course</strong>?
            </p>
            <form action="process-delete-regis.php" method="POST" style="margin-top: 25px;">
                <input type="hidden" name="reg_id" id="cancel_reg_id">
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary btn-block" style="background: #ef4444; border: none;">Confirm Drop</button>
                    <button type="button" class="btn btn-secondary btn-block" onclick="closeCancelModal()">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ADD MODAL
    function openAddModal() {
        document.getElementById('addRegModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeAddModal() {
        document.getElementById('addRegModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // CANCEL MODAL
    function openCancelModal(id, student, course) {
        document.getElementById('cancelModal').classList.add('active');
        document.getElementById('cancel_reg_id').value = id;
        document.getElementById('cancel_student_name').textContent = student;
        document.getElementById('cancel_course_name').textContent = course;
    }
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.remove('active');
    }

    // SEARCH FILTER
    function searchTable() {
        let input = document.getElementById("searchInput");
        let filter = input.value.toUpperCase();
        let table = document.getElementById("regTable");
        let tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) {
            let tdName = tr[i].getElementsByTagName("td")[0];
            let tdCourse = tr[i].getElementsByTagName("td")[1];
            if (tdName || tdCourse) {
                let txtValueName = tdName.textContent || tdName.innerText;
                let txtValueCourse = tdCourse.textContent || tdCourse.innerText;
                if (txtValueName.toUpperCase().indexOf(filter) > -1 || txtValueCourse.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }

    // CLICK OUTSIDE TO CLOSE
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    }
</script>

<?php include '../includes/footer.php'; ?>