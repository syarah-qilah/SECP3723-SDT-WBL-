<?php
session_start();
require_once '../config/database.php';

// 1. SECURITY & DATA SETUP
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../auth/login.php");
    exit();
}
$matric_no = $_SESSION['key_id'];

// 2. HANDLE ACTIONS (Register or Drop)
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $c_code = $_POST['c_code'];
    
    // REGISTER LOGIC
    if (isset($_POST['register'])) {
        // Check if already registered
        $check = mysqli_query($conn, "SELECT * FROM Registration WHERE matricno='$matric_no' AND c_code='$c_code'");
        if (mysqli_num_rows($check) == 0) {
            // Check Seats
            $course_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT max_student FROM Course WHERE c_code='$c_code'"));
            $taken_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM Registration WHERE c_code='$c_code' AND regisStat='Approved'"));
            
            $max = $course_q['max_student'];
            $current = $taken_q['total'];
            
            // Logic: Auto-Approve if seats exist
            $status = ($current < $max) ? 'Approved' : 'Pending';
            
            $sql = "INSERT INTO Registration (matricno, c_code, regisStat) VALUES ('$matric_no', '$c_code', '$status')";
            if (mysqli_query($conn, $sql)) {
                $msg_type = "success";
                $msg = "Successfully registered for $c_code! Status: $status";
            }
        }
    }
    
    // DROP LOGIC
    if (isset($_POST['drop'])) {
        $sql = "DELETE FROM Registration WHERE matricno='$matric_no' AND c_code='$c_code'";
        if (mysqli_query($conn, $sql)) {
            $msg_type = "warning";
            $msg = "Dropped course $c_code.";
        }
    }
}

// 3. FETCH STUDENT STATS (For the top cards)
// Total Approved
$stat_app = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM Registration WHERE matricno='$matric_no' AND regisStat='Approved'"));
// Total Pending
$stat_pen = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM Registration WHERE matricno='$matric_no' AND regisStat='Pending'"));
// Total Credits
$stat_cred = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(c.c_credit) as total FROM Registration r JOIN Course c ON r.c_code = c.c_code WHERE r.matricno='$matric_no' AND r.regisStat='Approved'"));
$total_credits = $stat_cred['total'] ? $stat_cred['total'] : 0;

// 4. FETCH ALL COURSES (With Search Support)
$search_sql = "";
if(isset($_GET['search']) && !empty($_GET['search'])){
    $search = $_GET['search'];
    $search_sql = " WHERE c_name LIKE '%$search%' OR c_code LIKE '%$search%'";
}
$result = mysqli_query($conn, "SELECT * FROM Course" . $search_sql);

include '../includes/header.php';
include 'sidebar.php';
?>

<main class="main-content">
    
    <div class="topbar">
        <div class="topbar-left">
            <h1>Register Courses 📝</h1>
            <p class="text mb-0">Select your subjects for Semester 2</p>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="stat-title">Approved Courses</p>
                    <h3 class="stat-value" style="font-size: 1.8rem;"><?php echo $stat_app['cnt']; ?></h3>
                </div>
                <i class="fas fa-check-circle fa-2x text-success"></i>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="stat-title">Pending Approval</p>
                    <h3 class="stat-value" style="font-size: 1.8rem;"><?php echo $stat_pen['cnt']; ?></h3>
                </div>
                <i class="fas fa-clock fa-2x text-warning"></i>
            </div>
        </div>

        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="stat-title">Total Credits</p>
                    <h3 class="stat-value" style="font-size: 1.8rem;"><?php echo $total_credits; ?> / 18</h3>
                </div>
                <i class="fas fa-layer-group fa-2x text-info"></i>
            </div>
        </div>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?php echo $msg_type; ?> mb-4 shadow-sm" role="alert" style="border-radius: 15px;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form method="GET" action="">
    <div class="d-flex align-items-center mb-5 gap-3">
        <div class="input-group input-group-lg shadow-sm flex-grow-1" style="background: rgba(255, 255, 255, 0.05); border-radius: 50px; padding: 5px; border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px);">
            <span class="input-group-text border-0 bg-transparent text-white ps-4">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" name="search" class="form-control border-0 bg-transparent text-white shadow-none" 
                   placeholder="Search by Course Code or Name..." style="height: 50px;" 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            
            <button type="submit" class="btn btn-primary px-4" style="border-radius: 50px; background: var(--neon-gradient); margin-right: 5px;">
                Search
            </button>
        </div>

        <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
            <a href="register-course.php" class="btn btn-outline-light d-flex align-items-center justify-content-center" 
               style="height: 60px; border-radius: 50px; padding: 0 30px; border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); color: white; text-decoration: none;">
                <i class="fas fa-times me-2"></i> Show All
            </a>
        <?php endif; ?>
    </div>
</form>

    <h3 class="mb-4 text-white">Available Courses</h3>
    
    <div class="row">
        <?php 
        // 7. THE MAIN PHP LOOP
        if(mysqli_num_rows($result) > 0) {
            while($course = mysqli_fetch_assoc($result)) {
                
                // DATA PREP
                $c_code = $course['c_code'];
                $max = $course['max_student'];
                
                // Get Seat Count
                $seat_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM Registration WHERE c_code='$c_code' AND regisStat='Approved'"));
                $taken = $seat_res['total'];
                $available = $max - $taken;
                $percent = ($max > 0) ? ($taken / $max) * 100 : 100;
                
                // Get My Status
                $my_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT regisStat FROM Registration WHERE matricno='$matric_no' AND c_code='$c_code'"));
                $my_status = $my_res ? $my_res['regisStat'] : null;

                // Determine Colors based on status
                $card_border = "";
                $status_badge = "<span class='badge badge-success'>Open</span>";
                
                if ($available <= 0) {
                    $status_badge = "<span class='badge badge-danger'>Full</span>";
                    $card_border = "border-danger";
                } elseif ($available < 5) {
                    $status_badge = "<span class='badge badge-warning'>Filling Fast</span>";
                    $card_border = "border-warning";
                }
                
                if ($my_status) {
                     $card_border = "border-success"; // Highlight my courses
                     $status_badge = "<span class='badge badge-primary'><i class='fas fa-check'></i> $my_status</span>";
                }

                // --- START CARD HTML ---
                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="stat-card h-100 <?php echo $card_border; ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <?php echo $status_badge; ?>
                            <span class="badge" style="background: rgba(124, 58, 237, 0.2); color: #c4b5fd; border: 1px solid rgba(124, 58, 237, 0.3);">
                                <i class="fas fa-medal me-1"></i> <?php echo $course['c_credit']; ?> Credits
                            </span>
                        </div>
                        
                        <h4 class="text-white mt-2 mb-1"><?php echo $course['c_name']; ?></h4>
                        <p class="text-primary small"><?php echo $course['c_code']; ?> • Section <?php echo $course['section']; ?></p>
                        
                        <div class="course-meta">
                            <div><i class="fas fa-calendar-alt"></i> <?php echo $course['day_time']; // Ensure this column exists in DB ?></div>
                            </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text">Capacity</span>
                                <span class="<?php echo ($available > 0) ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $taken; ?>/<?php echo $max; ?> Taken
                                </span>
                            </div>
                            <div class="seat-progress">
                                <div class="progress-fill" style="width: <?php echo $percent; ?>%; background: <?php echo ($available > 0) ? 'var(--success)' : 'var(--danger)'; ?>;"></div>
                            </div>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="c_code" value="<?php echo $c_code; ?>">
                            
                            <?php if ($my_status == 'Approved' || $my_status == 'Pending'): ?>
                                <button type="submit" name="drop" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to drop this course?');">
                                    <i class="fas fa-times-circle"></i> Drop Course
                                </button>
                            <?php elseif ($available <= 0): ?>
                                <button type="button" class="btn btn-secondary w-100" disabled>
                                    Class Full
                                </button>
                            <?php else: ?>
                                <button type="submit" name="register" class="btn btn-primary w-100 btn-register" 
                                        style="border-radius: 12px; background: var(--neon-gradient);">
                                    <i class="fas fa-plus-circle me-2"></i> Register
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
                <?php
                // --- END CARD HTML ---
            }
        } else {
            echo "<p class='text-center text'>No courses found matching your search.</p>";
        }
        ?>
    </div>

</main>
</div>
<?php include '../includes/footer.php'; ?>