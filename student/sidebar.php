<aside class="sidebar">
    
    <div class="sidebar-top">
        <div class="sidebar-header">
            <div class="brand-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <h2>SMS</h2>
                <p>Student Portal</p>
            </div>
        </div>
        
        <nav class="sidebar-menu">
            <a href="/smsCopy/student/dashboard.php" class="menu-item <?php echo ($activePage == 'dashboard') ? 'active' : ''; ?>">
                <span class="menu-item-icon"><i class="fas fa-chart-pie"></i></span>
                <span>Dashboard</span>
            </a>

            <a href="/smsCopy/student/register-course.php" class="menu-item <?php echo ($activePage == 'register') ? 'active' : ''; ?>">
                <span class="menu-item-icon"><i class="fas fa-file-signature"></i></span>
                <span>Register Course</span>
            </a>

            <a href="/smsCopy/student/my-courses.php" class="menu-item <?php echo ($activePage == 'courses') ? 'active' : ''; ?>">
                <span class="menu-item-icon"><i class="fas fa-book"></i></span>
                <span>My Courses</span>
            </a>

            <a href="/smsCopy/student/profile.php" class="menu-item <?php echo ($activePage == 'profile') ? 'active' : ''; ?>">
                <span class="menu-item-icon"><i class="fas fa-user"></i></span>
                <span>My Profile</span>
            </a>
        </nav>
    </div>

    <div class="sidebar-footer">
        
        <div class="mini-info-box">
            <label>Student Email</label>
            <div class="value">syarah@graduate.utm.my</div>
        </div>

        <div class="mini-info-box">
            <label>Student ID</label>
            <div class="value" style="color: #60a5fa;">A20EC0001</div>
        </div>

        <a href="../auth/login.php" class="btn-sidebar-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

</aside>

<script>
    // Keep your existing Active Link script, it is good!
    document.addEventListener("DOMContentLoaded", function() {
        const currentLocation = window.location.href;
        const menuLinks = document.querySelectorAll('.sidebar-menu a');
        menuLinks.forEach(link => {
            if (link.href === currentLocation) {
                link.classList.add('active');
            }
        });
    });
</script>