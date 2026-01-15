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
        Schema::create('organization_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('invoice_id');
            $table->enum('invoice_type', ['weekly', 'monthly', 'manual'])
                  ->comment('Type of invoice');
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'success', 'failed', 'refunded', 'initiated'])
                  ->default('initiated')->nullable();
            $table->string('transaction_id', 100)->nullable()
                  ->comment('System internal transaction id');
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('INR')->nullable();
            $table->string('icici_merchantTxnNo', 100)->nullable()
                  ->comment('ICICI generated merchant transaction no');
            $table->string('icici_txnID', 100)->nullable()
                  ->comment('ICICI Bank transaction id');
            $table->timestamp('payment_date')
                  ->nullable()
                  ->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')
                  ->nullable()
                  ->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')
                  ->nullable()
                  ->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            // Indexes
            $table->index('invoice_id');
            $table->index('transaction_id');
            $table->index('icici_merchantTxnNo');
            $table->index('icici_txnID');
            $table->index('organization_id', 'organization_payments_organization_id_foreign');

            // Foreign Keys
            $table->foreign('invoice_id', 'organization_payments_invoice_id_foreign')
                  ->references('id')
                  ->on('organization_invoices')
                  ->onDelete('cascade');
            $table->foreign('organization_id', 'organization_payments_organization_id_foreign')
                  ->references('id')
                  ->on('organizations')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_payments');
    }
};
