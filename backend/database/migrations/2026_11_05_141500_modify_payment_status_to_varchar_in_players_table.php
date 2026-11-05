<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Safely alters payment_status column from ENUM('paid', 'unpaid', 'not_confirmed')
     * to VARCHAR(30) NOT NULL DEFAULT 'unpaid'.
     *
     * Data Safety Guarantees:
     * - Preserves 100% of existing rows ('paid', 'unpaid', 'not_confirmed').
     * - Does NOT drop or recreate tables.
     * - Supports new 'half_paid' (50% Paid) status without truncation.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `players` MODIFY COLUMN `payment_status` VARCHAR(30) NOT NULL DEFAULT 'unpaid'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `players` MODIFY COLUMN `payment_status` ENUM('paid', 'half_paid', 'unpaid', 'not_confirmed') NOT NULL DEFAULT 'unpaid'");
    }
};
