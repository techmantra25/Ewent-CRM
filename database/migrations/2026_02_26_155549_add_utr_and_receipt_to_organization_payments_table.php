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
        Schema::table('organization_payments', function (Blueprint $table) {
            $table->string('utr_no', 100)
                  ->nullable()
                  ->after('icici_txnID');

            $table->string('receipt_upload')
                  ->nullable()
                  ->after('utr_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_payments', function (Blueprint $table) {
             $table->dropColumn(['utr_no', 'receipt_upload']);
        });
    }
};
