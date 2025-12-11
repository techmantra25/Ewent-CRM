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
        Schema::create('admin_ratings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('admin_id');
            $table->decimal('rating', 2, 1);
            $table->text('comments')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            // Index
            $table->index('admin_id', 'admin_ratings_admin_id_foreign');
            // Foreign key
            $table->foreign('admin_id', 'admin_ratings_admin_id_foreign')
                ->references('id')
                ->on('admins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_ratings', function (Blueprint $table) {
            $table->dropForeign('admin_ratings_admin_id_foreign');
            $table->dropIndex('admin_ratings_admin_id_foreign');
        });

        Schema::dropIfExists('admin_ratings');
    }
};
