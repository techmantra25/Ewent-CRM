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
         Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->integer('id', false, true)->autoIncrement();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->integer('quantity');
            $table->enum('type', ['Credit', 'Debit']);
            $table->enum('purpose', ['Rent', 'Sell', 'New']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            // Indexes
            $table->index('order_id', 'order_id_k1');
            $table->index('product_id', 'product_id_k2');
        });
        // Foreign Key
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->foreign('order_id', 'order_id_k1')
                  ->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->dropForeign('order_id_k1');
        });

        Schema::dropIfExists('stock_ledgers');
    }
};
