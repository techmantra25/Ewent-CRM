<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rental_prices', function (Blueprint $table) {
             $table->enum('customer_type', ['B2B', 'B2C'])
                  ->default('B2C')
                  ->after('subscription_type');
            $table->double('deposit_amount', 10, 2)
                  ->default(0)
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_prices', function (Blueprint $table) {
            $table->dropColumn('customer_type');
            $table->double('deposit_amount', 10, 2)
                  ->default(null)
                  ->change();
        });
    }
};
