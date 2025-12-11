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
         Schema::create('selling_queries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->text('remarks');
            $table->timestamps(); 
            // Indexes
            $table->index('user_id', 'user_id');
            $table->index('product_id', 'product_id');
        });

        // Foreign keys
        Schema::table('selling_queries', function (Blueprint $table) {
            $table->foreign('user_id', 'selling_queries_ibfk_1')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('product_id', 'selling_queries_ibfk_2')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('selling_queries', function (Blueprint $table) {
            $table->dropForeign('selling_queries_ibfk_1');
            $table->dropForeign('selling_queries_ibfk_2');
        });
        
        Schema::dropIfExists('selling_queries');
    }
};
