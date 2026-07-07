<?php
// Try to load the extension directly
$ext = 'C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\ext\php_mongodb.dll';
echo "File exists: " . (file_exists($ext) ? "YES" : "NO") . "\n";
echo "dl enabled: " . (ini_get('enable_dl') ? "YES" : "NO") . "\n";
try {
    dl($ext);
    echo "dl() succeeded\n";
} catch (Throwable $e) {
    echo "dl() failed: " . $e->getMessage() . "\n";
}
echo "extension_loaded: " . (extension_loaded('mongodb') ? "YES" : "NO") . "\n";
