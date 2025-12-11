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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('product_id');
            $table->string('vehicle_number', 50);
            $table->string('vehicle_track_id', 255)->nullable();
            $table->string('imei_number', 250)->nullable();
            $table->string('chassis_number', 250)->nullable();
            $table->string('friendly_name', 250)->nullable();
            $table->tinyInteger('status')
                ->default(1)
                ->comment('0: Inactive, 1: Active');
            $table->enum('immobilizer_status', ['MOBILIZE', 'IMMOBILIZE'])
                ->default('MOBILIZE');
            $table->string('immobilizer_request_id', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            // Index
            $table->index('product_id', 'product_id_k1');
        });

        // Foreign Key
        Schema::table('stocks', function (Blueprint $table) {
            $table->foreign('product_id', 'product_id_k1')
                ->references('id')->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign('product_id_k1');
        });
        
        Schema::dropIfExists('stocks');
    }
};
