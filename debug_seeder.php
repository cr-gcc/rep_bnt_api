<?php

use App\Models\Status;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$statuses = [
    ['name' => 'Pendientes', 'code' => 0, 'slug' => 'pen'],
    ['name' => 'Aprobadas', 'code' => 1, 'slug' => 'ap'],
    ['name' => 'Rechazadas', 'code' => 2, 'slug' => 'nap'],
    ['name' => 'Hold', 'code' => 3, 'slug' => 'hold'],
    ['name' => 'Aprovadas AOP', 'code' => 4, 'slug' => 'aop'],
    ['name' => 'Recuperadas', 'code' => 5, 'slug' => 'rec'],
];

try {
    echo "Seeding statuses...\n";
    foreach ($statuses as $status) {
        Status::updateOrCreate(
            ['name' => $status['name']],
            [
                'code' => $status['code'],
                'slug' => $status['slug'],
                'active' => true
            ]
        );
        echo "Processed: " . $status['name'] . "\n";
    }
    echo "Done. Total count: " . Status::count() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
