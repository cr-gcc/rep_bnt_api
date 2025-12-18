<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Updating Guards from 'web' to 'api' ===\n\n";

// Update permissions
$permissionsUpdated = DB::table('permissions')
    ->where('guard_name', 'web')
    ->update(['guard_name' => 'api']);
echo "Updated {$permissionsUpdated} permissions to use 'api' guard\n";

// Update roles
$rolesUpdated = DB::table('roles')
    ->where('guard_name', 'web')
    ->update(['guard_name' => 'api']);
echo "Updated {$rolesUpdated} roles to use 'api' guard\n";

// Update model_has_permissions
$modelPermsUpdated = DB::table('model_has_permissions')
    ->where('model_type', 'App\\Models\\User')
    ->update(['model_type' => 'App\\Models\\User']);
echo "Checked model_has_permissions table\n";

// Update model_has_roles
$modelRolesUpdated = DB::table('model_has_roles')
    ->where('model_type', 'App\\Models\\User')
    ->update(['model_type' => 'App\\Models\\User']);
echo "Checked model_has_roles table\n";

echo "\n=== Verification ===\n";
$permissions = DB::table('permissions')->select('id', 'name', 'guard_name')->get();
foreach ($permissions as $perm) {
    echo "Permission ID: {$perm->id}, Name: {$perm->name}, Guard: {$perm->guard_name}\n";
}

echo "\n";
$roles = DB::table('roles')->select('id', 'name', 'guard_name')->get();
foreach ($roles as $role) {
    echo "Role ID: {$role->id}, Name: {$role->name}, Guard: {$role->guard_name}\n";
}

echo "\nDone!\n";
