<?php
echo "<h1>Searching for get_current_user() function...</h1>";
echo "<pre>";

$files_to_check = [
    'includes/session.php',
    'includes/functions.php',
    'config/database.php',
    'auth/login-process.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, 'function get_current_user') !== false) {
            echo "✓ FOUND in: $file\n";
            
            // Show the line numbers
            $lines = explode("\n", $content);
            foreach ($lines as $num => $line) {
                if (strpos($line, 'function get_current_user') !== false) {
                    echo "   Line " . ($num + 1) . ": " . trim($line) . "\n";
                }
            }
            echo "\n";
        } else {
            echo "- Not in: $file\n";
        }
    }
}

echo "</pre>";
?>