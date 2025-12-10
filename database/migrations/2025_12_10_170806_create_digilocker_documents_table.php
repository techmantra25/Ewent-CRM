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
        Schema::create('digilocker_documents', function (Blueprint $table) {
            $table->bigIncrements('id'); 
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('request_id');
            $table->string('webhook_security_key')->nullable();
            $table->string('request_timestamp')->nullable();
            $table->string('sdk_url')->nullable();
            $table->tinyInteger('success')->default(0)->nullable();
            $table->string('response_code', 50)->nullable();
            $table->string('response_message')->nullable();
            $table->enum('billable', ['Y','N'])->default('N')->nullable();
            $table->string('document_type', 50)->nullable();
            $table->string('document_name')->nullable();
            $table->string('document_status', 50)->nullable();
            $table->string('fetched_at')->nullable();
            $table->string('issuer')->nullable();
            $table->string('issuer_id', 100)->nullable();
            $table->string('issue_date')->nullable();
            $table->text('document_uri')->nullable();
            $table->longText('mime_types')->charset('utf8mb4')->collation('utf8mb4_bin')->nullable();
            $table->longText('raw_xml')->nullable();
            $table->string('kyc_code', 100)->nullable();
            $table->string('kyc_response_status', 10)->nullable();
            $table->string('kyc_timestamp')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            // Indexes
            $table->index('request_id', 'idx_request_id');
            $table->index('document_type', 'idx_document_type');
            $table->index('issuer_id', 'idx_issuer_id');
            $table->index('fetched_at', 'idx_fetched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digilocker_documents');
    }
};
