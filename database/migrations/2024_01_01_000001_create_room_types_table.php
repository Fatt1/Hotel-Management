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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 100);
            $table->unsignedInteger('adult_quantity');
            $table->unsignedInteger('child_quantity');
            $table->unsignedInteger('single_bed_quantity');
            $table->unsignedInteger('double_bed_quantity');
            $table->string("description", 200)->nullable();
            $table->decimal('width', 8, 2);
            $table->decimal('height', 8, 2);
            $table->decimal('hourly_price', 10, 2);
            $table->decimal('daily_price', 10, 2);
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
