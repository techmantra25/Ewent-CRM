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
        DB::statement("
            ALTER TABLE organization_payments 
            MODIFY invoice_type 
            ENUM('weekly','monthly','manual','custom') 
            NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE organization_payments 
            MODIFY invoice_type 
            ENUM('weekly','monthly','manual') 
            NOT NULL
        ");
    }
};
