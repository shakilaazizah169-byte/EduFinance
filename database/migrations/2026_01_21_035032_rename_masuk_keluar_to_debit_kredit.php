<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mutasi_kas', function (Blueprint $table) {
            $table->renameColumn('masuk', 'debit');
            $table->renameColumn('keluar', 'kredit');
       

        });
    }

    public function down(): void
    {
        Schema::table('mutasi_kas', function (Blueprint $table) {
            $table->renameColumn('debit', 'masuk');
            $table->renameColumn('kredit', 'keluar');
        });
    }
};
