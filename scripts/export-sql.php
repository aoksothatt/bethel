<?php
require __DIR__ . '/../backend/vendor/autoload.php';
$app = require_once __DIR__ . '/../backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Player;
use App\Models\User;

$sql = "-- =========================================================\n";
$sql .= "-- BETHEL HIGH SCHOOL — TEAM JERSEY TRACKER DATABASE DUMP\n";
$sql .= "-- Compatible with MySQL 8.0, MariaDB, TiDB Cloud Serverless\n";
$sql .= "-- Total Players: 28 | Admin Users: 1\n";
$sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
$sql .= "-- =========================================================\n\n";

$sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
$sql .= "SET NAMES utf8mb4;\n\n";

// 1. Users table
$sql .= "-- ---------------------------------------------------------\n";
$sql .= "-- Table structure for table `users`\n";
$sql .= "-- ---------------------------------------------------------\n";
$sql .= "DROP TABLE IF EXISTS `users`;\n";
$sql .= "CREATE TABLE `users` (\n";
$sql .= "  `id` bigint unsigned NOT NULL AUTO_INCREMENT,\n";
$sql .= "  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,\n";
$sql .= "  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,\n";
$sql .= "  `email_verified_at` timestamp NULL DEFAULT NULL,\n";
$sql .= "  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,\n";
$sql .= "  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,\n";
$sql .= "  `created_at` timestamp NULL DEFAULT NULL,\n";
$sql .= "  `updated_at` timestamp NULL DEFAULT NULL,\n";
$sql .= "  PRIMARY KEY (`id`),\n";
$sql .= "  UNIQUE KEY `users_email_unique` (`email`)\n";
$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

// Insert admin user
$admin = User::first();
if ($admin) {
    $sql .= "INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`) VALUES\n";
    $sql .= sprintf("(1, %s, %s, %s, NOW(), NOW());\n\n",
        var_export($admin->name, true),
        var_export($admin->email, true),
        var_export($admin->password, true)
    );
}

// 2. Personal Access Tokens (Sanctum)
$sql .= "-- ---------------------------------------------------------\n";
$sql .= "-- Table structure for table `personal_access_tokens`\n";
$sql .= "-- ---------------------------------------------------------\n";
$sql .= "DROP TABLE IF EXISTS `personal_access_tokens`;\n";
$sql .= "CREATE TABLE `personal_access_tokens` (\n";
$sql .= "  `id` bigint unsigned NOT NULL AUTO_INCREMENT,\n";
$sql .= "  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,\n";
$sql .= "  `tokenable_id` bigint unsigned NOT NULL,\n";
$sql .= "  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,\n";
$sql .= "  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,\n";
$sql .= "  `abilities` text COLLATE utf8mb4_unicode_ci,\n";
$sql .= "  `last_used_at` timestamp NULL DEFAULT NULL,\n";
$sql .= "  `expires_at` timestamp NULL DEFAULT NULL,\n";
$sql .= "  `created_at` timestamp NULL DEFAULT NULL,\n";
$sql .= "  `updated_at` timestamp NULL DEFAULT NULL,\n";
$sql .= "  PRIMARY KEY (`id`),\n";
$sql .= "  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),\n";
$sql .= "  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)\n";
$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

// 3. Players table
$sql .= "-- ---------------------------------------------------------\n";
$sql .= "-- Table structure for table `players`\n";
$sql .= "-- ---------------------------------------------------------\n";
$sql .= "DROP TABLE IF EXISTS `players`;\n";
$sql .= "CREATE TABLE `players` (\n";
$sql .= "  `id` bigint unsigned NOT NULL AUTO_INCREMENT,\n";
$sql .= "  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,\n";
$sql .= "  `shirt_number` int NOT NULL,\n";
$sql .= "  `jersey_size` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,\n";
$sql .= "  `payment_status` enum('paid','unpaid','not_confirmed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',\n";
$sql .= "  `notes` text COLLATE utf8mb4_unicode_ci,\n";
$sql .= "  `created_at` timestamp NULL DEFAULT NULL,\n";
$sql .= "  `updated_at` timestamp NULL DEFAULT NULL,\n";
$sql .= "  PRIMARY KEY (`id`),\n";
$sql .= "  KEY `players_payment_status_index` (`payment_status`),\n";
$sql .= "  KEY `players_shirt_number_index` (`shirt_number`)\n";
$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

// Insert 28 players
$players = Player::orderBy('id')->get();
$sql .= "INSERT INTO `players` (`id`, `name`, `shirt_number`, `jersey_size`, `payment_status`, `notes`, `created_at`, `updated_at`) VALUES\n";
$rows = [];
foreach ($players as $p) {
    $rows[] = sprintf("(%d, %s, %d, %s, %s, %s, NOW(), NOW())",
        $p->id,
        var_export($p->name, true),
        $p->shirt_number,
        var_export($p->jersey_size, true),
        var_export($p->payment_status, true),
        $p->notes !== null ? var_export($p->notes, true) : 'NULL'
    );
}
$sql .= implode(",\n", $rows) . ";\n\n";

$sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

$outputPath = __DIR__ . '/../backend/database/bethel-dump.sql';
file_put_contents($outputPath, $sql);
echo "✓ Successfully exported MySQL dump to: {$outputPath} (" . strlen($sql) . " bytes)\n";
