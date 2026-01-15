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
        Schema::create('organization_discounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('organization_id');
            $table->decimal('discount_percentage', 5, 2)->default(0.00)->nullable();

            $table->tinyInteger('discount_is_positive')
                  ->default(0)->nullable()
                  ->comment('0 = negative/discount, 1 = positive/markup');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            // Match SQL default timestamp behavior
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->nullable();
            // Index
            $table->index('organization_id', 'fk_org_discount');
            // Foreign Key
            $table->foreign('organization_id')
                ->references('id')->on('organizations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_discounts');
    }
};
