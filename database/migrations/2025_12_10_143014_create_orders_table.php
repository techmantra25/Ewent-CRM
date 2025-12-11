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
         Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->enum('user_type', ['B2B', 'B2C'])->default('B2C')->comment('User placed the order as B2B or B2C');
            $table->string('subscription_type')->nullable();
            $table->enum('order_type', ['Rent', 'Sell']);
            $table->string('order_number');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->decimal('deposit_amount', 10, 2)->default(0.00);
            $table->decimal('rental_amount', 10, 2)->default(0.00);
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('final_amount', 10, 2)->default(0.00);
            $table->unsignedInteger('quantity');
            $table->enum('payment_mode', ['Online', 'Offline'])->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('shipping_address')->nullable();
            $table->unsignedInteger('rent_duration')->nullable()->comment('in days');
            $table->dateTime('rent_start_date')->nullable();
            $table->dateTime('rent_end_date')->nullable();
            $table->dateTime('return_date')->nullable();
            $table->enum('rent_status', [
                'pending','active','inactive','returned','ready to assign','cancelled','suspended','deallocated'
            ])->default('pending')->nullable();
            $table->enum('cancel_request', ['Yes', 'No'])->default('No');
            $table->dateTime('cancel_request_at')->nullable();
            $table->timestamps();
        });

        // Indexes & Foreign Keys
        Schema::table('orders', function (Blueprint $table) {
            $table->index('product_id', 'order_product_id_k1');
            $table->index('user_id', 'order_user_id_k1');

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);
        });
        
        Schema::dropIfExists('orders');
    }
};
