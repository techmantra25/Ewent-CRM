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
        Schema::create('bom_parts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('part_name')->nullable();
            $table->string('part_number')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('part_unit', 100)->nullable();
            $table->decimal('part_price', 10, 2)->nullable();
            $table->integer('warranty_in_day')->nullable();
            $table->enum('warranty', ['Yes', 'No'])->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_parts');
    }
};
