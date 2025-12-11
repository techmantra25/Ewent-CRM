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
           Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('image');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            // Index
            $table->index('product_id', 'product_images_product_id_foreign');
        });

        // Foreign Key
        Schema::table('product_images', function (Blueprint $table) {
            $table->foreign('product_id', 'product_images_product_id_foreign')
                ->references('id')
                ->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropForeign('product_images_product_id_foreign');
        });

        Schema::dropIfExists('product_images');
    }
};
