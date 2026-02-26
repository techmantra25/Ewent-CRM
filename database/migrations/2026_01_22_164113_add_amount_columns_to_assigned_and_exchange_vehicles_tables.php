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
        // assigned_vehicles table
        Schema::table('assigned_vehicles', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)
                  ->default(0.00)
                  ->after('status');

            $table->decimal('deposit_amount', 10, 2)
                  ->default(0.00)
                  ->after('amount');

            $table->decimal('rental_amount', 10, 2)
                  ->default(0.00)
                  ->after('deposit_amount');
        });

        // exchange_vehicles table
        Schema::table('exchange_vehicles', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)
                  ->default(0.00)
                  ->after('status');

            $table->decimal('deposit_amount', 10, 2)
                  ->default(0.00)
                  ->after('amount');

            $table->decimal('rental_amount', 10, 2)
                  ->default(0.00)
                  ->after('deposit_amount');
        });
    }

    public function down(): void
    {
        Schema::table('assigned_vehicles', function (Blueprint $table) {
            $table->dropColumn(['amount', 'deposit_amount', 'rental_amount']);
        });

        Schema::table('exchange_vehicles', function (Blueprint $table) {
            $table->dropColumn(['amount', 'deposit_amount', 'rental_amount']);
        });
    }
};
