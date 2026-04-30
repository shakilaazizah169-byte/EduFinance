<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mutation_evidences', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('mutation_id');
            $table->date('evidence_date')->nullable();
            $table->string('evidence_number', 50)->unique();
            $table->enum('evidence_type', ['struk', 'kwitansi', 'nota', 'faktur', 'transfer', 'lainnya']);
            $table->string('evidence_title', 255);
            $table->decimal('evidence_amount', 15, 2)->nullable();
            $table->string('evidence_file', 255)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('mutation_id')
                ->references('mutasi_id')
                ->on('mutasi_kas')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mutation_evidences');
    }
};
