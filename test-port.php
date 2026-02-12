<?php
$ports = [25, 465, 587, 2525];
$host = 'smtp.gmail.com';

foreach ($ports as $port) {
    $connection = @fsockopen($host, $port, $errno, $errstr, 5);
    if ($connection) {
        echo "✅ Port $port: OPEN<br>";
        fclose($connection);
    } else {
        echo "❌ Port $port: BLOCKED (Error: $errstr)<br>";
    }
}
?>