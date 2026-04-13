<?php
// File: database/migrations/xxxx_xx_xx_create_invoices_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number', 100)->unique();
            $table->date('invoice_date');
            // Bill To
            $table->string('bill_to_nama', 255);
            $table->text('bill_to_alamat')->nullable();
            $table->string('bill_to_telepon', 50)->nullable();
            $table->string('bill_to_email', 255)->nullable();
            // Harga
            $table->decimal('subtotal',  15, 2)->default(0);
            $table->decimal('tax_rate',   5, 2)->default(0);
            $table->decimal('sales_tax', 15, 2)->default(0);
            $table->decimal('other',     15, 2)->default(0);
            $table->decimal('total',     15, 2)->default(0);
            // Teks
            $table->string('terbilang', 1000)->nullable();
            $table->text('catatan_bank')->nullable();
            $table->string('pesan_penutup', 255)->nullable();
            $table->text('catatan_tambahan')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->text('description');
            $table->decimal('qty',       10, 2)->default(1);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('amount',    15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};