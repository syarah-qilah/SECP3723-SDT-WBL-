<?php

session_start();

//Connect to DB 
include('dbconnect.php');

//Retrieve data from database
$fid = $_POST['fid'];
$fpwd = $_POST['fpwd'];

// SQL Retrieve operation - RETRIEVE
$sql= "SELECT * FROM tb_user
        WHERE u_id='$fid' AND u_pwd='$fpwd'";

//Execute SQL 
$result=mysqli_query($con,$sql);
$row=mysqli_fetch_array($result);

//Redirect to the corresponding page - Simple rule-based AI solution
$count=mysqli_num_rows($result);

if($count==1)
{
    $_SESSION['u_id']= session_id();
    $_SESSION['uid']=$fid;

    if ($row['u_type']=='01')
    {
        header('Location:staff.php');
    }
        if ($row['u_type']=='02')
    {
        header('Location:lecturer.php');
    }
        if ($row['u_type']=='03')
    {
        header('Location:student.php');
    }
}
else   //no user found
{
    header('Location:login.php');
}

?>