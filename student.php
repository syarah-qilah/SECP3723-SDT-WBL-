<?php
    include ('mysession.php');
    if (!session_id())
    {
        session_start();
    }

    include ('headerstudent.php');
?>


Student Page


<?php
    include 'footer.php';
?>