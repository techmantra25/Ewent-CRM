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
        Schema::create('user_kyc_logs', function (Blueprint $table) {
            $table->integer('id', false, true)->autoIncrement();
            $table->unsignedBigInteger('user_id');
            $table->enum('document_type', [
                'Driving Licence',
                'Aadhar Card',
                'Pan Card',
                'Current Address Proof',
                'Passbook',
                'Profile Image'
            ])->nullable();
            $table->enum('status', ['Uploaded', 'Verified', 'Rejected', 'Re-uploaded']);
            $table->text('remarks')->nullable();
            $table->text('message')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_kyc_logs');
    }
};
