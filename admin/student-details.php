<?php 
$activePage = 'students'; 
include '../includes/header.php'; 
include 'sidebar.php'; 

// Simulate fetching ID 
$student_id = isset($_GET['id']) ? $_GET['id'] : 'S2024006';
?>

<main class="main-content">
    
    <div class="topbar" style="justify-content: flex-start; gap: 20px;">
        <a href="manage-student.php" class="btn btn-secondary" style="background: transparent; border: 1px solid #374151;">
            <i class="fas fa-arrow-left"></i> Back to Students
        </a>
    </div>

    <div class="profile-card">
        <div class="profile-avatar gradient-hero">
            <i class="fas fa-user-graduate" style="font-size: 2.5rem; color: white;"></i>
        </div>
        
        <div class="profile-info-main">
            <h2>Sarah Johnson</h2>
            <div class="profile-badges">
                <span class="id-badge"><i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($student_id); ?></span>
                <span class="dept-badge"><i class="fas fa-building"></i> Information Systems</span>
            </div>
        </div>

        <div class="profile-info-contact">
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <label>Email</label>
                    <span>sarah.j@university.edu</span>
                </div>
            </div>
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <div>
                    <label>Phone</label>
                    <span>+60 12-345 6789</span>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px;">
        <h3 class="section-title">Registered Courses</h3>
        
        <div class="content-card" style="min-height: 300px; display: flex; align-items: center; justify-content: center;">
            
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <p>No courses registered yet</p>
            </div>

        </div>
    </div>

</main>

<?php include '../includes/footer.php'; ?>