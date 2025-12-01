<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $pdo = Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "Database Connection Successful!\n";
    echo "Driver: " . Illuminate\Support\Facades\DB::connection()->getDriverName() . "\n";
    echo "Database: " . Illuminate\Support\Facades\DB::connection()->getDatabaseName() . "\n";
} catch (\Exception $e) {
    echo "Database Connection Failed: " . $e->getMessage() . "\n";
}
