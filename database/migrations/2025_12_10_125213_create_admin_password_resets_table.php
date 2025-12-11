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
        Schema::create('admin_password_resets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mobile', 15);
            $table->char('otp', 6);
            $table->dateTime('expires_at');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            // Indexes
            $table->index('mobile', 'idx_admin_pw_resets_mobile');
            $table->index(['mobile', 'otp'], 'idx_admin_pw_resets_mobile_otp');
            $table->index('expires_at', 'idx_admin_pw_resets_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_password_resets', function (Blueprint $table) {
            $table->dropIndex('idx_admin_pw_resets_mobile');
            $table->dropIndex('idx_admin_pw_resets_mobile_otp');
            $table->dropIndex('idx_admin_pw_resets_expires_at');
        });
        
        Schema::dropIfExists('admin_password_resets');

    }
};
