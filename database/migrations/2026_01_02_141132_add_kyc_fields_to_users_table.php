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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('org_kyc_verified_at')->nullable()->after('rejected_by');
            $table->unsignedBigInteger('org_kyc_verified_by')->nullable()->after('org_kyc_verified_at');
            $table->enum('org_is_verified', ['unverified', 'verified', 'rejected'])
            ->default('unverified')
            ->after('org_kyc_verified_by');
            $table->timestamp('org_date_of_rejection')->nullable()->after('org_is_verified');
            $table->unsignedBigInteger('org_rejected_by')->nullable()->after('org_date_of_rejection');

            // Optional: foreign keys (recommended)
            $table->foreign('org_kyc_verified_by')->references('id')->on('organizations')->nullOnDelete();
            $table->foreign('org_rejected_by')->references('id')->on('organizations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
