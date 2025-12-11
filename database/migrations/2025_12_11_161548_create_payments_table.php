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
         Schema::create('payments', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('order_type')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', [
                'pending', 'completed', 'failed', 'refunded',
                'authorized', 'captured', 'created', 'processing'
            ])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->text('razorpay_order_id')->nullable();
            $table->text('razorpay_payment_id')->nullable();
            $table->text('razorpay_signature')->nullable();
            $table->decimal('amount', 10, 2);
            $table->char('currency', 10)->default('INR');
            $table->string('icici_merchantTxnNo')->nullable();
            $table->string('icici_txnID')->nullable();
            $table->dateTime('payment_date')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexes
            $table->index('order_id', 'payments_order_id_foreign');
        });

        // Foreign Key
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('order_id', 'payments_order_id_foreign')
                ->references('id')->on('orders')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_order_id_foreign');
        });

        Schema::dropIfExists('payments');
    }
};
