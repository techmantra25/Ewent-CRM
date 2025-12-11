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
        Schema::create('organizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('organization_id')->nullable()->unique();
            $table->enum('subscription_type', ['weekly', 'monthly'])->default('weekly')->nullable();
            $table->string('renewal_day')->nullable();
            $table->string('renewal_day_of_month')->nullable();
            $table->string('email')->unique();
            $table->string('mobile', 20)->nullable()->unique();
            $table->string('password');
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(1)->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->tinyInteger('discount_is_positive')->default(0)->nullable();
            $table->string('street_address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
