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
        Schema::create('assigned_vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->enum('status', [
                'assigned',
                'returned',
                'cancelled',
                'sold',
                'deallocated',
                'overdue'
            ])->default('assigned');
            $table->timestamp('assigned_at')->useCurrent();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamp('deallocated_at')->nullable();
            $table->unsignedBigInteger('deallocated_by')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            // Indexes
            $table->index('order_id', 'order_id_vehicle_k1');
            $table->index('assigned_by', 'admin_item_id_k1');
            $table->index('deallocated_by', 'deallocated_id_k1');
            // Foreign Keys
            $table->foreign('order_id', 'order_id_vehicle_k1')
                ->references('id')->on('orders')
                ->onDelete('cascade');
            $table->foreign('assigned_by', 'admin_item_id_k1')
                ->references('id')->on('admins')
                ->onDelete('cascade');
            $table->foreign('deallocated_by', 'deallocated_id_k1')
                ->references('id')->on('admins')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigned_vehicles');
    }
};
