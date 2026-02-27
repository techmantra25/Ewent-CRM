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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('branch_code')->unique();
            $table->text('address')->nullable();

            // Foreign key
            $table->unsignedInteger('city_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            
            $table->foreign('city_id')
                  ->references('id')
                  ->on('cities')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
