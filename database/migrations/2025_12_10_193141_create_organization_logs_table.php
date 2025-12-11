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
        Schema::create('organization_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->enum('trigger_type', ['create', 'update', 'delete'])->default('update');
            $table->longText('old_data')->nullable()
                  ->check('json_valid(`old_data`)');
            $table->longText('new_data')->nullable()
                  ->check('json_valid(`new_data`)');
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            // Indexes
            $table->index('organization_id', 'organization_logs_organization_id_foreign');
            $table->index('updated_by', 'organization_logs_admin_id_foreign');
            // Foreign Keys
            $table->foreign('organization_id', 'organization_logs_organization_id_foreign')
                  ->references('id')
                  ->on('organizations')
                  ->onDelete('cascade');
            $table->foreign('updated_by', 'organization_logs_admin_id_foreign')
                  ->references('id')
                  ->on('admins')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_logs');
    }
};
