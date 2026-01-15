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
        Schema::create('shipping_activities', function (Blueprint $table) {
            $table->integer('id', false, true)->autoIncrement();
            $table->unsignedBigInteger('order_id');
            $table->enum('status', [
                'Ride Booked',
                'Payment Received',
                'Ride Canceled',
                'Vehicle Assigned',
                'Ride Started',
                'Ride Completed'
            ]);
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('payment_status', ['Pending', 'Paid', 'Refunded'])->default('Pending')->nullable();
            $table->string('description', 255)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_activities');
    }
};
