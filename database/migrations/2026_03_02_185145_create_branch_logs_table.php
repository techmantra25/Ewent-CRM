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
        Schema::create('branch_logs', function (Blueprint $table) {
            $table->id();

            // Which branch
            $table->foreignId('branch_id')
                  ->constrained('branches')
                  ->cascadeOnDelete();

            // Which admin/user performed action
            $table->foreignId('admin_id')
                  ->nullable()
                  ->constrained('admins')
                  ->nullOnDelete();

            // Action type (create, update, delete, transfer, etc)
            $table->string('action');

            // Model name (Stock, Product, Order etc)
            $table->string('module')->nullable();

            // Record ID of related table
            $table->unsignedBigInteger('reference_id')->nullable();

            // Old & new data (JSON)
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();

            // IP & device info
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_logs');
    }
};
