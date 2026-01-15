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
        Schema::create('cancel_request_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('request_date');
            $table->unsignedBigInteger('vehicle_id');
            $table->dateTime('accepted_date')->nullable();
            $table->unsignedBigInteger('accepted_by')->nullable();
            $table->enum('type', ['accepted', 'rejected']);
            $table->text('rejected_reason')->nullable();
            // Match SQL default timestamps exactly
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancel_request_histories');
    }
};
