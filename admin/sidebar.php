<aside class="sidebar">
    <div class="sidebar-top">
        <div class="sidebar-header">
            <div class="brand-icon" style="background: linear-gradient(135deg, #2dd4bf, #0891b2);">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h2>SMS</h2>
                <p>IT Admin Portal</p>
            </div>
        </div>
        
        <nav class="sidebar-menu">
            <a href="dashboard.php" class="menu-item <?= $activePage == 'dashboard' ? 'active' : '' ?>">
                <span class="menu-item-icon"><i class="fas fa-chart-line"></i></span>
                <span>Dashboard</span>
            </a>

            <a href="manage-courses.php" class="menu-item <?= $activePage == 'courses' ? 'active' : '' ?>">
                <span class="menu-item-icon"><i class="fas fa-database"></i></span>
                <span>Course Management</span>
            </a>

            <a href="manage-regis.php" class="menu-item <?= $activePage == 'registrations' ? 'active' : '' ?>">
                <span class="menu-item-icon"><i class="fas fa-file-signature"></i></span>
                <span>Registration Management</span>
            </a>

            <a href="manage-student.php" class="menu-item <?= $activePage == 'students' ? 'active' : '' ?>">
                <span class="menu-item-icon"><i class="fas fa-users-cog"></i></span>
                <span>Student Records</span>
            </a>
            
             <a href="manage-lect.php" class="menu-item <?= $activePage == 'lecturers' ? 'active' : '' ?>">
                <span class="menu-item-icon"><i class="fas fa-users-cog"></i></span>
                <span>Lecturer Records</span>
            </a>

        </nav>
    </div>

    <div class="sidebar-footer">
        <div class="mini-info-box">
            <label>Admin Access</label>
            <div class="value" style="color: #2dd4bf;">Level: SuperAdmin</div>
        </div>
        <div class="mini-info-box">
            <label>System Staff ID</label>
            <div class="value">IT-99234</div>
        </div>
        <a href="../auth/login.php" class="btn-sidebar-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>