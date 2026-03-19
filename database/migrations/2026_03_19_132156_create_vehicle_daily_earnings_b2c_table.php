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
        Schema::create('vehicle_daily_earnings_b2c', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('rider_id');
            $table->unsignedBigInteger('vehicle_id');

            $table->decimal('amount', 10, 2);
            $table->date('date');

            $table->timestamps();

            // Indexes
            $table->index('order_id');
            $table->index('rider_id');
            $table->index('vehicle_id');
            $table->index('date');

            // Foreign Keys
            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('cascade');

            $table->foreign('rider_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('vehicle_id')
                ->references('id')->on('stocks')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_daily_earnings_b2c');
    }
};
