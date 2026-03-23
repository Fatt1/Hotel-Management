<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('bookings', 'checkin_date')) {
            Schema::table('bookings', function (Blueprint $table): void {
                $table->dateTime('checkin_date')->nullable()->after('booking_date');
            });
        }

        if (!Schema::hasColumn('bookings', 'checkout_date')) {
            Schema::table('bookings', function (Blueprint $table): void {
                $table->dateTime('checkout_date')->nullable()->after('checkin_date');
            });
        }

        if (!Schema::hasColumn('booking_details', 'room_amount')) {
            Schema::table('booking_details', function (Blueprint $table): void {
                $table->decimal('room_amount', 12, 2)->default(0)->after('daily_price');
            });
        }

        if (Schema::hasColumn('bookings', 'checkin_date') && Schema::hasColumn('bookings', 'checkout_date')) {
            DB::statement(
                "UPDATE bookings b
                 JOIN (
                    SELECT booking_id, MIN(checkin_date) AS min_checkin, MAX(checkout_date) AS max_checkout
                    FROM booking_details
                    GROUP BY booking_id
                 ) d ON d.booking_id = b.id
                 SET b.checkin_date = COALESCE(b.checkin_date, d.min_checkin),
                     b.checkout_date = COALESCE(b.checkout_date, d.max_checkout)"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('bookings', 'checkout_date')) {
            Schema::table('bookings', function (Blueprint $table): void {
                $table->dropColumn('checkout_date');
            });
        }

        if (Schema::hasColumn('bookings', 'checkin_date')) {
            Schema::table('bookings', function (Blueprint $table): void {
                $table->dropColumn('checkin_date');
            });
        }

        if (Schema::hasColumn('booking_details', 'room_amount')) {
            Schema::table('booking_details', function (Blueprint $table): void {
                $table->dropColumn('room_amount');
            });
        }
    }
};
