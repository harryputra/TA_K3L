<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FLOOR 2 ===\n";
$floor2 = \App\Models\CampusRoom::query()
    ->where('building_key', 'gedung-teori')
    ->where('is_active', true)
    ->where('floor', 2)
    ->orderBy('sort_order')
    ->get();

foreach ($floor2 as $room) {
    echo "ID: {$room->id}, Name: {$room->name}\n";
}
echo "Total floor 2: " . $floor2->count() . "\n";

echo "\n=== FLOOR 3 ===\n";
$floor3 = \App\Models\CampusRoom::query()
    ->where('building_key', 'gedung-teori')
    ->where('is_active', true)
    ->where('floor', 3)
    ->orderBy('sort_order')
    ->get();

foreach ($floor3 as $room) {
    echo "ID: {$room->id}, Name: {$room->name}\n";
}
echo "Total floor 3: " . $floor3->count() . "\n";

// Check users for reporters
$users = \App\Models\User::query()->limit(3)->get();
echo "\n=== USERS ===\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}\n";
}
