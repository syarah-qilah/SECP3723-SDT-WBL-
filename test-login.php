<?php
session_start();

echo "<h1>Session Test</h1>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<hr>";
echo "<a href='auth/logout.php'>Clear Session & Logout</a><br>";
echo "<a href='auth/login.php'>Go to Login</a>";
?>