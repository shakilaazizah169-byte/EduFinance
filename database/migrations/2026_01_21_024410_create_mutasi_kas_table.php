<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutasi_kas', function (Blueprint $table) {
            $table->increments('mutasi_id');
            $table->date('tanggal');
            $table->unsignedInteger('kode_transaksi_id');
            $table->string('uraian', 255)->nullable();
            $table->decimal('masuk', 15, 2)->nullable();
            $table->decimal('keluar', 15, 2)->nullable();
            $table->decimal('saldo', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('kode_transaksi_id')
                  ->references('kode_transaksi_id')
                  ->on('kode_transaksi')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mutasi_kas');
    }
};
