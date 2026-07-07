<?php
$exts = ['mongodb', 'soap', 'zip', 'xsl', 'sockets', 'sodium', 'sqlite3', 'tidy', 'curl', 'gd', 'mbstring', 'mysqli', 'openssl', 'pdo_mysql', 'fileinfo', 'intl', 'exif'];
foreach ($exts as $ext) {
    echo "$ext: " . (extension_loaded($ext) ? "YES" : "NO") . "\n";
}
