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
        Schema::create('damaged_part_logs', function (Blueprint $table) {
            $table->increments('id'); 
            $table->integer('order_item_id');  
            $table->integer('bom_part_id');   
            $table->float('price', 8, 2);  
            $table->integer('log_by');  
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damaged_part_logs');
    }
};
