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
        Schema::create('org_deposit_invoice_merchant_numbers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('deposit_invoice_id');

            $table->string('merchantTxnNo')->unique();
            $table->string('redirect_url')->nullable();
            $table->text('secureHash')->nullable();
            $table->text('tranCtx')->nullable();

            $table->decimal('amount', 12, 2);

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
        Schema::dropIfExists('org_deposit_invoice_merchant_numbers');
    }
};
