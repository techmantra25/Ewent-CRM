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
        Schema::create('payment_items', function (Blueprint $table) {
            $table->integer('id', false, true)->autoIncrement(); 
            $table->string('payment_for')->nullable();
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('duration')->nullable()->comment('in days');
            $table->enum('type', ['deposit', 'rental'])->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->useCurrent();
            // Indexes
            $table->index('payment_id', 'payment_payment_id_foreign');
            $table->index('vehicle_id', 'stock_vehicle_id_foreign');
            $table->index('product_id', 'model_product_id_foreign');
        });

        // Foreign Keys
        Schema::table('payment_items', function (Blueprint $table) {
            $table->foreign('product_id', 'model_product_id_foreign')
                ->references('id')->on('products')
                ->onDelete('cascade');

            $table->foreign('payment_id', 'payment_payment_id_foreign')
                ->references('id')->on('payments')
                ->onDelete('cascade');

            $table->foreign('vehicle_id', 'stock_vehicle_id_foreign')
                ->references('id')->on('stocks')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_items', function (Blueprint $table) {
            $table->dropForeign('model_product_id_foreign');
            $table->dropForeign('payment_payment_id_foreign');
            $table->dropForeign('stock_vehicle_id_foreign');
        });

        Schema::dropIfExists('payment_items');
    }
};
