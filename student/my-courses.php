<?php
session_start();
require_once '../config/database.php'; //
require_once '../includes/functions.php'; //

// 1. SECURITY CHECK: Ensure user is logged in as a Student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../auth/login.php");
    exit();
}

$matric_no = $_SESSION['key_id']; // Captured during login

// 2. GET CURRENT ACADEMIC INFO
// Uses the function from includes/functions.php to determine current semester label
$current_semester_label = get_current_semester(); 
$current_sem_num = (date('n') >= 2 && date('n') <= 8) ? 2 : 1;
$current_session = "2025/2026"; 

// 3. FETCH CURRENT SEMESTER COURSES
// Querying the Registration table joined with Course as defined in the database schema
$current_sql = "SELECT r.*, c.c_name, c.c_credit, c.section, c.day_time 
                FROM Registration r 
                JOIN Course c ON r.c_code = c.c_code 
                WHERE r.matricno = '$matric_no' 
                AND r.semester = $current_sem_num 
                AND r.academic_session = '$current_session'";
$current_res = mysqli_query($conn, $current_sql);

// 4. HANDLE HISTORY FILTERING
$selected_filter = $_GET['session'] ?? 'all'; 
$history_where = "WHERE r.matricno = '$matric_no' AND (r.semester != $current_sem_num OR r.academic_session != '$current_session')";

if ($selected_filter !== 'all') {
    $parts = explode('_', $selected_filter);
    if(count($parts) == 2) {
        $sess = mysqli_real_escape_string($conn, $parts[0]);
        $sem = (int)$parts[1];
        $history_where .= " AND r.academic_session = '$sess' AND r.semester = $sem";
    }
}

$history_sql = "SELECT r.*, c.c_name, c.c_credit, c.section 
                FROM Registration r 
                JOIN Course c ON r.c_code = c.c_code 
                $history_where
                ORDER BY r.regisDate DESC";
$history_res = mysqli_query($conn, $history_sql);

// 5. FETCH UNIQUE PAST SESSIONS FOR DROPDOWN
$sessions_sql = "SELECT DISTINCT academic_session, semester FROM Registration 
                 WHERE matricno = '$matric_no' 
                 AND (semester != $current_sem_num OR academic_session != '$current_session')
                 ORDER BY academic_session DESC, semester DESC";
$sessions_res = mysqli_query($conn, $sessions_sql);

// 6. CALCULATE TOTAL CREDITS (Current Approved Only)
$credit_sql = "SELECT SUM(c.c_credit) as total FROM Registration r JOIN Course c ON r.c_code = c.c_code 
               WHERE r.matricno = '$matric_no' AND r.regisStat = 'Approved' AND r.semester = $current_sem_num";
$credit_data = mysqli_fetch_assoc(mysqli_query($conn, $credit_sql));
$total_credits = $credit_data['total'] ?? 0;
?>

<?php include '../includes/header.php'; ?>
<?php include 'sidebar.php'; ?>

<main class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <h1>My Courses 📚</h1>
            <p class="text mb-0">Manage your academic journey</p>
        </div>
    </div>

    <ul class="nav nav-pills mb-4" id="courseTabs" role="tablist" style="gap: 10px;">
        <li class="nav-item">
            <button class="nav-link active btn-glass" id="current-tab" data-bs-toggle="pill" data-bs-target="#current" type="button" role="tab">
                <i class="fas fa-book-open"></i> Current Semester
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link btn-glass" id="history-tab" data-bs-toggle="pill" data-bs-target="#history" type="button" role="tab">
                <i class="fas fa-history"></i> Course History
            </button>
        </li>
    </ul>

    <div class="tab-content" id="courseTabsContent">
        <div class="tab-pane fade show active" id="current" role="tabpanel">
            <div class="content-card mb-4" style="border-left: 4px solid var(--primary);">
                <div class="semester-header d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="text-white mb-0"><?php echo $current_semester_label; ?></h3>
                        <p class="text-muted small mb-0">Total Credits: <strong><?php echo $total_credits; ?> / 18</strong></p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="print-slip.php" target="_blank" class="btn btn-outline-light btn-sm" style="border-radius: 50px; padding: 8px 20px;">
                            <i class="fas fa-print me-2"></i> Print Slip
                        </a>
                        <a href="register-course.php" class="btn btn-primary btn-sm" style="border-radius: 50px; padding: 8px 20px; background: var(--neon-gradient);">
                            <i class="fas fa-plus-circle me-1"></i> Add / Drop Course
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php if (mysqli_num_rows($current_res) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($current_res)): ?>
                        <div class="col-12 mb-3">
                            <div class="course-list-card">
                                <div class="d-flex align-items-center">
                                    <div class="course-icon-box"><i class="fas fa-laptop-code"></i></div>
                                    <div>
                                        <h4 class="text-white mb-0"><?php echo htmlspecialchars($row['c_name']); ?></h4>
                                        <div class="d-flex align-items-center gap-3 mt-1">
                                            <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;"><?php echo $row['c_code']; ?></span>
                                            <span class="text small">Section <?php echo $row['section']; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end d-none d-md-block">
                                    <p class="text mb-0 small"><i class="far fa-clock text-info me-1"></i> <?php echo $row['day_time'] ?? 'TBA'; ?></p>
                                    <p class="text mb-0 small"><i class="fas fa-calendar-alt text-danger me-1"></i> Registered: <?php echo date('d M Y', strtotime($row['regisDate'])); ?></p>
                                </div>
                                <div class="ms-4">
                                    <?php echo get_status_badge($row['regisStat']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No registrations found for this semester.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="content-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0"><i class="fas fa-history"></i> Academic Record</h3>
                    
                    <form method="GET" id="filterForm" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="history"> <select name="session" class="glass-select" onchange="this.form.submit()">
                            <option value="all">All Sessions</option>
                            <?php while($s_row = mysqli_fetch_assoc($sessions_res)): ?>
                                <?php 
                                    $val = $s_row['academic_session'] . "_" . $s_row['semester']; 
                                    $label = "Session " . $s_row['academic_session'] . " - Sem " . $s_row['semester'];
                                    $selected = ($selected_filter == $val) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $label; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </form>
                </div>

                <div class="mt-4">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Course Name</th>
                                <th>Credits</th>
                                <th>Section</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($history_res) > 0): ?>
                                <?php while($h_row = mysqli_fetch_assoc($history_res)): ?>
                                    <tr>
                                        <td><strong><?php echo $h_row['c_code']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($h_row['c_name']); ?></td>
                                        <td><?php echo $h_row['c_credit']; ?></td>
                                        <td><?php echo $h_row['section']; ?></td>
                                        <td>
                                            <span class="badge badge-success">Passed</span>
                                            <small class="d-block text-muted" style="font-size: 10px;">Sem <?php echo $h_row['semester']; ?></small>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4">No records found for this selection.</td></tr>
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
    if (urlParams.get('tab') === 'history') {
        const historyTab = new bootstrap.Tab(document.querySelector('#history-tab'));
        historyTab.show();
    }
});
</script>

<?php include '../includes/footer.php'; ?>