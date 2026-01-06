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
        Schema::create('organization_deposit_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('deposit_invoice_id');

            $table->string('invoice_type')->default('Deposit');
            $table->string('payment_method')->nullable(); // card, netbanking, upi
            $table->string('payment_status')->default('pending'); // pending, success, failed
            $table->string('transaction_id')->nullable();

            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('INR');

            $table->string('icici_merchantTxnNo')->nullable();
            $table->string('icici_txnID')->nullable();

            $table->timestamp('payment_date')->nullable();

            $table->timestamps();
            // Optional Foreign Key
            $table->foreign('organization_id')
                  ->references('id')
                  ->on('organizations')
                  ->onDelete('cascade');
            // Optional Foreign Key
            $table->foreign('deposit_invoice_id')
                  ->references('id')
                  ->on('organization_deposit_invoices')
                  ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_deposit_payments');
    }
};
