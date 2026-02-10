<?php 
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

$activePage = 'lecturers'; 

// 2. AUTO-GENERATE ID 
$current_year = date('y');
$prefix = "L" . $current_year; 

$check_sql = "SELECT lectID FROM Lecturer WHERE lectID LIKE '$prefix%' ORDER BY lectID DESC LIMIT 1";
$check_res = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_res) > 0) {
    $row = mysqli_fetch_assoc($check_res);
    $last_num = intval(substr($row['lectID'], 3)); 
    $next_num = $last_num + 1;
} else {
    $next_num = 1;
}
$next_lect_id = $prefix . str_pad($next_num, 3, '0', STR_PAD_LEFT);


// 3. FETCH LECTURERS
$sql = "SELECT l.*, u.name, u.email, u.faculty, 
        (SELECT COUNT(*) FROM course_lecturer cl WHERE cl.lectID = l.lectID) as course_count 
        FROM Lecturer l 
        JOIN User u ON l.username = u.username 
        ORDER BY l.lectID ASC";
$res = mysqli_query($conn, $sql);

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
            <h1>Lecturer Management 👨‍🏫</h1>
            <p class="text mb-0">Admin User • <strong style="color: var(--secondary);">System Administrator</strong></p>
        </div>
        <div class="topbar-right">
             <button class="btn btn-primary" onclick="openLecturerModal()">
                <i class="fas fa-plus"></i> Add Lecturer
            </button>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header" style="justify-content: flex-end; margin-bottom: 15px;">
            <input type="text" id="searchInput" onkeyup="searchTable()" class="form-control" 
                   placeholder="Search Name or ID..." 
                   style="max-width: 300px; color: #ffffff; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
        </div>

        <div class="table-responsive">
            <table class="data-table" id="lectTable">
                <thead>
                    <tr>
                        <th>Lecturer</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Courses</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center" style="gap: 15px;">
                                        <div class="avatar-circle" style="background: linear-gradient(135deg, #10b981, #34d399); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            <?php echo strtoupper(substr($row['name'], 0, 2)); ?>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-white"><?php echo $row['name']; ?></div>
                                            <div class="text small" style="color: #aaa;"><?php echo $row['lectID']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="color: white;"><?php echo $row['department']; ?></div>
                                    <div style="font-size: 0.8rem; color: #888;"><?php echo $row['faculty']; ?></div>
                                </td>
                                <td class="text" style="color: #ccc;"><?php echo $row['email']; ?></td>
                                <td><span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #6ee7b7;"><?php echo $row['course_count']; ?> Active</span></td>
                                <td style="text-align: right;">
                                    <a href="view-lecturer.php?id=<?php echo $row['lectID']; ?>" class="btn btn-sm btn-tertiary" style="color: #fff; text-decoration: none;">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No lecturers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="addLecturerModal" class="modal-overlay">
    <div class="modal-wrapper modal-lg">
        <div class="modal-header">
            <h3 class="modal-title">Add New Lecturer</h3>
            <button class="close-modal-btn" onclick="closeLecturerModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="process-add-lecturer.php" method="POST">
            <div class="modal-body">
                
                <div class="form-row">
                    <div class="form-group col-half">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" class="form-control-dark" placeholder="e.g. Dr. Sarah Smith" required>
                    </div>
                    <div class="form-group col-half">
                        <label>Lecturer ID (Auto) *</label>
                        <input type="text" name="lect_id" class="form-control-dark" 
                               value="<?php echo $next_lect_id; ?>" readonly 
                               style="background: rgba(255,255,255,0.1); color: #4ade80; font-weight: bold;">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" class="form-control-dark" placeholder="lecturer@utm.my" required>
                </div>

                <div class="form-row">
                    <div class="form-group col-half">
                        <label>Faculty</label>
                        <select name="faculty" class="form-control-dark">
                            <option value="Computing">Faculty of Computing</option>
                            <option value="Engineering">Faculty of Engineering</option>
                            <option value="Science">Faculty of Science</option>
                        </select>
                    </div>
                    <div class="form-group col-half">
                        <label>Department</label>
                        <select name="department" class="form-control-dark">
                            <option value="Software Engineering">Software Engineering</option>
                            <option value="Network & Security">Network & Security</option>
                            <option value="Data Science">Data Science</option>
                            <option value="Information Systems">Information Systems</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                     <label>Office Room</label>
                     <input type="text" name="office" class="form-control-dark" placeholder="e.g. N28 - Room 405">
                </div>

                <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">

                <h4 style="font-size: 1rem; color: #ccc; margin-bottom: 15px;">Login Credentials</h4>
                
                <div class="form-row">
                    <div class="form-group col-half">
                        <label>Username (Same as ID)</label>
                        <input type="text" name="username" class="form-control-dark" value="<?php echo $next_lect_id; ?>" readonly style="background: rgba(255,255,255,0.05); color: #ccc;">
                    </div>
                    <div class="form-group col-half">
                        <label>Auto-Generated Password</label>
                        <div style="position: relative;">
                            <input type="text" name="password" id="auto_password" class="form-control-dark" readonly style="color: #fcd34d; font-family: monospace; font-weight: bold; letter-spacing: 1px;">
                            <button type="button" onclick="generatePassword()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #ccc; cursor: pointer;" title="Regenerate">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <small class="text-muted">Copy this password before saving.</small>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Create Lecturer</button>
                <button type="button" class="btn btn-secondary" onclick="closeLecturerModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openLecturerModal() {
        document.getElementById('addLecturerModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        generatePassword(); // Generate a secure password immediately
    }

    function closeLecturerModal() {
        document.getElementById('addLecturerModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Function to generate random password
    function generatePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#";
        let password = "";
        for (let i = 0; i < 8; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('auto_password').value = password;
    }

    function searchTable() {
        let input = document.getElementById("searchInput");
        let filter = input.value.toUpperCase();
        let table = document.getElementById("lectTable");
        let tr = table.getElementsByTagName("tr");
        for (let i = 1; i < tr.length; i++) {
            let tdName = tr[i].getElementsByTagName("td")[0];
            if (tdName) {
                let txtValue = tdName.textContent || tdName.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            closeLecturerModal();
        }
    }
</script>

<?php include '../includes/footer.php'; ?>