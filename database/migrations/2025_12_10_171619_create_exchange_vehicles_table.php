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
        Schema::create('exchange_vehicles', function (Blueprint $table) {
            $table->bigIncrements('id'); // bigint AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->unsignedInteger('vehicle_id');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->enum('status', ['exchanged','renewal','returned'])->default('exchanged');
            $table->timestamp('exchanged_at')->useCurrent();
            $table->unsignedBigInteger('exchanged_by')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            // Indexes
            $table->index('vehicle_id', 'fk_vehicle');
            $table->index('order_id', 'order_id_vehicle_k1');
            $table->index('exchanged_by', 'admin_item_id_k1');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_vehicles');
    }
};
