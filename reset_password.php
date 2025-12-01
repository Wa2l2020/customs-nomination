<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::create([
    'name' => 'wa2l',
    'email' => 'wa2l@customs.gov.eg', // Using a dummy email as it's required
    'password' => \Illuminate\Support\Facades\Hash::make('3416wa2l'),
    'role' => 'admin',
    'job_number' => '10001', // Dummy job number
    'phone' => '01000000000', // Dummy phone
]);

echo "User created successfully:\n";
echo "Name: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Password: 3416wa2l\n";
echo "Role: {$user->role}\n";
