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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clients_id');
            $table->string('invoice_id');
            $table->string('for');
            $table->string('payment_method');
            $table->string('amount');
            $table->string('frequency');
            $table->text('notes')->nullable();
            $table->string('card_amount')->nullable();
            $table->string('processing_fee')->nullable();
            $table->string('payment_link')->nullable();
            $table->boolean('initial_payment')->default(false);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
