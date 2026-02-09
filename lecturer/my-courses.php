<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Lecturer') {
    header("Location: ../auth/login.php");
    exit();
}

$lect_id = $_SESSION['key_id'];
$current_sem_num = (date('n') >= 2 && date('n') <= 8) ? 2 : 1;
$current_session = "2025/2026";
$current_semester_label = get_current_semester();

// 1. FETCH CURRENT ASSIGNED COURSES
// We join Course and course_lecturer to find what this specific lecturer is teaching now
$current_sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM Registration WHERE c_code = c.c_code AND regisStat = 'Approved') as enrolled_count
                FROM Course c
                JOIN course_lecturer cl ON c.c_code = cl.c_code
                WHERE cl.lectID = '$lect_id' 
                AND c.semester = $current_sem_num 
                AND c.academic_session = '$current_session'";
$current_res = mysqli_query($conn, $current_sql);

// 2. HANDLE TEACHING HISTORY FILTERING
$selected_filter = $_GET['session'] ?? 'all';
// Using LOWER() to prevent case-sensitivity issues
$history_where = "WHERE LOWER(cl.lectID) = LOWER('$lect_id') AND (c.semester != $current_sem_num OR c.academic_session != '$current_session')";

if ($selected_filter !== 'all') {
    $parts = explode('_', $selected_filter);
    if(count($parts) == 2) {
        $sess = mysqli_real_escape_string($conn, $parts[0]);
        $sem = (int)$parts[1];
        $history_where .= " AND c.academic_session = '$sess' AND c.semester = $sem";
    }
}

$history_sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM Registration WHERE c_code = c.c_code AND regisStat = 'Approved') as enrolled_count
                FROM Course c
                JOIN course_lecturer cl ON c.c_code = cl.c_code
                $history_where
                ORDER BY c.academic_session DESC";
$history_res = mysqli_query($conn, $history_sql);

// 3. FETCH UNIQUE PAST TEACHING SESSIONS FOR DROPDOWN

$sessions_sql = "SELECT DISTINCT c.academic_session, c.semester 
                 FROM Course c 
                 JOIN course_lecturer cl ON c.c_code = cl.c_code
                 WHERE LOWER(cl.lectID) = LOWER('$lect_id') 
                 AND (c.semester != $current_sem_num OR c.academic_session != '$current_session')
                 ORDER BY c.academic_session DESC, c.semester DESC";
$sessions_res = mysqli_query($conn, $sessions_sql);

?>

<?php 
include '../includes/header.php'; 
?>

<?php include 'sidebar.php'; ?>

<main class="main-content">
    
    <div class="topbar">
        <div class="topbar-left">
            <h1>Assigned Courses 📚</h1>
            <p class="text mb-0">Manage teaching loads and student rosters</p>
        </div>
    </div>

   <ul class="nav nav-tabs-modern mb-4" id="courseTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="current-tab" data-bs-toggle="pill" data-bs-target="#current" type="button" role="tab">
            <i class="fas fa-chalkboard-teacher me-2"></i> Current Semester
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="history-tab" data-bs-toggle="pill" data-bs-target="#history" type="button" role="tab">
            <i class="fas fa-archive me-2"></i> Teaching History
        </button>
    </li>
</ul>
    <div class="tab-content" id="courseTabsContent">
        
        <div class="tab-pane fade show active" id="current" role="tabpanel">
            
            <div class="content-card mb-4" style="background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(0, 0, 0, 0) 100%); border-left: 4px solid var(--primary);">
                <div class="semester-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-white mb-0">Semester 2, 2025/2026</h3>
            
                    </div>
            
                </div>
                
            </div>

            <div class="row">
    <?php if (mysqli_num_rows($current_res) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($current_res)): ?>
            <div class="col-12 mb-3">
                <div class="course-list-card d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="course-icon-box">
                            <i class="fas fa-chalkboard"></i>
                        </div>
                        <div>
                            <h4 class="text-white mb-0"><?php echo htmlspecialchars($row['c_name']); ?></h4>
                            <div class="d-flex align-items-center gap-3 mt-1">
                                <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;"><?php echo $row['c_code']; ?></span>
                                <span class="text small"><i class="fas fa-users me-1"></i> Section <?php echo $row['section']; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center d-none d-md-block">
                        <p class="text mb-0 small"><i class="fas fa-user-graduate text-info me-1"></i> <?php echo $row['enrolled_count']; ?> Enrolled</p>
                        <p class="text mb-0 small"><i class="far fa-clock text me-1"></i> <?php echo $row['day_time'] ?? 'TBA'; ?></p>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <span class="badge badge-success px-3 py-2">Active</span>
                        <a href="student-list.php?course_id=<?php echo $row['c_code']; ?>" class="btn btn-glass btn-sm">
                            <i class="fas fa-eye me-1"></i> View Student List
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <p class="text">No courses assigned to you for this semester.</p>
        </div>
    <?php endif; ?>
</div>
        </div>

        <div class="tab-pane fade" id="history" role="tabpanel">
    <div class="content-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h3 class="card-title mb-0"><i class="fas fa-history"></i> Teaching Record</h3>
            
            <form method="GET" id="historyFilter">
    <input type="hidden" name="tab" value="history"> <select name="session" class="glass-select" onchange="this.form.submit()">
        <option value="all">View All History</option>
        
        <?php 
        // Reset pointer to the start of the list
        if (mysqli_num_rows($sessions_res) > 0) {
            mysqli_data_seek($sessions_res, 0);
            while($s_row = mysqli_fetch_assoc($sessions_res)) {
                $val = $s_row['academic_session'] . "_" . $s_row['semester']; 
                $label = "Session " . $s_row['academic_session'] . " - Sem " . $s_row['semester'];
                $selected = ($selected_filter == $val) ? 'selected' : '';
                
                echo "<option value='$val' $selected>$label</option>";
            }
        } else {
            // DEBUG OPTION: If you see this, the SQL found 0 rows.
            echo "<option disabled>No History Found (ID: $lect_id)</option>";
        }
        ?>
    </select>
</form>
        </div>

        <div class="mt-4">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Course Name</th>
                        <th>Section</th>
                        <th>Total Students</th>
                        <th>Session</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($history_res) > 0): ?>
                        <?php while($h_row = mysqli_fetch_assoc($history_res)): ?>
                            <tr>
                                <td><strong><?php echo $h_row['c_code']; ?></strong></td>
                                <td><?php echo htmlspecialchars($h_row['c_name']); ?></td>
                                <td><?php echo $h_row['section']; ?></td>
                                <td><?php echo $h_row['enrolled_count']; ?></td>
                                <td>
                                    <span class="badge badge-secondary">
                                        Sem <?php echo $h_row['semester']; ?> (<?php echo $h_row['academic_session']; ?>)
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4">No historical records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    // If the URL has ?tab=history, force the history tab to open
    if (urlParams.get('tab') === 'history') {
        const historyTabBtn = document.querySelector('#history-tab');
        if (historyTabBtn) {
            const tabInstance = new bootstrap.Tab(historyTabBtn);
            tabInstance.show();
        }
    }
});
</script>


<?php include '../includes/footer.php'; ?>