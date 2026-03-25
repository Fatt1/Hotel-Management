<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('booking_reviews');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Review feature was removed; do not recreate dropped table here.
    }
};
