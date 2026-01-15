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
        Schema::table('organizations', function (Blueprint $table) {
           // Add rider_visibility_percentage column after status
            $table->decimal('rider_visibility_percentage', 5, 2)
                ->default(0.00)
                ->nullable()
                ->after('status')
                ->comment('Value will always be plus (+) or 0');

            // Remove discount_is_positive column
            $table->dropColumn('discount_is_positive');

            // Update comment on discount_percentage column
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
        Schema::table('organizations', function (Blueprint $table) {
           // Remove rider_visibility_percentage column
            $table->dropColumn('rider_visibility_percentage');

            // Re-add discount_is_positive column (adjust type if needed)
            $table->boolean('discount_is_positive')->default(true);

            // Remove/restore previous comment on discount_percentage
            $table->decimal('discount_percentage', 5, 2)->comment(null)->change();
        });
    }
};
