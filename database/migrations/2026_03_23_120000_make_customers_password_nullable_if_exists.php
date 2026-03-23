<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('customers', 'password')) {
            return;
        }

        DB::statement('ALTER TABLE customers MODIFY password VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('customers', 'password')) {
            return;
        }

        DB::statement("UPDATE customers SET password = '' WHERE password IS NULL");
        DB::statement('ALTER TABLE customers MODIFY password VARCHAR(255) NOT NULL');
    }
};
