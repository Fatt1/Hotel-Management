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
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->dateTime('checkin_date');
            $table->dateTime('checkout_date');
            $table->boolean('checkout_status')->default(false);
            $table->decimal('hourly_price', 10, 2);
            $table->decimal('daily_price', 10, 2);
            $table->decimal('room_amount', 12, 2)->default(0);
            $table->decimal('service_amount', 12, 2)->default(0);
            $table->decimal('surcharge_amount', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
