<?php 
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if(!isset($_GET['code'])) { header("Location: manage-course.php"); exit(); }
$code = mysqli_real_escape_string($conn, $_GET['code']);

// Fetch Course and Lecturer Data
$sql = "SELECT c.*, l.lectID FROM Course c 
        LEFT JOIN course_lecturer cl ON c.c_code = cl.c_code 
        LEFT JOIN Lecturer l ON cl.lectID = l.lectID 
        WHERE c.c_code = '$code'";
$res = mysqli_query($conn, $sql);
$course = mysqli_fetch_assoc($res);

if (!$course) { header("Location: manage-course.php"); exit(); }

// Fetch Lecturers for dropdown
$lecturers = mysqli_query($conn, "SELECT l.lectID, u.name FROM Lecturer l JOIN User u ON l.username = u.username ORDER BY u.name ASC");

include '../includes/header.php'; 
include 'sidebar.php'; 
?>

<main class="main-content">
    
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981;">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="topbar mb-4">
        <div class="topbar-left">
            <div class="mb-2">
                <a href="manage-courses.php" class="text" style="text-decoration: none; font-size: 0.85rem; transition: 0.3s;" onmouseover="this.style.color='#8b5cf6'" onmouseout="this.style.color='#6c757d'">
                    <i class="fas fa-chevron-left me-1"></i> Back to Curriculum List
                </a>
            </div>
            <h1 class="text-white">Course Profile: <span style="color: #8b5cf6;"><?php echo $course['c_code']; ?></span></h1>
        </div>
    </div>

    <div class="content-card">
        <form id="courseForm" action="process-edit-course.php" method="POST">
            <input type="hidden" name="original_course_code" value="<?php echo $course['c_code']; ?>">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-white mb-0">Subject Details</h3>
                
                <button type="button" id="btn-edit-course" class="btn btn-primary" style="background: linear-gradient(135deg, #8b5cf6, #6366f1); border:none; border-radius: 50px;">
                    <i class="fas fa-pen me-2"></i> Edit Details
                </button>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-group-dark">
                        <label class="text small">Course Name</label>
                        <input type="text" name="course_name" class="form-control-dark editable-field" 
                               value="<?php echo htmlspecialchars($course['c_name']); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group-dark">
                        <label class="text small">Course Code</label>
                        <input type="text" name="course_code" class="form-control-dark editable-field" 
                               value="<?php echo $course['c_code']; ?>" readonly>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="form-group-dark">
                        <label class="text small">Semester</label>
                        <input type="number" name="semester" class="form-control-dark editable-field" 
                               value="<?php echo $course['semester']; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group-dark">
                        <label class="text small">Credits</label>
                        <input type="number" name="credits" class="form-control-dark editable-field" 
                               value="<?php echo $course['c_credit']; ?>" readonly>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <div class="form-group-dark">
                        <label class="text small">Assigned Lecturer</label>
                        <select name="lecturer" class="form-control-dark editable-field" disabled>
                            <option value="">-- No Lecturer Assigned --</option>
                            <?php while($l = mysqli_fetch_assoc($lecturers)): ?>
                                <option value="<?php echo $l['lectID']; ?>" <?php echo ($l['lectID'] == $course['lectID']) ? 'selected' : ''; ?>>
                                    <?php echo $l['name']; ?> (<?php echo $l['lectID']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-group-dark">
                        <label class="text small">Max Enrollment</label>
                        <input type="number" name="max_students" class="form-control-dark editable-field" 
                               value="<?php echo $course['max_student']; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group-dark">
                        <label class="text small">Schedule</label>
                        <input type="text" name="schedule" class="form-control-dark editable-field" 
                               value="<?php echo htmlspecialchars($course['day_time'] ?? ''); ?>" readonly>
                    </div>
                </div>
            </div>

            <div id="editActions" class="mt-4 pt-3 d-none justify-content-end" style="border-top: 1px dashed rgba(255,255,255,0.1); gap: 15px;">
                <button type="button" id="btn-cancel" class="btn btn-outline-secondary text-white" style="border-radius: 50px;">Cancel</button>
                <button type="submit" class="btn btn-success" style="border-radius: 50px; padding: 10px 30px; background: #10b981; border: none;">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('btn-edit-course');
    const cancelBtn = document.getElementById('btn-cancel');
    const actionsToolbar = document.getElementById('editActions');
    const inputs = document.querySelectorAll('.editable-field');

    // UNLOCK FIELDS
    editBtn.addEventListener('click', function() {
        actionsToolbar.classList.remove('d-none');
        actionsToolbar.classList.add('d-flex');
        editBtn.style.display = 'none';
        
        inputs.forEach(input => {
            input.removeAttribute('readonly');
            input.removeAttribute('disabled'); 
            input.style.border = '1px solid #8b5cf6';
            input.style.background = 'rgba(139, 92, 246, 0.05)';
        });
    });

    
    cancelBtn.addEventListener('click', function() {
        location.reload(); 
    });
});
</script>

<?php include '../includes/footer.php'; ?>