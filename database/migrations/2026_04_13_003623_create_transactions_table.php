<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_id')->unique();
            $table->decimal('amount', 15, 2);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->integer('license_duration_days'); // 30, 90, 365, dll
            $table->string('package_type'); // monthly, quarterly, yearly, lifetime
            $table->timestamp('purchased_at');
            $table->timestamp('expired_at')->nullable();
            $table->json('payment_response')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'payment_status']);
            $table->index('order_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};