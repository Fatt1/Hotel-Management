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
        Schema::create('role_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_name', 255);
            $table->integer('claim_value');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_claims');
    }
};
