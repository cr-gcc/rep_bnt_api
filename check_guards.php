<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Permissions ===\n";
$permissions = DB::table('permissions')->select('id', 'name', 'guard_name')->get();
foreach ($permissions as $perm) {
    echo "ID: {$perm->id}, Name: {$perm->name}, Guard: {$perm->guard_name}\n";
}

echo "\n=== Checking Roles ===\n";
$roles = DB::table('roles')->select('id', 'name', 'guard_name')->get();
foreach ($roles as $role) {
    echo "ID: {$role->id}, Name: {$role->name}, Guard: {$role->guard_name}\n";
}
