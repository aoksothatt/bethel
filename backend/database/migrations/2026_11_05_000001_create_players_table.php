<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('shirt_number'); // Duplicate allowed, 0 allowed
            $table->enum('jersey_size', ['M', 'L', 'XL']);
            $table->enum('payment_status', ['paid', 'unpaid', 'not_confirmed'])->default('unpaid');
            $table->text('notes')->nullable(); // Supports Khmer notes & GK
            $table->timestamps();

            // Indexes for fast querying & filtering
            $table->index('name');
            $table->index('shirt_number');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
