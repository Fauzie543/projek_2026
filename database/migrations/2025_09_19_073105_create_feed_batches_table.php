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
        Schema::create('feed_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('feed_recipes')->cascadeOnDelete();
            $table->timestamp('produced_at')->nullable();
            $table->decimal('qty_kg', 12, 2);
            $table->decimal('cost_total', 14, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feed_batches');
    }
};