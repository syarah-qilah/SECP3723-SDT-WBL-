<?php
    include ('mysession.php');
    if (!session_id())
    {
        session_start();
    }

    include 'headerlec.php';
?>

Lecturer Page


<?php
    include 'footer.php';
?>