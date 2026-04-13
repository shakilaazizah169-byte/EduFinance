@php
use Carbon\Carbon;
Carbon::setLocale('id');

/**
 * DIAGNOSIS LENGKAP:
 * ===================
 * Masalah utama: super_admin belum punya row di tabel school_settings,
 * sehingga SchoolSetting::where('user_id', $superAdmin->id)->first() = null
 * → fallback ke `new SchoolSetting()` yang kosong semua kolomnya.
 *
 * Di blade lama, kondisi `if (!isset($setting))` TIDAK pernah true karena
 * $setting sudah di-set (walaupun isinya kosong), sehingga hardcoded fallback
 * tidak aktif, tapi getRawOriginal() juga return null → tampil kosong.
 *
 * FIX:
 * 1. Gunakan ->exists untuk cek apakah setting punya data DB
 * 2. Semua nilai teks pakai helper getNilai() dengan fallback eksplisit
 * 3. Gambar pakai getRawOriginal() → toBase64() yang sudah proven bekerja
 */

// Helper: ambil nilai raw dari model atau fallback ke default
$getNilai = fn(string $col, string $default = '') => 
    (isset($setting) && ($setting instanceof \App\Models\SchoolSetting))
        ? ($setting->getRawOriginal($col) ?: $default)
        : $default;

$namaSekolah    = $getNilai('nama_sekolah',      'EduFinance');
$alamat         = $getNilai('alamat',            'Jl. Pendidikan No. 123, Jakarta');
$telepon        = $getNilai('telepon',           '(021) 1234567');
$emailSekolah   = $getNilai('email',             'info@edufinance.id');
$namaKepala     = $getNilai('nama_kepala_sekolah','Pemilik');
$nipKepala      = $getNilai('nip_kepala_sekolah', '');

// Gambar: hanya bisa diambil jika $setting adalah SchoolSetting model
$logoBase64 = (isset($setting) && method_exists($setting, 'logoSekolahBase64'))
    ? $setting->logoSekolahBase64()
    : null;

$ttdBase64 = (isset($setting) && method_exists($setting, 'ttdKepalaBase64'))
    ? $setting->ttdKepalaBase64()
    : null;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $payment->order_id ?? $license->license_key }}</title>
    <style>
        @page { margin: 18mm 15mm 22mm 15mm; }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #000;
            margin: 0;
        }

        /* ===== KOP ===== */
        .kop { width:100%; border-collapse:collapse; }
        .kop td { vertical-align:middle; padding:4px; }
        .logo-cell { width:85px; text-align:center; }
        .logo-cell img { max-width:80px; max-height:80px; }
        .logo-placeholder {
            width:80px; height:80px;
            border:2px dashed #ccc;
            border-radius: 6px;
            text-align:center;
            font-size:8pt;
            color:#bbb;
            padding-top:28px;
        }
        .sekolah-name { font-size:15pt; font-weight:bold; text-transform:uppercase; }
        .sekolah-info { font-size:8.5pt; color:#333; line-height:1.6; }
        .invoice-badge { font-size:22pt; font-weight:900; color:#1e3a8a; letter-spacing:2px; }
        .invoice-no { font-size:8.5pt; color:#555; margin-top:3px; }

        hr.kop-line { border:none; border-top:3px double #1e3a8a; margin:10px 0 14px; }

        /* ===== INFO TABEL ===== */
        .info { width:100%; border-collapse:collapse; margin:10px 0; font-size:8.5pt; }
        .info td { padding:3px 5px; vertical-align:top; }
        .info .label { width:115px; font-weight:bold; color:#1e3a8a; white-space:nowrap; }
        .info .colon { width:8px; }

        /* ===== DATA TABEL ===== */
        table.data { width:100%; border-collapse:collapse; font-size:8.5pt; margin-top:8px; }
        table.data th {
            background:#1e3a8a; color:#fff;
            padding:7px 8px; text-align:left;
            border:1px solid #1e3a8a;
        }
        table.data th.text-right { text-align:right; }
        table.data th.text-center { text-align:center; }
        table.data td { padding:5px 8px; border:1px solid #dde3f0; }
        table.data tfoot td {
            padding:5px 8px;
            border:1px solid #dde3f0;
            background:#f8f9ff;
        }
        table.data tfoot tr:last-child td {
            background:#1e3a8a;
            color:#fff;
            font-weight:bold;
        }
        .text-right  { text-align:right; }
        .text-center { text-align:center; }

        /* ===== BALANCE DUE ===== */
        .balance-due {
            text-align:right;
            margin:12px 0 4px;
            font-size:11pt;
        }
        .balance-due .label { font-weight:bold; color:#1a1a1a; }
        .balance-due .amount { color:#1e3a8a; font-weight:900; font-size:12pt; }

        /* ===== TTD ===== */
        .ttd-wrap { width:100%; border-collapse:collapse; margin-top:28px; }
        .ttd-wrap td { width:50%; text-align:center; vertical-align:top; }
        .ttd-kiri { text-align:left !important; }
        .ttd-label { font-size:8pt; color:#555; margin-bottom:50px; line-height:1.7; }
        .ttd-img { display:block; margin:0 auto 6px; max-height:58px; max-width:140px; }
        .ttd-spacer { height:58px; display:block; }
        .ttd-garis {
            display:inline-block; border-top:1.5px solid #1a1a1a;
            min-width:140px; padding-top:4px;
        }
        .ttd-nama  { font-weight:bold; font-size:9pt; color:#1a1a1a; }
        .ttd-title { font-size:8pt; color:#555; }

        /* ===== FOOTER ===== */
        .doc-footer {
            margin-top:18px;
            padding-top:8px;
            border-top:1px solid #ccc;
            font-size:7.5pt;
            text-align:center;
            color:#777;
            line-height:1.8;
        }
    </style>
</head>
<body>

{{-- ============================= KOP ============================= --}}
<table class="kop">
    <tr>
        {{-- Logo kiri --}}
        <td class="logo-cell">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo">
            @else
                <div class="logo-placeholder">LOGO</div>
            @endif
        </td>

        {{-- Identitas tengah --}}
        <td style="text-align:center; padding:4px 12px;">
            <div class="sekolah-name">{{ $namaSekolah }}</div>
            <div class="sekolah-info">
                {{ $alamat }}<br>
                {{ $telepon }}
                @if($emailSekolah) &nbsp;|&nbsp; {{ $emailSekolah }} @endif
            </div>
        </td>

        {{-- Badge INVOICE kanan --}}
        <td class="logo-cell" style="text-align:right; vertical-align:middle; padding-right:0;">
            <div class="invoice-badge">INVOICE</div>
            <div class="invoice-no">No. {{ $payment->order_id ?? $license->license_key }}</div>
        </td>
    </tr>
</table>

<hr class="kop-line">

{{-- ========================= INFO PEMBELI ========================= --}}
<table class="info">
    <tr>
        <td class="label">Bill To</td>
        <td class="colon">:</td>
        <td><strong>{{ $user->name }}</strong></td>
        <td class="label" style="padding-left:20px;">Invoice Date</td>
        <td class="colon">:</td>
        <td>{{ Carbon::parse($payment->created_at ?? $license->created_at)->translatedFormat('d F Y') }}</td>
    </tr>
    <tr>
        <td class="label">Instansi</td>
        <td class="colon">:</td>
        <td>{{ $user->school_name ?? '-' }}</td>
        <td class="label" style="padding-left:20px;">License Key</td>
        <td class="colon">:</td>
        <td style="font-family: 'Courier New', monospace; font-size:8pt;">{{ $license->license_key }}</td>
    </tr>
    <tr>
        <td class="label">Email</td>
        <td class="colon">:</td>
        <td>{{ $user->email }}</td>
        <td class="label" style="padding-left:20px;">Periode</td>
        <td class="colon">:</td>
        <td>{{ Carbon::parse($license->start_date)->format('d/m/Y') }} &ndash; {{ Carbon::parse($license->end_date)->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td class="label">Telepon</td>
        <td class="colon">:</td>
        <td>{{ $user->phone ?? '-' }}</td>
        <td></td><td></td><td></td>
    </tr>
</table>

{{-- ========================= TABEL ITEM ========================= --}}
<table class="data">
    <thead>
        <tr>
            <th class="text-center" style="width:5%;">#</th>
            <th style="width:55%;">Deskripsi</th>
            <th class="text-right" style="width:20%;">Harga</th>
            <th class="text-right" style="width:20%;">Total</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center">1</td>
            <td><strong>{{ $packageLabel }}</strong></td>
            <td class="text-right">Rp {{ number_format($license->price, 0, ',', '.') }}</td>
            <td class="text-right">Rp {{ number_format($license->price, 0, ',', '.') }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="text-right">Sub Total</td>
            <td class="text-right">Rp {{ number_format($license->price, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="text-right">PPN 0%</td>
            <td class="text-right">Rp 0</td>
        </tr>
        <tr>
            <td colspan="3" class="text-right">TOTAL</td>
            <td class="text-right">Rp {{ number_format($license->price, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

{{-- ========================= BALANCE DUE ========================= --}}
<div class="balance-due">
    <span class="label">Balance Due : </span>
    <span class="amount">Rp {{ number_format($license->price, 0, ',', '.') }}</span>
</div>

{{-- ========================= TANDA TANGAN ========================= --}}
<table class="ttd-wrap">
    <tr>
        <td class="ttd-kiri"></td>
        <td style="text-align:center;">
            <div class="ttd-label">
                Jakarta, {{ Carbon::now()->translatedFormat('d F Y') }}<br>
                Pemilik,
            </div>

            {{-- TTD: tampil jika ada, spacer jika tidak ada --}}
            @if($ttdBase64)
                <img class="ttd-img" src="{{ $ttdBase64 }}" alt="TTD">
            @else
                <span class="ttd-spacer"></span>
            @endif

            <div>
                <span class="ttd-garis">
                    <div class="ttd-nama">{{ $namaKepala }}</div>
                    @if($nipKepala)
                        <div class="ttd-title">{{ $nipKepala }}</div>
                    @endif
                </span>
            </div>
        </td>
    </tr>
</table>

{{-- ========================= FOOTER ========================= --}}
<div class="doc-footer">
    Terima kasih telah menggunakan layanan {{ $namaSekolah }}<br>
    Dokumen ini sah dan diproses secara elektronik<br>
    Dicetak otomatis pada {{ Carbon::now()->translatedFormat('d F Y H:i') }} WIB
</div>

</body>
</html>