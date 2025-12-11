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
        Schema::create('org_invoice_merchant_numbers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('merchantTxnNo', 100);
            $table->text('redirect_url');
            $table->string('secureHash', 255);
            $table->string('tranCtx', 255)->nullable();
            $table->decimal('amount', 12, 2);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->unique('merchantTxnNo', 'uniq_merchantTxnNo');
            $table->index('amount','amount');
            $table->index('organization_id', 'org_id_from_organizations');
            $table->index('invoice_id', 'invoice_id_from_organizations');
        });

        // Foreign Keys
        Schema::table('org_invoice_merchant_numbers', function (Blueprint $table) {
            $table->foreign('organization_id', 'org_id_from_organizations')
                ->references('id')->on('organizations')
                ->onDelete('cascade');

            $table->foreign('invoice_id', 'invoice_id_from_organizations')
                ->references('id')->on('organization_invoices')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('org_invoice_merchant_numbers', function (Blueprint $table) {
            $table->dropForeign('org_id_from_organizations');
            $table->dropForeign('invoice_id_from_organizations');
        });

        Schema::dropIfExists('org_invoice_merchant_numbers');
    }
};
