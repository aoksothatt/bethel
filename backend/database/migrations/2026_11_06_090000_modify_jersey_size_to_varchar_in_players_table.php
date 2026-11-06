<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Safely alters jersey_size column from ENUM('M', 'L', 'XL')
     * to VARCHAR(10) NOT NULL DEFAULT 'L'.
     *
     * Cross-Database Compatibility:
     * - SQLite: Uses Laravel Schema::table change() rebuilder to drop the
     *   CHECK ("jersey_size" in ('M', 'L', 'XL')) constraint, converting the column
     *   to VARCHAR(10) NOT NULL DEFAULT 'L' while preserving all existing rows.
     * - MySQL / TiDB: Executes non-destructive MODIFY COLUMN preserving all existing
     *   rows, indexes, and NOT NULL constraints.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('players', function (Blueprint $table) {
                $table->string('jersey_size', 10)->default('L')->change();
            });
        } else {
            DB::statement("ALTER TABLE `players` MODIFY COLUMN `jersey_size` VARCHAR(10) NOT NULL DEFAULT 'L'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * Rollback Limitations:
     * - If records with size 'S' or 'XXL' exist in the database, reversing to ENUM('M', 'L', 'XL')
     *   will be rejected by MySQL/TiDB in strict SQL mode and SQLite CHECK constraints.
     * - Any 'S' or 'XXL' player records must be reassigned to 'M', 'L', or 'XL' prior to executing rollback.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('players', function (Blueprint $table) {
                $table->enum('jersey_size', ['M', 'L', 'XL'])->default('L')->change();
            });
        } else {
            DB::statement("ALTER TABLE `players` MODIFY COLUMN `jersey_size` ENUM('M', 'L', 'XL') NOT NULL DEFAULT 'L'");
        }
    }
};
