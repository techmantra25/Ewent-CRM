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
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('designation')->nullable();
            $table->string('image')->nullable();
            $table->string('country_code', 20)->default('+91');
            $table->string('mobile')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            // Index
            $table->index('designation', 'designation_dk_1');
            // Foreign key
            $table->foreign('designation', 'designation_dk_1')
                ->references('id')
                ->on('designations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign('designation_dk_1');
            $table->dropIndex('designation_dk_1');
        });
        Schema::dropIfExists('admins');
    }
};
