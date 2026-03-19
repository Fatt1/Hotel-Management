<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `maintenance_tickets` MODIFY `repair_cost` DECIMAL(30,2) NOT NULL DEFAULT 0.00");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `maintenance_tickets` MODIFY `repair_cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00");
    }
};
