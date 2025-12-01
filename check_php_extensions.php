<?php

echo "=======================================\n";
echo " PHP Environment Diagnostic Tool\n";
echo "=======================================\n\n";

$requiredExtensions = [
    "curl",
    "fileinfo",
    "mbstring",
    "openssl",
    "pdo_mysql",
    "pdo_sqlite",
    "sqlite3",
    "zip"
];

echo "🔍 Checking PHP version...\n";
echo shell_exec("php -v");
echo "\n---------------------------------------\n";

echo "🔍 Checking loaded PHP extensions...\n\n";

$loaded = get_loaded_extensions();
$missing = [];

foreach ($requiredExtensions as $ext) {
    if (in_array($ext, array_map('strtolower', $loaded))) {
        echo "✔ $ext is loaded\n";
    } else {
        echo "❌ $ext is MISSING!\n";
        $missing[] = $ext;
    }
}

echo "\n---------------------------------------\n";

if (!empty($missing)) {
    echo "❗ Missing Extensions:\n";
    foreach ($missing as $ext) {
        echo "- $ext\n";
    }

    echo "\n⚠ الحل المقترح:\n";
    echo "افتح ملف php.ini وابحث عن:\n";
    foreach ($missing as $ext) {
        echo ";extension=$ext\n";
    }
    echo "\nوشيل علامة ; من بداية السطر، واتأكد أن extension_dir صحيح.\n";
} else {
    echo "🎉 كل الامتدادات المهمة تعمل بشكل صحيح!\n";
}

echo "=======================================\n";
