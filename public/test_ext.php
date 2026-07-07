<?php
echo "extension_loaded: " . (extension_loaded("mongodb") ? "YES" : "NO") . "\n";
echo "class_exists: " . (class_exists("MongoDB\Driver\Manager") ? "YES" : "NO") . "\n";
echo "ini: " . ini_get("extension_dir") . "\n";

