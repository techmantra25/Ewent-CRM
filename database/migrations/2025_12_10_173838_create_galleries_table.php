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
        Schema::create('galleries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->string('image');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            // Indexes
            $table->index('product_id', 'galleries_product_id_foreign');
            // Foreign key
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
