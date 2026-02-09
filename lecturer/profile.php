<?php 
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// 1. SECURITY & DATA FETCHING
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Lecturer') {
    header("Location: ../auth/login.php");
    exit();
}

$lect_id = $_SESSION['key_id'];

// Join User and Lecturer tables to get complete info
$sql = "SELECT u.*, l.lectID, l.department, l.position, l.office_room, l.address, l.ic_no 
        FROM User u 
        JOIN Lecturer l ON u.username = l.username 
        WHERE l.lectID = '$lect_id'";

$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Handle cases where data might be empty
$address = $user['address'] ?? ''; 
$ic_number = $user['ic_no'] ?? '';
$office_room = $user['office_room'] ?? '';
$department = $user['department'] ?? 'General';
$position = $user['position'] ?? 'Lecturer';

include '../includes/header.php'; 
include 'sidebar.php'; 
?>

<main class="main-content">
    
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="topbar">
        <div class="topbar-left">
            <h1>My Profile 👤</h1>
            <p class="text mb-0">Manage your professional information</p>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-4 mb-4">
            <div class="modern-card h-100 text-center">
                
                <div class="profile-avatar-large" style="background: linear-gradient(135deg, #f472b6, #8b5cf6);">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                
                <h3 class="text-white mb-1"><?php echo $user['name']; ?></h3>
                <p style="color: #f472b6; font-weight: 600; letter-spacing: 1px;">ID: <?php echo $user['lectID']; ?></p>

                <div class="mt-4 text-start">
                    
                    <div class="info-pill">
                        <label>Designation</label>
                        <div class="value"><i class="fas fa-briefcase text-primary me-2"></i><?php echo $position; ?></div>
                    </div>

                    <div class="info-pill">
                        <label>Faculty</label>
                        <div class="value"><i class="fas fa-university text-primary me-2"></i><?php echo $user['faculty']; ?></div>
                    </div>

                    <div class="info-pill">
                        <label>Department</label>
                        <div class="value"><i class="fas fa-laptop-code text-primary me-2"></i><?php echo $department; ?></div>
                    </div>

                    <div class="sidebar-separator"></div>

                    <form action="update_password.php" method="POST">
                        <p class="text small mb-2 text-center text">
                            <i class="fas fa-lock me-1"></i> Update Password
                        </p>
                        
                        <input type="password" class="form-control-sidebar mb-2" name="new_pass" placeholder="New Password" required>
                        <input type="password" class="form-control-sidebar" name="confirm_pass" placeholder="Confirm Password" required>
                        
                        <button type="submit" class="btn-sidebar-update mt-3 w-100" style="border-color: #f472b6; color: #f472b6;">
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
                    
                    <button type="button" id="btn-edit-profile" class="btn-edit-solid" style="background: linear-gradient(135deg, #f472b6, #ec4899); border:none;">
                        <i class="fas fa-pen me-2"></i> Edit Details
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
                                <input type="text" class="form-control-dark locked-view editable-field" name="ic_no" value="<?php echo $ic_number; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group-dark">
                                <label>Staff Email (Official)</label>
                                <input type="email" class="form-control-dark locked-view editable-field" name="email" value="<?php echo $user['email']; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group-dark">
                                <label>Home Address</label>
                                <input type="text" class="form-control-dark locked-view editable-field" name="address" value="<?php echo $address; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group-dark">
                                <label>Office Room</label>
                                <input type="text" class="form-control-dark locked-view editable-field" name="office_room" value="<?php echo $office_room; ?>" readonly>
                            </div>
                        </div>

                    </div>

                    <div class="edit-actions-toolbar mt-4" id="editActions" style="display: none;">
                        <button type="button" id="btn-cancel" class="btn btn-outline-secondary text-white me-2" style="border-radius: 50px;">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success" style="border-radius: 50px; padding-left: 25px; padding-right: 25px; background: #10b981; border: none;">
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
            input.style.border = '1px solid #f472b6'; // Using the pink theme color
            input.style.background = 'rgba(244, 114, 182, 0.1)';
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