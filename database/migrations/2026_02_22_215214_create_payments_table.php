<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('package_type'); // monthly, yearly, lifetime
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending'); // pending, settlement, expired, cancel
            $table->string('payment_type')->nullable(); // credit_card, bank_transfer, etc
            $table->json('raw_response')->nullable();
            $table->timestamps();
            
            // Index
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};