<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Stored Procedure 1: Laporan Mutasi Kas per Periode
        DB::unprepared("
            DROP PROCEDURE IF EXISTS GetLaporanMutasiKas;
            
            CREATE PROCEDURE GetLaporanMutasiKas(
                IN p_user_id INT,
                IN p_tahun INT,
                IN p_bulan INT
            )
            BEGIN
                SELECT 
                    tanggal,
                    uraian,
                    debit,
                    kredit,
                    saldo
                FROM mutasi_kas
                WHERE user_id = p_user_id 
                    AND YEAR(tanggal) = p_tahun
                    AND (p_bulan = 0 OR MONTH(tanggal) = p_bulan)
                ORDER BY tanggal ASC;
            END
        ");

        // Stored Procedure 2: Ringkasan Keuangan per Bulan
        DB::unprepared("
            DROP PROCEDURE IF EXISTS GetRingkasanKeuangan;
            
            CREATE PROCEDURE GetRingkasanKeuangan(
                IN p_user_id INT,
                IN p_tahun INT
            )
            BEGIN
                SELECT 
                    MONTH(tanggal) as bulan,
                    SUM(debit) as total_pemasukan,
                    SUM(kredit) as total_pengeluaran,
                    SUM(debit) - SUM(kredit) as surplus
                FROM mutasi_kas
                WHERE user_id = p_user_id 
                    AND YEAR(tanggal) = p_tahun
                GROUP BY MONTH(tanggal)
                ORDER BY bulan ASC;
            END
        ");

        // Stored Procedure 3: Cek Status Lisensi User
        DB::unprepared("
            DROP PROCEDURE IF EXISTS GetStatusLisensi;
            
            CREATE PROCEDURE GetStatusLisensi(
                IN p_user_id INT
            )
            BEGIN
                SELECT 
                    l.license_key,
                    l.status,
                    l.end_date,
                    u.name,
                    u.email
                FROM licenses l
                JOIN users u ON l.user_id = u.id
                WHERE l.user_id = p_user_id
                ORDER BY l.created_at DESC
                LIMIT 1;
            END
        ");
    }

    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetLaporanMutasiKas");
        DB::unprepared("DROP PROCEDURE IF EXISTS GetRingkasanKeuangan");
        DB::unprepared("DROP PROCEDURE IF EXISTS GetStatusLisensi");
    }
};