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
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id'); 
            $table->string('name');
            $table->integer('state_id')->unsigned();
            $table->string('country');
            $table->tinyInteger('status')
                ->default(1)
                ->comment('0: Inactive, 1: Active');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();
            $table->index('state_id');
            // Foreign key constraint
            $table
                ->foreign('state_id')
                ->references('id')
                ->on('states')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
