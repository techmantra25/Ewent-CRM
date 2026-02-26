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
        // Update ENUM to include custom
        DB::statement("
            ALTER TABLE organization_invoices 
            MODIFY type 
            ENUM('weekly', 'custom', 'monthly') 
            NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback ENUM
        DB::statement("
            ALTER TABLE organization_invoices 
            MODIFY type 
            ENUM('weekly', 'monthly') 
            NOT NULL
        ");
    }
};
