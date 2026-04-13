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
        Schema::create('kode_transaksi', function (Blueprint $table) {
            $table->increments('kode_transaksi_id');
            $table->string('kode', 10)->unique();
            $table->string('keterangan', 255);
            $table->unsignedInteger('kategori_id');
            $table->timestamps();

            $table->foreign('kategori_id')
                  ->references('kategori_id')
                  ->on('kategori')
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
        Schema::dropIfExists('kode_transaksi');
    }
};
