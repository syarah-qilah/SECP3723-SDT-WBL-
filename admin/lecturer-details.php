<?php 
$activePage = 'lecturers'; 
include '../includes/header.php'; 
include 'sidebar.php'; 
?>

<main class="main-content">
    
    <div class="mb-4">
        <a href="manage-lect.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Lecturers
        </a>
    </div>

    <div class="detail-header-card">
        <div class="detail-avatar gradient-teal">
            <i class="fas fa-user-tie"></i>
        </div>
        
        <div class="detail-info">
            <h2 class="detail-name">Dr. Sarah Smith</h2>
            
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="icon-box">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <span class="label">Email</span>
                        <span class="value">lecturer@university.edu</span>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="icon-box">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <span class="label">Department</span>
                        <span class="value">Computer Science</span>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="icon-box">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <span class="label">Phone</span>
                        <span class="value">N/A</span>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="icon-box">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <span class="label">Office</span>
                        <span class="value">N/A</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-header mt-5 mb-3">
        <h3 class="section-title-border">Assigned Courses</h3>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Semester</th>
                        <th>Credits</th>
                        <th>Students</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="font-weight-bold text-white">Data Structures and Algorithms</div>
                            <div class="text-purple small">CS201</div>
                        </td>
                        <td>Fall 2024</td>
                        <td>4</td>
                        <td>6/25</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="font-weight-bold text-white">Database Systems</div>
                            <div class="text-purple small">CS301</div>
                        </td>
                        <td>Fall 2024</td>
                        <td>3</td>
                        <td>4/20</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="font-weight-bold text-white">Web Development</div>
                            <div class="text-purple small">CS102</div>
                        </td>
                        <td>Spring 2024</td>
                        <td>3</td>
                        <td>1/30</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</main>

<script src="../assets/js/admin.js"></script>
<?php include '../includes/footer.php'; ?>