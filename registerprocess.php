<?php 
//Connect to DB 
include('dbconnect.php');

//Retrieve data from database
$fname = $_POST['fname'];
$fpwd = $_POST['fpwd'];
$femail = $_POST['femail'];
$foperator= $_POST['foperator'];
$fphone = $_POST['fphone'];
$fgender= $_POST['fgender'];
$fprog= $_POST['fprog'];
$fcol= $_POST['fcol'];


//SQLinsert operation - CREATE NEW DATA
$sql = "INSERT INTO tb_user(u_pwd, u_name, u_phoneoperator, u_phnumber, u_email, u_gender, u_programme, u_residential, u_type)
        VALUES('$fpwd','$fname','$foperator','$fphone','$femail','$fgender','$fprog','$fcol','03')";

//Execute SQL
mysqli_query($con,$sql);

//Close connection
mysqli_close($con);

//Notification - success or fail

//Redirect page -Success
header('Location: registerlogin.php');


?>
