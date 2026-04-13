<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_key')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('active'); // active, expired, suspended
            $table->date('start_date');
            $table->date('end_date');
            $table->string('package_type'); // monthly, yearly, lifetime
            $table->decimal('price', 15, 2);
            $table->timestamps();
            
            // Index untuk pencarian cepat
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index(['end_date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('licenses');
    }
};