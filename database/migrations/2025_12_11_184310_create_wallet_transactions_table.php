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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->enum('transaction_type', ['credit', 'debit', 'refund']);
            $table->decimal('amount', 10, 2);
            $table->string('description', 255)->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            // Indexes
            $table->index('wallet_id', 'wallet_transactions_wallet_id_foreign');
            $table->index('order_id', 'wallet_transactions_order_id_foreign');
        });

        // Foreign keys
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->foreign('wallet_id', 'wallet_transactions_wallet_id_foreign')
                  ->references('id')->on('wallets')
                  ->onDelete('cascade');

            $table->foreign('order_id', 'wallet_transactions_order_id_foreign')
                  ->references('id')->on('orders')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropForeign('wallet_transactions_wallet_id_foreign');
            $table->dropForeign('wallet_transactions_order_id_foreign');
        });

        Schema::dropIfExists('wallet_transactions');
    }
};
