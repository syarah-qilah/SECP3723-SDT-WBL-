<?php 
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

$activePage = 'courses';


$sql = "SELECT c.*, u.name as lect_name, l.lectID,
        (SELECT COUNT(*) FROM Registration r WHERE r.c_code = c.c_code AND r.regisStat = 'Approved') as enrolled
        FROM Course c
        LEFT JOIN course_lecturer cl ON c.c_code = cl.c_code
        LEFT JOIN Lecturer l ON cl.lectID = l.lectID
        LEFT JOIN User u ON l.username = u.username 
        ORDER BY c.c_code ASC";
$courses_res = mysqli_query($conn, $sql);


$lect_sql = "SELECT l.lectID, u.name 
             FROM Lecturer l 
             JOIN User u ON l.username = u.username 
             ORDER BY u.name ASC";
$lect_res = mysqli_query($conn, $lect_sql);


$lecturers = [];
if ($lect_res && mysqli_num_rows($lect_res) > 0) {
    while($row = mysqli_fetch_assoc($lect_res)) {
        $lecturers[] = $row;
    }
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
            <h1>Course Management 📚</h1>
            <p class="text mb-0">Admin Portal • <strong style="color: var(--secondary);">System Administrator</strong></p>
        </div>
        
        <div class="topbar-right">
            <button class="btn btn-primary" onclick="openModal()">
                <i class="fas fa-plus-circle me-2"></i> Add New Course
            </button>
        </div>
    </div>

    <div class="content-card">
        
        <div class="card-header" style="flex-wrap: wrap; gap: 15px;">
            <h2 class="card-title">Active Curriculum</h2>
            
            <div style="display: flex; gap: 10px; flex-grow: 1; justify-content: flex-end;">
                <input type="text" id="searchInput" onkeyup="searchTable()" class="form-control" placeholder="Search by code or name..." style="max-width: 300px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table" id="courseTable">
                <thead>
                    <tr>
                        <th>Course Info</th>
                        <th>Credits</th>
                        <th>Enrollment Capacity</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($courses_res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($courses_res)): ?>
                            <?php 
                                // Calculate Progress
                                $max = $row['max_student'];
                                $current = $row['enrolled'];
                                $perc = ($max > 0) ? ($current / $max) * 100 : 0;
                                
                                // Determine Status/Color based on capacity
                                $status_badge = '<span class="badge badge-success">Active</span>';
                                $progress_class = 'seats-available';
                                
                                if ($current >= $max) {
                                    $progress_class = 'seats-danger'; // Full
                                    $status_badge = '<span class="badge badge-danger">Full</span>';
                                } elseif ($perc > 80) {
                                    $progress_class = 'seats-warning'; // Almost Full
                                }
                            ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="course-icon-box" style="width: 40px; height: 40px; font-size: 1rem; background: linear-gradient(135deg, #3b82f6, #8b5cf6);">
                                            <i class="fas fa-book"></i>
                                        </div>
                                        <div>
                                            <div style="color: white; font-weight: 600;"><?php echo $row['c_name']; ?></div>
                                            <div style="font-size: 0.8rem; color: var(--text);">
                                                <span class="text-primary"><?php echo $row['c_code']; ?></span> • 
                                                <?php echo $row['lect_name'] ?? 'Unassigned'; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge" style="background: rgba(255,255,255,0.1); color: white;"><?php echo $row['c_credit']; ?> Credits</span></td>
                                <td style="min-width: 150px;">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #ccc; margin-bottom: 5px;">
                                        <span><?php echo $current; ?>/<?php echo $max; ?> Students</span>
                                        <span><?php echo round($perc); ?>%</span>
                                    </div>
                                    <div class="seat-progress">
                                        <div class="progress-fill <?php echo $progress_class; ?>" style="width: <?php echo $perc; ?>%"></div>
                                    </div>
                                </td>
                                <td><?php echo $status_badge; ?></td>
                               <td style="text-align: right;">
                                <a href="course-details.php?code=<?php echo $row['c_code']; ?>" 
                                class="btn btn-sm btn-tertiary" 
                                style="color: #fff; text-decoration: none; padding: 5px 12px; font-size: 0.85rem; border-radius: 6px;">
                                    <i class="fas fa-eye me-1"></i> View Details
                                </a>
                                
                                <button class="btn" style="padding: 5px 10px; font-size: 0.8rem; background: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); margin-left: 5px;" 
                                    onclick="openDeleteModal('<?php echo $row['c_code']; ?>', '<?php echo addslashes($row['c_name']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No active courses found. Click "Add New Course" to start.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="addCourseModal" class="modal-overlay">
    <div class="modal-wrapper">
        <div class="modal-header">
            <h3>Add New Course</h3>
            <button class="close-modal-btn" onclick="closeModal()">&times;</button>
        </div>

        <form action="process-add-course.php" method="POST">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Course Code *</label>
                        <input type="text" name="course_code" class="form-control-dark" placeholder="e.g. SECJ1013" required>
                    </div>
                    <div class="form-group">
                        <label>Semester (1 or 2) *</label>
                        <input type="number" name="semester" class="form-control-dark" placeholder="e.g. 1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Course Name *</label>
                    <input type="text" name="course_name" class="form-control-dark" placeholder="e.g. Programming Technique I" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Credits</label>
                        <input type="number" name="credits" class="form-control-dark" value="3">
                    </div>
                    <div class="form-group">
                        <label>Max Students</label>
                        <input type="number" name="max_students" class="form-control-dark" value="40">
                    </div>
                </div>

                <div class="form-group">
                    <label>Assign Lecturer *</label>
                    <select name="lecturer" class="form-control-dark" required>
                        <option value="" disabled selected>Select Lecturer</option>
                        <?php foreach($lecturers as $lect): ?>
                            <option value="<?php echo $lect['lectID']; ?>">
                                <?php echo $lect['name']; ?> (<?php echo $lect['lectID']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Schedule / Day Time</label>
                    <input type="text" name="schedule" class="form-control-dark" placeholder="e.g. Mon 08:00 AM">
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-block">Add Course</button>
                <button type="button" class="btn btn-secondary btn-block" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="editCourseModal" class="modal-overlay">
    <div class="modal-wrapper">
        <div class="modal-header">
            <h3>Edit Course</h3>
            <button class="close-modal-btn" onclick="closeEditModal()">&times;</button>
        </div>

        <form action="process-edit-course.php" method="POST">
            <div class="modal-body">
                <input type="hidden" name="original_course_code" id="edit_original_code">

                <div class="form-row">
                    <div class="form-group">
                        <label>Course Code</label>
                        <input type="text" name="course_code" id="edit_course_code" class="form-control-dark" required>
                    </div>
                    <div class="form-group">
                        <label>Semester</label>
                        <input type="number" name="semester" id="edit_semester" class="form-control-dark" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Course Name</label>
                    <input type="text" name="course_name" id="edit_course_name" class="form-control-dark" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Credits</label>
                        <input type="number" name="credits" id="edit_credits" class="form-control-dark">
                    </div>
                    <div class="form-group">
                        <label>Max Students</label>
                        <input type="number" name="max_students" id="edit_max_students" class="form-control-dark">
                    </div>
                </div>

                <div class="form-group">
                    <label>Lecturer</label>
                    <select name="lecturer" id="edit_lecturer" class="form-control-dark">
                        <option value="">No Lecturer Assigned</option>
                        <?php foreach($lecturers as $lect): ?>
                            <option value="<?php echo $lect['lectID']; ?>">
                                <?php echo $lect['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Schedule</label>
                    <input type="text" name="schedule" id="edit_schedule" class="form-control-dark">
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
                <button type="button" class="btn btn-secondary btn-block" onclick="closeEditModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteCourseModal" class="modal-overlay">
    <div class="modal-wrapper" style="max-width: 400px; text-align: center;">
        <div class="modal-body" style="padding-top: 30px;">
            <div style="width: 60px; height: 60px; background: rgba(239, 68, 68, 0.2); border-radius: 50%; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 20px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 style="margin-bottom: 10px;">Delete Course?</h3>
            <p style="color: #ccc; font-size: 0.9rem;">
                Are you sure you want to delete <strong style="color: white;" id="delete_course_name_display">this course</strong>?
                <br>This action cannot be undone.
            </p>

            <form action="process-delete-course.php" method="POST" style="margin-top: 25px;">
                <input type="hidden" name="course_id" id="delete_course_id">
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary btn-block" style="background: #ef4444; border: none;">Delete</button>
                    <button type="button" class="btn btn-secondary btn-block" onclick="closeDeleteModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // --- ADD MODAL ---
    function openModal() {
        document.getElementById('addCourseModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('addCourseModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // --- EDIT MODAL ---
    function openEditModal(code, name, sem, credit, max, lectID, schedule) {
        // 1. Open the modal
        document.getElementById('editCourseModal').classList.add('active');
        
        // 2. Populate the fields with existing data
        document.getElementById('edit_original_code').value = code;
        document.getElementById('edit_course_code').value = code;
        document.getElementById('edit_course_name').value = name;
        document.getElementById('edit_semester').value = sem;
        document.getElementById('edit_credits').value = credit;
        document.getElementById('edit_max_students').value = max;
        document.getElementById('edit_schedule').value = schedule;
        
        // 3. Select the correct lecturer in dropdown
        // (If lectID is null or empty, it defaults to the "Select" option)
        document.getElementById('edit_lecturer').value = lectID;
    }

    function closeEditModal() {
        document.getElementById('editCourseModal').classList.remove('active');
    }

    // --- DELETE MODAL ---
    function openDeleteModal(id, name) {
        document.getElementById('deleteCourseModal').classList.add('active');
        document.getElementById('delete_course_id').value = id;
        document.getElementById('delete_course_name_display').textContent = name;
    }
    function closeDeleteModal() {
        document.getElementById('deleteCourseModal').classList.remove('active');
    }

    // --- SEARCH FUNCTION ---
    function searchTable() {
        let input = document.getElementById("searchInput");
        let filter = input.value.toUpperCase();
        let table = document.getElementById("courseTable");
        let tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) {
            let tdInfo = tr[i].getElementsByTagName("td")[0]; // Course Info Column
            if (tdInfo) {
                let txtValue = tdInfo.textContent || tdInfo.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }

    // Close modals if clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    }
</script>

<?php include '../includes/footer.php'; ?>