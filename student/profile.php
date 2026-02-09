<?php 
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// 1. SECURITY & DATA FETCHING
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../auth/login.php");
    exit();
}

$matric_no = $_SESSION['key_id'];

// Join User and Student tables to get complete info
$sql = "SELECT u.*, s.matricno, s.program, s.year 
        FROM User u 
        JOIN Student s ON u.username = s.username 
        WHERE s.matricno = '$matric_no'";

$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Handle cases where address/ic might be empty
$address = $user['address'] ?? ''; 
$ic_number = $user['ic_number'] ?? '';

include '../includes/header.php'; 
include 'sidebar.php'; 
?>

<main class="main-content">
    
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="topbar">
        <div class="topbar-left">
            <h1>My Profile 👤</h1>
            <p class="text mb-0">Manage your personal information</p>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-4 mb-4">
            <div class="modern-card h-100 text-center">
                
                <div class="profile-avatar-large">
                    <i class="far fa-user"></i>
                </div>
                <h3 class="text-white mb-1"><?php echo $user['name']; ?></h3>
                <p style="color: #d8b4fe; font-weight: 600; letter-spacing: 1px;"><?php echo $user['matricno']; ?></p>

                <div class="mt-4 text-start">
                    
                    <div class="info-pill">
                        <label>Role</label>
                        <div class="value"><i class="fas fa-user-graduate text-primary me-2"></i><?php echo $user['role']; ?></div>
                    </div>

                    <div class="info-pill">
                        <label>Faculty</label>
                        <div class="value"><i class="fas fa-university text-primary me-2"></i><?php echo $user['faculty']; ?></div>
                    </div>

                    <div class="info-pill">
                        <label>Program</label>
                        <div class="value"><i class="fas fa-graduation-cap text-primary me-2"></i><?php echo $user['program']; ?></div>
                    </div>

                    <div class="sidebar-separator"></div>

                    <form action="update_password.php" method="POST">
                        <p class="text small mb-2 text-center">
                            <i class="fas fa-lock me-1"></i> Update Password
                        </p>
                        
                        <input type="password" class="form-control-sidebar mb-2" name="new_pass" placeholder="New Password" required>
                        <input type="password" class="form-control-sidebar" name="confirm_pass" placeholder="Confirm Password" required>
                        
                        <button type="submit" class="btn-sidebar-update mt-3 w-100">
                            Update Password
                        </button>
                    </form>

                </div>

            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="modern-card h-100">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-white mb-0">Personal Details</h3>
                    
                    <button type="button" id="btn-edit-profile" class="btn-edit-solid">
                        <i class="fas fa-pen"></i> Edit Details
                    </button>
                </div>
                
                <form id="profileForm" action="update_profile.php" method="POST">
                    <div class="row">
                        
                        <div class="col-md-6">
                            <div class="form-group-dark">
                                <label>Full Name</label>
                                <input type="text" class="form-control-dark locked-view" value="<?php echo $user['name']; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group-dark">
                                <label>IC / Passport Number</label>
                                <input type="text" class="form-control-dark locked-view editable-field" name="ic_number" value="<?php echo $ic_number; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group-dark">
                                <label>Student Email</label>
                                <input type="email" class="form-control-dark locked-view editable-field" name="email" value="<?php echo $user['email']; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group-dark">
                                <label>Address</label>
                                <input type="text" class="form-control-dark locked-view editable-field" name="address" value="<?php echo $address; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group-dark">
                                <label>Academic Advisor</label>
                                <div class="d-flex align-items-center mt-2 p-3" style="background: rgba(255,255,255,0.05); border-radius: 12px;">
                                    <div style="width: 40px; height: 40px; background: #333; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="fas fa-chalkboard-teacher text-white"></i>
                                    </div>
                                    <div>
                                        <div class="text-white font-weight-bold">Dr. Azman Bin Yasin</div>
                                        <div class="text small">Senior Lecturer</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="edit-actions-toolbar mt-4" id="editActions" style="display: none;">
                        <button type="button" id="btn-cancel" class="btn btn-outline-secondary text-white me-2" style="border-radius: 50px;">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success" style="border-radius: 50px; padding-left: 25px; padding-right: 25px;">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('btn-edit-profile');
    const cancelBtn = document.getElementById('btn-cancel');
    const actionsToolbar = document.getElementById('editActions');
    const inputs = document.querySelectorAll('.editable-field');

    // Enable Edit Mode
    editBtn.addEventListener('click', function() {
        actionsToolbar.style.display = 'block';
        editBtn.style.display = 'none';
        
        inputs.forEach(input => {
            input.removeAttribute('readonly');
            input.style.border = '1px solid var(--primary)';
            input.style.background = 'rgba(255,255,255,0.1)';
        });
    });

    // Cancel Edit Mode
    cancelBtn.addEventListener('click', function() {
        location.reload(); // Simple refresh to reset values
    });
});
</script>

<div class="wrapper">
<?php include '../includes/footer.php'; ?>