<?php

$s = App\Models\SchoolSetting::where('user_id', 2)->first();

echo 'ID: ' . $s->id . PHP_EOL;
echo 'nama_sekolah: ' . $s->nama_sekolah . PHP_EOL;
echo 'alamat: ' . $s->alamat . PHP_EOL;
echo 'logo_sekolah path: ' . $s->logo_sekolah . PHP_EOL;
echo 'ttd_kepala path: ' . $s->ttd_kepala . PHP_EOL;

// Cek file fisik di storage
$logoPath = storage_path('app/public/' . $s->logo_sekolah);
$ttdPath  = storage_path('app/public/' . $s->ttd_kepala);

echo 'Logo file exists: ' . (file_exists($logoPath) ? 'YES' : 'NO - path: ' . $logoPath) . PHP_EOL;
echo 'TTD file exists: '  . (file_exists($ttdPath)  ? 'YES' : 'NO - path: ' . $ttdPath)  . PHP_EOL;

// Cek method
echo 'logoSekolahBase64 method exists: ' . (method_exists($s, 'logoSekolahBase64') ? 'YES' : 'NO') . PHP_EOL;

$b64 = $s->logoSekolahBase64();
echo 'base64 result: ' . (empty($b64) ? 'NULL/EMPTY' : substr($b64, 0, 50) . '...') . PHP_EOL;

$b64ttd = $s->ttdKepalaBase64();
echo 'ttd base64 result: ' . (empty($b64ttd) ? 'NULL/EMPTY' : substr($b64ttd, 0, 50) . '...') . PHP_EOL;
