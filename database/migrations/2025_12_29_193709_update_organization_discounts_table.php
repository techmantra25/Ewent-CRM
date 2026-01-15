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
        Schema::table('organization_discounts', function (Blueprint $table) {

            // Remove discount_is_positive column
            if (Schema::hasColumn('organization_discounts', 'discount_is_positive')) {
                $table->dropColumn('discount_is_positive');
            }

            // Add comment to discount_percentage column
            $table->decimal('discount_percentage', 5, 2)
                ->comment('Value will always be minus (-) or 0')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_discounts', function (Blueprint $table) {

            // Re-add discount_is_positive column
            $table->boolean('discount_is_positive')->default(true);

            // Remove comment from discount_percentage column
            $table->decimal('discount_percentage', 5, 2)
                  ->comment(null)
                  ->change();
        });
    }
};
