<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini menyimpan riwayat pemasukan Super Admin
     * dari setiap pembayaran user (pembelian paket berlangganan).
     * 
     * Data HANYA bisa di-insert otomatis (dari webhook/checkStatus),
     * tidak bisa diedit/dihapus manual.
     */
    public function up(): void
    {
        Schema::create('super_admin_mutasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')
                  ->constrained('payments')
                  ->cascadeOnDelete();
            $table->string('order_id')->index();
            $table->string('buyer_name')->nullable();
            $table->string('buyer_email')->nullable();
            $table->string('school_name')->nullable();
            $table->string('package_type');          // monthly, yearly, lifetime
            $table->string('package_label');         // Paket Bulanan, dst
            $table->date('tanggal');
            $table->text('uraian');                  // Keterangan transaksi
            $table->decimal('debit', 15, 2)->default(0);    // pemasukan
            $table->decimal('kredit', 15, 2)->default(0);   // selalu 0 (hanya pemasukan)
            $table->decimal('saldo', 15, 2)->default(0);    // saldo kumulatif
            $table->timestamps();

            $table->unique('payment_id'); // 1 payment = 1 mutasi (cegah duplikat)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('super_admin_mutasi');
    }
};