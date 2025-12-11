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
        Schema::create('pincodes', function (Blueprint $table) {
            $table->integer('id', false, true)->autoIncrement();
            $table->string('pincode', 10);
            $table->integer('city_id')->unsigned();
            $table->tinyInteger('status')
                ->default(1)
                ->comment('0: Inactive, 1: Active');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            // Index
            $table->index('city_id', 'city_id');
        });
        // Foreign key
        Schema::table('pincodes', function (Blueprint $table) {
            $table->foreign('city_id', 'pincodes_ibfk_1')
                ->references('id')->on('cities')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pincodes', function (Blueprint $table) {
            $table->dropForeign('pincodes_ibfk_1');
        });

        Schema::dropIfExists('pincodes');
    }
};
