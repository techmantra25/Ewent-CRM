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
        Schema::create('organization_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('invoice_id');
            $table->integer('total_day');
            $table->decimal('total_price', 10, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            // Indexes
            $table->index('user_id', 'user_id_with_invoice_item');
            $table->index('invoice_id', 'invoice_id_with_invoice_item');
            // Foreign Keys
            $table->foreign('invoice_id', 'invoice_id_with_invoice_item')
                  ->references('id')->on('organization_invoices')
                  ->onDelete('cascade');
            $table->foreign('user_id', 'user_id_with_invoice_item')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_invoice_items');
    }
};
