<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {

            // Add renewal_interval_days after renewal_day_of_month
            $table->string('renewal_interval_days')
                  ->nullable()
                  ->after('renewal_day_of_month');
        });

        // Update ENUM to include custom
        DB::statement("
            ALTER TABLE organizations 
            MODIFY subscription_type 
            ENUM('weekly', 'custom', 'monthly') 
            NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('renewal_interval_days');
        });

        // Rollback ENUM
        DB::statement("
            ALTER TABLE organizations 
            MODIFY subscription_type 
            ENUM('weekly', 'monthly') 
            NOT NULL
        ");
    }
};
