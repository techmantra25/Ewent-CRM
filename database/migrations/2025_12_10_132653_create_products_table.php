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
        Schema::create('products', function (Blueprint $table) {
             $table->bigIncrements('id');

        $table->integer('stock_qty')->default(0);
        $table->tinyInteger('stock')->default(1)->comment('1: Stock in, 0:Stock Out');

        $table->string('title');
        $table->string('product_sku')->nullable();
        $table->integer('position')->nullable();
        $table->string('types')->nullable();

        $table->text('short_desc')->nullable();
        $table->longText('long_desc')->nullable();

        $table->unsignedBigInteger('category_id')->nullable();
        $table->unsignedBigInteger('sub_category_id')->nullable();

        $table->string('image')->nullable();

        $table->tinyInteger('is_selling')->default(0)->comment('0:inactive, 1:active');
        $table->decimal('base_price', 10, 2)->nullable();
        $table->decimal('display_price', 10, 2)->nullable();
        $table->tinyInteger('is_rent')->default(0)->comment('0:inactive, 1:active');

        $table->string('meta_title')->nullable();
        $table->text('meta_description')->nullable();
        $table->text('meta_keyword')->nullable();

        $table->tinyInteger('status')->default(1);
        $table->tinyInteger('is_driving_licence_required')->default(1)->comment('1:Yes, 0:No');

        $table->tinyInteger('is_bestseller')->default(0);
        $table->tinyInteger('is_new_arrival')->default(1);
        $table->tinyInteger('is_featured')->default(0);

        $table->timestamp('deleted_at')->nullable();
        $table->timestamp('created_at')->nullable();
        $table->timestamp('updated_at')->nullable();

        // Indexes
        $table->index('category_id', 'products_category_id_foreign');
        $table->index('sub_category_id', 'products_sub_category_id_foreign');

        // Foreign Keys
        $table->foreign('category_id', 'products_category_id_foreign')
              ->references('id')
              ->on('categories')
              ->onDelete('cascade');

        $table->foreign('sub_category_id', 'products_sub_category_id_foreign')
              ->references('id')
              ->on('sub_categories')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_category_id_foreign');
            $table->dropForeign('products_sub_category_id_foreign');

            $table->dropIndex('products_category_id_foreign');
            $table->dropIndex('products_sub_category_id_foreign');
        });
        Schema::dropIfExists('products');
    }
};
 