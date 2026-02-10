</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="/smsCopy/assets/js/main.js"></script>

    <?php
        $current_url = $_SERVER['REQUEST_URI'];

       
        if (strpos($current_url, '/student/') !== false) {
            
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/smsCopy/assets/js/student.js')) {
                echo '<script src="/smsCopy/assets/js/student.js"></script>';
            }
        } 
        
        
        elseif (strpos($current_url, '/admin/') !== false) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/smsCopy/assets/js/admin.js')) {
                echo '<script src="/smsCopy/assets/js/admin.js"></script>';
            }
        }
    ?>

</body>
</html>