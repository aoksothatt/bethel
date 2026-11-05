<?php
require __DIR__ . '/../backend/vendor/autoload.php';
$app = require_once __DIR__ . '/../backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Player;

$players = Player::all();
echo "TOTAL: " . $players->count() . PHP_EOL;
$stats = [
    'paid' => 0,
    'unpaid' => 0,
    'not_confirmed' => 0,
    'sizes' => ['M' => 0, 'L' => 0, 'XL' => 0]
];

foreach ($players as $p) {
    echo sprintf("[%02d] #%-3s | %-16s | Size: %-2s | Status: %-13s | Notes: %s\n",
        $p->id,
        $p->shirt_number,
        $p->name,
        $p->jersey_size,
        $p->payment_status,
        $p->notes ?? '-'
    );
    if ($p->payment_status === 'paid') $stats['paid']++;
    elseif ($p->payment_status === 'unpaid') $stats['unpaid']++;
    else $stats['not_confirmed']++;
    if (isset($stats['sizes'][$p->jersey_size])) {
        $stats['sizes'][$p->jersey_size]++;
    }
}

echo "\n--- STATS ---\n";
print_r($stats);
