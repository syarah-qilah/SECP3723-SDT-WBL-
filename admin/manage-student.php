<?php 
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

$activePage = 'students'; 

// 2. AUTO-GENERATE NEXT MATRIC NO
$current_prefix = "A25EC"; 
$last_id_sql = "SELECT matricno FROM Student WHERE matricno LIKE '$current_prefix%' ORDER BY matricno DESC LIMIT 1";
$last_id_res = mysqli_query($conn, $last_id_sql);

if (mysqli_num_rows($last_id_res) > 0) {
    $row = mysqli_fetch_assoc($last_id_res);
    $last_number = intval(substr($row['matricno'], 5)); 
    $next_number = $last_number + 1;
} else {
    $next_number = 1; 
}
$next_matric = $current_prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);


// 3. FETCH STUDENTS
$sql = "SELECT s.*, u.name, u.email, u.faculty, 
        (SELECT COUNT(*) FROM Registration r WHERE r.matricno = s.matricno) as course_count 
        FROM Student s 
        JOIN User u ON s.username = u.username 
        ORDER BY s.matricno ASC";
$res = mysqli_query($conn, $sql);

// ... Previous database connection code ...

if(isset($_POST['add_student_btn'])) {
    
    // 1. Get Data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $generated_password = $_POST['password']; // The auto-generated one
    $username = $email; // Or however you generate usernames

    // 2. Insert into Database
    $query = "INSERT INTO students (name, email, password) VALUES ('$name', '$email', '$generated_password')";
    $query_run = mysqli_query($con, $query);

    if($query_run) {
        
        // 3. INCLUDE EMAIL CONFIG AND SEND
        include('../includes/email-config.php');
        
        $emailSent = sendWelcomeEmail($email, $name, $username, $generated_password);

        if($emailSent) {
            $_SESSION['status'] = "Student Added & Email Sent!";
        } else {
            $_SESSION['status'] = "Student Added, but Email Failed.";
        }
        
        header('Location: manage-students.php');
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
            <h1>Student Management 🎓</h1>
            <p class="text mb-0">Admin User • <strong style="color: var(--secondary);">System Administrator</strong></p>
        </div>
        <div class="topbar-right">
             <button class="btn btn-primary" onclick="openAddStudentModal()">
                <i class="fas fa-plus"></i> Add Student
            </button>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header" style="justify-content: flex-end; margin-bottom: 15px;">
            <input type="text" id="searchInput" onkeyup="searchTable()" class="form-control" 
                   placeholder="Search Name or Matric No..." 
                   style="max-width: 300px; color: #ffffff; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
        </div>

        <div class="table-responsive">
            <table class="data-table" id="studentTable">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Matric No</th>
                        <th>Program / Faculty</th>
                        <th>Email</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center" style="gap: 15px;">
                                        <div class="avatar-circle" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            <?php echo strtoupper(substr($row['name'], 0, 2)); ?>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-white"><?php echo $row['name']; ?></div>
                                            <div class="text small" style="color: #aaa;"><?php echo $row['course_count']; ?> courses enrolled</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge" style="background: rgba(139, 92, 246, 0.2); color: #c4b5fd;"><?php echo $row['matricno']; ?></span></td>
                                <td>
                                    <div style="color: white;"><?php echo $row['program']; ?></div>
                                    <div style="font-size: 0.8rem; color: #888;"><?php echo $row['faculty']; ?></div>
                                </td>
                                <td class="text" style="color: #ccc;"><?php echo $row['email']; ?></td>
                                <td style="text-align: right;">
                                    <a href="view-student.php?id=<?php echo $row['matricno']; ?>" class="btn btn-sm btn-tertiary" style="color: #fff; text-decoration: none;">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
</main>

<div id="addStudentModal" class="modal-overlay">
    <div class="modal-wrapper modal-lg">
        
        <div class="modal-header">
            <h3>Add New Student</h3>
            <button class="close-modal-btn" onclick="closeAddStudentModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="process-add-student.php" method="POST" id="addStudentForm">
            <div class="modal-body">
                
                <div class="form-row">
                    <div class="form-group col-half">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" class="form-control-dark" placeholder="e.g. Ali Bin Abu" required>
                    </div>
                    <div class="form-group col-half">
                        <label>Matric No (Auto-Generated) *</label>
                        <input type="text" name="matric_no" id="input_matric" class="form-control-dark" 
                               value="<?php echo $next_matric; ?>" readonly 
                               style="background: rgba(255,255,255,0.1); color: #4ade80; font-weight: bold;">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control-dark" placeholder="student@graduate.utm.my" required>
                </div>

                <div class="form-row">
                    <div class="form-group col-half">
                        <label>Faculty</label>
                        <select name="faculty" id="facultySelect" class="form-control-dark" required onchange="updatePrograms()">
                            <option value="" disabled selected>Select Faculty</option>
                            <option value="Computing">Faculty of Computing</option>
                            <option value="Engineering">Faculty of Engineering</option>
                            <option value="Science">Faculty of Science</option>
                            <option value="Management">Faculty of Management</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-half">
                        <label>Program Code</label>
                        <select name="program" id="programSelect" class="form-control-dark" required>
                            <option value="" disabled selected>Select Faculty First</option>
                        </select>
                    </div>
                </div>

                <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">

                <h4 style="font-size: 1rem; color: #ccc; margin-bottom: 15px;">Login Credentials</h4>
                
                <div class="form-row">
                    <div class="form-group col-half">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control-dark" value="<?php echo $next_matric; ?>" readonly style="background: rgba(255,255,255,0.05); color: #ccc;">
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
                <button type="submit" class="btn btn-primary">Add Student</button>
                <button type="button" class="btn btn-secondary" onclick="closeAddStudentModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- 1. DATA FOR DEPENDENT DROPDOWNS ---
    const programData = {
        "Computing": [
            {code: "SECJ", name: "Software Engineering (SECJ)"},
            {code: "SECP", name: "Data Engineering (SECP)"},
            {code: "SECR", name: "Network Security (SECR)"},
            {code: "SECB", name: "Bioinformatics (SECB)"},
            {code: "SECG", name: "Graphics & Multimedia (SECG)"}
        ],
        "Engineering": [
            {code: "SKEE", name: "Electrical Engineering (SKEE)"},
            {code: "SKEM", name: "Mechanical Engineering (SKEM)"},
            {code: "SKEC", name: "Civil Engineering (SKEC)"},
            {code: "SKEL", name: "Electronics (SKEL)"}
        ],
        "Science": [
            {code: "SSCM", name: "Mathematics (SSCM)"},
            {code: "SSCP", name: "Physics (SSCP)"},
            {code: "SSCC", name: "Chemistry (SSCC)"}
        ],
        "Management": [
            {code: "SHAD", name: "Human Resource Dev (SHAD)"},
            {code: "SHAM", name: "Management (SHAM)"},
            {code: "SHAF", name: "Marketing (SHAF)"}
        ]
    };

    function updatePrograms() {
        const faculty = document.getElementById("facultySelect").value;
        const programSelect = document.getElementById("programSelect");
        
        // Clear existing options
        programSelect.innerHTML = '<option value="" disabled selected>Select Program</option>';
        
        // If the selected faculty exists in our data, populate options
        if (programData[faculty]) {
            programData[faculty].forEach(prog => {
                const option = document.createElement("option");
                option.value = prog.code;
                option.textContent = prog.name;
                programSelect.appendChild(option);
            });
        }
    }

    // --- 2. MODAL LOGIC ---
    function openAddStudentModal() {
        document.getElementById('addStudentModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        generatePassword(); 
        
        // Optional: Reset dropdowns on open
        document.getElementById("facultySelect").value = "";
        document.getElementById("programSelect").innerHTML = '<option value="" disabled selected>Select Faculty First</option>';
    }

    function closeAddStudentModal() {
        document.getElementById('addStudentModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // --- 3. PASSWORD GENERATOR ---
    function generatePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#";
        let password = "";
        for (let i = 0; i < 8; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('auto_password').value = password;
    }

    // --- 4. SEARCH FUNCTION ---
    function searchTable() {
        let input = document.getElementById("searchInput");
        let filter = input.value.toUpperCase();
        let table = document.getElementById("studentTable");
        let tr = table.getElementsByTagName("tr");
        for (let i = 1; i < tr.length; i++) {
            let tdName = tr[i].getElementsByTagName("td")[0];
            let tdMatric = tr[i].getElementsByTagName("td")[1];
            if (tdName || tdMatric) {
                let txtValueName = tdName.textContent || tdName.innerText;
                let txtValueMatric = tdMatric.textContent || tdMatric.innerText;
                if (txtValueName.toUpperCase().indexOf(filter) > -1 || txtValueMatric.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            closeAddStudentModal();
        }
    }
</script>

<?php include '../includes/footer.php'; ?>