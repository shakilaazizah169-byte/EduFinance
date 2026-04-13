<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class TableDataManager extends Command
{
    protected $signature = 'table:manage';
    protected $description = 'Lihat struktur tabel dan isi data via terminal (dengan user_id)';

    public function handle()
    {
        $this->info('=== MANAJEMEN TABEL DATABASE (Multi-Instansi) ===');
        
        while (true) {
            $this->newLine();
            $menu = $this->choice('Pilih menu', [
                '0. Lihat semua tabel',
                '1. Lihat deskripsi tabel',
                '2. Isi data ke tabel (dengan user_id)',
                '3. Keluar'
            ], 0);
            
            switch ($menu) {
                case '0. Lihat semua tabel':
                    $this->showAllTables();
                    break;
                case '1. Lihat deskripsi tabel':
                    $this->describeTable();
                    break;
                case '2. Isi data ke tabel (dengan user_id)':
                    $this->insertDataWithUserId();
                    break;
                case '3. Keluar':
                    $this->info('👋 Sampai jumpa!');
                    return;
            }
        }
    }
    
    private function showAllTables()
    {
        $tables = DB::select('SHOW TABLES');
        $database = DB::getDatabaseName();
        $key = "Tables_in_{$database}";
        
        if (empty($tables)) {
            $this->warn('Tidak ada tabel ditemukan.');
            return;
        }
        
        $this->newLine();
        $this->info("📁 Daftar tabel dalam database '{$database}':");
        
        $tableList = [];
        foreach ($tables as $index => $table) {
            $tableList[] = [$index + 1, $table->$key];
        }
        $this->table(['No', 'Nama Tabel'], $tableList);
    }
    
    private function describeTable()
    {
        $tableName = $this->ask('Masukkan nama tabel');
        
        if (!Schema::hasTable($tableName)) {
            $this->error("❌ Tabel '{$tableName}' tidak ditemukan!");
            return;
        }
        
        $columns = DB::select("DESCRIBE {$tableName}");
        
        $this->newLine();
        $this->info("📋 Deskripsi Tabel: {$tableName}");
        $this->newLine();
        
        $rows = [];
        foreach ($columns as $col) {
            $rows[] = [
                $col->Field,
                $col->Type,
                $col->Null == 'YES' ? '✓' : '✗',
                $col->Key,
                $col->Default ?? 'NULL',
                $col->Extra ?: '-'
            ];
        }
        
        $this->table(
            ['Kolom', 'Tipe', 'Nullable', 'Key', 'Default', 'Extra'],
            $rows
        );
    }
    
    private function insertDataWithUserId()
    {
        // Step 1: Pilih user/instansi
        $userId = $this->selectUser();
        if (!$userId) {
            return;
        }
        
        // Step 2: Pilih tabel
        $tableName = $this->ask('Masukkan nama tabel tujuan');
        
        if (!Schema::hasTable($tableName)) {
            $this->error("❌ Tabel '{$tableName}' tidak ditemukan!");
            return;
        }
        
        // Step 3: Cek apakah tabel memiliki kolom user_id
        $hasUserIdColumn = Schema::hasColumn($tableName, 'user_id');
        if (!$hasUserIdColumn) {
            $this->warn("⚠️  Tabel '{$tableName}' tidak memiliki kolom 'user_id'!");
            if (!$this->confirm('Lanjutkan tanpa user_id?', false)) {
                return;
            }
        }
        
        // Step 4: Ambil struktur kolom
        $columns = DB::select("DESCRIBE {$tableName}");
        
        // Filter kolom yang bisa diisi (skip auto-increment)
        $fillableColumns = [];
        foreach ($columns as $col) {
            if (str_contains($col->Extra, 'auto_increment')) {
                continue;
            }
            // Skip kolom user_id (akan diisi otomatis)
            if ($col->Field == 'user_id') {
                continue;
            }
            $fillableColumns[] = $col;
        }
        
        if (empty($fillableColumns)) {
            $this->warn('⚠️  Tidak ada kolom yang bisa diisi.');
            return;
        }
        
        $this->newLine();
        $this->info("🔧 Memasukkan data untuk User ID: {$userId}");
        $this->line("📝 Tabel target: {$tableName}");
        $this->newLine();
        
        $data = [];
        
        // Tambahkan user_id jika kolomnya ada
        if ($hasUserIdColumn) {
            $data['user_id'] = $userId;
            $this->line("✅ user_id akan diisi otomatis: {$userId}");
            $this->newLine();
        }
        
        // Isi kolom lainnya
        foreach ($fillableColumns as $col) {
            $isRequired = $col->Null == 'NO';
            $label = "{$col->Field} ({$col->Type})";
            
            while (true) {
                $value = $this->ask($label, $isRequired ? null : '(kosong = NULL)');
                
                if (empty($value) && !$isRequired) {
                    $data[$col->Field] = null;
                    break;
                }
                
                if (empty($value) && $isRequired) {
                    $this->error('❌ Kolom ini wajib diisi!');
                    continue;
                }
                
                $valid = $this->validateByType($value, $col->Type);
                
                if ($valid['status']) {
                    $data[$col->Field] = $valid['value'];
                    break;
                } else {
                    $this->error($valid['message']);
                }
            }
        }
        
        // Konfirmasi sebelum insert
        $this->newLine();
        $this->info('📋 Data yang akan dimasukkan:');
        foreach ($data as $key => $value) {
            $displayValue = $value === null ? 'NULL' : $value;
            $this->line("  {$key}: {$displayValue}");
        }
        
        if ($this->confirm('Apakah data sudah benar?', true)) {
            try {
                DB::table($tableName)->insert($data);
                $this->info('✅ Data berhasil dimasukkan!');
                
                // Tampilkan data yang baru masuk
                $lastId = DB::getPdo()->lastInsertId();
                $this->info("📌 ID Record terakhir: {$lastId}");
                
            } catch (\Exception $e) {
                $this->error('❌ Gagal memasukkan data: ' . $e->getMessage());
            }
        } else {
            $this->warn('❌ Dibatalkan.');
        }
    }
    
    private function selectUser()
    {
        $this->newLine();
        $this->info('📋 DAFTAR USER/INSTANSI:');
        
        // Ambil daftar user
        $users = DB::table('users')->select('id', 'name', 'email', 'school_name')->get();
        
        if ($users->isEmpty()) {
            $this->error('❌ Belum ada user! Buat user terlebih dahulu.');
            
            if ($this->confirm('Ingin membuat user baru?', true)) {
                $this->call('make:filament-user'); // Sesuaikan dengan auth Anda
            }
            return false;
        }
        
        // Tampilkan daftar user
        $userOptions = [];
        foreach ($users as $user) {
            $userOptions[] = [
                'ID' => $user->id,
                'Nama' => $user->name,
                'Email' => $user->email,
                'Instansi' => $user->school_name ?? '-'
            ];
        }
        
        $this->table(['ID', 'Nama', 'Email', 'Instansi'], $userOptions);
        
        // Pilih user
        $selectedId = $this->ask('Masukkan ID User yang akan digunakan');
        
        if (!is_numeric($selectedId)) {
            $this->error('❌ ID harus berupa angka!');
            return false;
        }
        
        $userExists = DB::table('users')->where('id', $selectedId)->exists();
        
        if (!$userExists) {
            $this->error("❌ User dengan ID {$selectedId} tidak ditemukan!");
            return false;
        }
        
        return (int)$selectedId;
    }
    
    private function validateByType($value, $mysqlType)
    {
        $type = strtolower($mysqlType);
        
        // Handle enum
        if (preg_match('/enum\((.*)\)/', $type, $matches)) {
            $allowed = str_getcsv($matches[1], ',', "'");
            if (!in_array($value, $allowed)) {
                return [
                    'status' => false,
                    'message' => "Nilai harus salah satu dari: " . implode(', ', $allowed)
                ];
            }
            return ['status' => true, 'value' => $value];
        }
        
        // Integer types
        if (str_contains($type, 'int')) {
            if (!is_numeric($value)) {
                return ['status' => false, 'message' => 'Harus berupa angka integer!'];
            }
            return ['status' => true, 'value' => (int)$value];
        }
        
        // Decimal, float, double
        if (str_contains($type, 'decimal') || str_contains($type, 'float') || str_contains($type, 'double')) {
            if (!is_numeric($value)) {
                return ['status' => false, 'message' => 'Harus berupa angka desimal!'];
            }
            return ['status' => true, 'value' => (float)$value];
        }
        
        // Date, datetime, timestamp
        if (str_contains($type, 'date') || str_contains($type, 'datetime') || str_contains($type, 'timestamp')) {
            if (strtotime($value) === false) {
                return ['status' => false, 'message' => 'Format tanggal tidak valid! (contoh: 2024-01-01)'];
            }
            return ['status' => true, 'value' => $value];
        }
        
        // Boolean
        if (str_contains($type, 'bool') || str_contains($type, 'tinyint(1)')) {
            $lower = strtolower($value);
            if (in_array($lower, ['true', '1', 'yes', 'y'])) {
                return ['status' => true, 'value' => 1];
            }
            if (in_array($lower, ['false', '0', 'no', 'n'])) {
                return ['status' => true, 'value' => 0];
            }
            return ['status' => false, 'message' => 'Harus true/false, 1/0, yes/no'];
        }
        
        return ['status' => true, 'value' => $value];
    }
}