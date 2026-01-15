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
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->string('title');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            // Index
            $table->index('category_id', 'sub_categories_category_id_foreign');
            // Foreign Key
            $table->foreign('category_id', 'sub_categories_category_id_foreign')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropForeign('sub_categories_category_id_foreign');
            $table->dropIndex('sub_categories_category_id_foreign');
        });
        Schema::dropIfExists('sub_categories');
    }
};
