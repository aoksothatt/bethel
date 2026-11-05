<?php
require __DIR__ . '/../backend/vendor/autoload.php';
$app = require_once __DIR__ . '/../backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Player;

// Remove any test player records with id > 28
Player::where('id', '>', 28)->delete();

// Ensure original record for PHOUNG is not_confirmed
Player::where('name', 'PHOUNG')->update(['payment_status' => 'not_confirmed']);

echo "Current Total Players: " . Player::count() . PHP_EOL;
$stats = [
    'paid' => Player::where('payment_status', 'paid')->count(),
    'unpaid' => Player::where('payment_status', 'unpaid')->count(),
    'not_confirmed' => Player::where('payment_status', 'not_confirmed')->count(),
    'size_M' => Player::where('jersey_size', 'M')->count(),
    'size_L' => Player::where('jersey_size', 'L')->count(),
    'size_XL' => Player::where('jersey_size', 'XL')->count(),
];
print_r($stats);
