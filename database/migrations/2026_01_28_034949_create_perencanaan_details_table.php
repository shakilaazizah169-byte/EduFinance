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
        Schema::create('perencanaan_details', function (Blueprint $table) {
            $table->increments('detail_id');
            $table->unsignedInteger('perencanaan_id');

            $table->string('perencanaan');
            $table->string('target');
            $table->text('deskripsi')->nullable();
            $table->text('pelaksanaan')->nullable();

            $table->timestamps();

            $table->foreign('perencanaan_id')
                ->references('perencanaan_id')
                ->on('perencanaans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perencanaan_details');
    }
};
