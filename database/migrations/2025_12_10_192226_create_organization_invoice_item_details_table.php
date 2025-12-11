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
        Schema::create('organization_invoice_item_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_item_id');
            $table->unsignedBigInteger('order_id');
            $table->date('date');
            $table->decimal('day_amount', 10, 2)->default(0.00);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            // Indexes
            $table->index('invoice_item_id', 'invoice_item_with_item_details');
            $table->index('order_id', 'orders_with_item_details');
            // Foreign Keys
            $table->foreign('invoice_item_id', 'invoice_item_with_item_details')
                  ->references('id')
                  ->on('organization_invoice_items')
                  ->onDelete('cascade');
            $table->foreign('order_id', 'orders_with_item_details')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_invoice_item_details');
    }
};
