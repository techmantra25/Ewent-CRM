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
        Schema::create('rental_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->integer('duration');
            $table->enum('subscription_type', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly']);
            $table->double('deposit_amount', 10, 2);
            $table->double('rental_amount', 10, 2)->nullable();
            $table->tinyInteger('status')->default(1)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            // Index
            $table->index('product_id', 'product_id');
        });

        // Foreign key
        Schema::table('rental_prices', function (Blueprint $table) {
            $table->foreign('product_id', 'rental_prices_ibfk_1')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_prices', function (Blueprint $table) {
            $table->dropForeign('rental_prices_ibfk_1');
        });

        Schema::dropIfExists('rental_prices');
    }
};
