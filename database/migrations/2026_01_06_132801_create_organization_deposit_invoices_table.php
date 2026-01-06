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
        Schema::create('organization_deposit_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('organization_id');

            $table->string('invoice_number')->unique();

            $table->enum('type', ['Deposit'])->default('Deposit');

            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');

            $table->integer('number_of_vehicle')->default(0);

            $table->decimal('vehicle_price_per_piece', 10, 2)->default(0);

            $table->decimal('total_amount', 12, 2)->default(0);

            $table->date('payment_date')->nullable();

            $table->timestamps();

            // Optional Foreign Key
            $table->foreign('organization_id')
                  ->references('id')
                  ->on('organizations')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_deposit_invoices');
    }
};
