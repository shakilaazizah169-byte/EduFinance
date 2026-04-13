@php
use Carbon\Carbon;
Carbon::setLocale('id');

/**
 * SINKRON DENGAN school_settings via getRawOriginal()
 * — sama persis dengan pola invoice.blade.php yang sudah proven bekerja.
 * $setting dikirim dari SuperAdminMutasiController::exportPdf()
 * menggunakan: SchoolSetting::where('user_id', auth()->id())->first()
 */
$getNilai = fn(string $col, string $default = '') =>
    (isset($setting) && ($setting instanceof \App\Models\SchoolSetting))
        ? ($setting->getRawOriginal($col) ?: $default)
        : $default;

$namaSekolah  = $getNilai('nama_sekolah',       'EduFinance');
$alamat       = $getNilai('alamat',             '');
$kota         = $getNilai('kota',               '');
$telepon      = $getNilai('telepon',            '');
$emailSetting = $getNilai('email',              '');
$npsn         = $getNilai('npsn',               '');
$namaKepala   = $getNilai('nama_kepala_sekolah','');
$nipKepala    = $getNilai('nip_kepala_sekolah', '');
$namaBendahara = $getNilai('nama_bendahara',    '');
$nipBendahara  = $getNilai('nip_bendahara',     '');

// ========== FIX: Define variables with proper fallbacks ==========
$logoSekolahBase64 = null;
$ttdKepalaBase64 = null;
$ttdBendaharaBase64 = null;

// Safely call methods if they exist
if (isset($setting) && $setting instanceof \App\Models\SchoolSetting) {
    $logoSekolahBase64 = method_exists($setting, 'logoSekolahBase64') ? $setting->logoSekolahBase64() : null;
    $ttdKepalaBase64 = method_exists($setting, 'ttdKepalaBase64') ? $setting->ttdKepalaBase64() : null;
    $ttdBendaharaBase64 = method_exists($setting, 'ttdBendaharaBase64') ? $setting->ttdBendaharaBase64() : null;
}

$fmt     = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
$fmtDate = fn($d) => Carbon::parse($d)->translatedFormat('d F Y');

$totalTrx = $mutasiList->count();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
@page {
    margin: 18mm 15mm 22mm 15mm;
    size: A4 landscape;
}
* { box-sizing: border-box; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9pt;
    color: #000;
    margin: 0;
}

/* ═══════════════ KOP ═══════════════ */
.kop { width: 100%; border-collapse: collapse; }
.kop td { vertical-align: middle; padding: 4px; }
.logo-cell { width: 80px; text-align: center; }
.logo-cell img { max-width: 75px; max-height: 75px; }
.logo-placeholder {
    width: 75px; height: 75px;
    border: 1px dashed #999;
    text-align: center;
    font-size: 8pt; color: #999;
    padding-top: 22px;
    line-height: 1.4;
}
.sekolah-name {
    font-size: 15pt; font-weight: bold;
    text-transform: uppercase;
}
.sekolah-info { font-size: 8.5pt; color: #333; line-height: 1.6; }

hr.kop-line { border: none; border-top: 3px double #000; margin: 8px 0 14px; }

/* ═══════════════ JUDUL ═══════════════ */
.judul { text-align: center; margin-bottom: 10px; }
.judul h2 { font-size: 13pt; font-weight: bold; text-transform: uppercase; margin: 0; }
.judul p  { margin: 3px 0 0; font-size: 9pt; color: #444; }

/* ═══════════════ TABEL DATA ═══════════════ */
table.data { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
table.data th {
    background: #1e3a8a; color: #fff;
    padding: 6px 7px;
    border: 1px solid #1e3a8a;
    vertical-align: middle;
}
table.data td {
    padding: 4px 7px;
    border: 1px solid #ccc;
    vertical-align: middle;
}
.text-center { text-align: center; }
.text-right  { text-align: right; }

/* Row khusus */
.row-even td { background: #f8fafc; }
.row-odd  td { background: #ffffff; }

.row-total td {
    background: #e0e7ff; font-weight: bold;
    border-top: 2px solid #1e3a8a;
}
.row-saldo-akhir td {
    background: #dcfce7; font-weight: bold;
}

/* Badge paket */
.badge {
    display: inline-block; border-radius: 20px;
    padding: 1px 7px; font-size: 7.5pt; font-weight: bold;
}
.b-monthly  { background: #dbeafe; color: #1e3a8a; }
.b-yearly   { background: #d1fae5; color: #065f46; }
.b-lifetime { background: #ede9fe; color: #4c1d95; }

.order-id {
    font-family: 'Courier New', monospace;
    font-size: 7.5pt; color: #475569;
    background: #f1f5f9; padding: 1px 5px; border-radius: 3px;
}

/* ═══════════════ RINGKASAN ═══════════════ */
.ringkasan { margin-top: 10px; width: 100%; border-collapse: collapse; font-size: 8.5pt; }
.ringkasan td { border: 1px solid #aaa; padding: 5px 8px; }
.ringkasan .lbl { background: #f5f5f5; font-weight: bold; width: 18%; }
.ringkasan .val { width: 32%; }

/* ═══════════════ TTD - SAMPING KIRI DAN KANAN SECARA SEJAJAR ═══════════════ */
.ttd-container {
    margin-top: 28px;
    width: 100%;
    display: table;
    table-layout: fixed;
}
.ttd-row {
    display: table-row;
}
.ttd-col {
    display: table-cell;
    vertical-align: top;
    width: 50%;
}
.ttd-col:first-child {
    text-align: left;
    padding-right: 20px;
}
.ttd-col:last-child {
    text-align: right;
    padding-left: 20px;
}
.ttd-content {
    display: inline-block;
    text-align: center;
}
.ttd-col:first-child .ttd-content {
    text-align: left;
}
.ttd-col:last-child .ttd-content {
    text-align: right;
}
.ttd-title {
    font-size: 8.5pt;
    font-weight: 500;
    margin-bottom: 12px;
    color: #1e293b;
    line-height: 1.4;
}
.ttd-signature-area {
    min-height: 80px;
    margin-bottom: 8px;
}
.ttd-signature-area img {
    max-height: 58px;
    max-width: 140px;
    object-fit: contain;
}
.ttd-spacer {
    height: 58px;
    display: block;
}
.ttd-garis {
    border-top: 1.5px solid #2c3e66;
    padding-top: 6px;
    min-width: 170px;
    margin-top: 4px;
}
.ttd-col:first-child .ttd-garis {
    text-align: left;
}
.ttd-col:last-child .ttd-garis {
    text-align: right;
}
.ttd-nama {
    font-weight: 700;
    font-size: 9.5pt;
    color: #0f172a;
    letter-spacing: 0.3px;
}
.ttd-nip {
    font-size: 7.5pt;
    color: #334155;
    margin-top: 2px;
}

/* ═══════════════ FOOTER ═══════════════ */
.footer {
    margin-top: 22px;
    padding-top: 5px;
    border-top: 1px solid #ccc;
    font-size: 8pt;
    text-align: center;
    color: #666;
}
</style>
</head>
<body>

{{-- ═══════════════ KOP SURAT ═══════════════ --}}
<table class="kop">
    60#
        {{-- Logo Instansi (kiri) --}}
        <td class="logo-cell">
            @if($logoSekolahBase64)
                <img src="{{ $logoSekolahBase64 }}" alt="Logo">
            @else
                <div class="logo-placeholder">LOGO<br>SEKOLAH</div>
            @endif
          </td>

        {{-- Identitas Tengah --}}
        <td style="text-align: center;">
            <div class="sekolah-name">{{ $namaSekolah }}</div>
            <div class="sekolah-info">
                @if($alamat) {{ $alamat }}@if($kota), {{ $kota }}@endif @endif
            </div>
            <div class="sekolah-info">
                @if($telepon) {{ $telepon }} @endif
                @if($emailSetting) | {{ $emailSetting }} @endif
                @if($npsn) | Kode Unik: {{ $npsn }} @endif
            </div>
          </td>

        {{-- Logo Yayasan (kanan) --}}
        <td class="logo-cell">

          </td>
    </tr>
</table>

<hr class="kop-line">

{{-- ═══════════════ JUDUL ═══════════════ --}}
<div class="judul">
    <h2>Laporan Mutasi Pemasukan</h2>
    <p>Filter: {{ $filterLabel }} &bull; Total {{ $totalTrx }} Transaksi</p>
    <p>Dicetak: {{ now()->translatedFormat('d F Y') }}, Pukul {{ now()->format('H:i') }} WIB</p>
</div>

{{-- ═══════════════ TABEL DATA ═══════════════ --}}
<table class="data">
    <thead>
         <tr>
            <th class="text-center" style="width: 3.5%;">No</th>
            <th style="width: 14%;">Order ID</th>
            <th class="text-center" style="width: 8.5%;">Tanggal</th>
            <th style="width: 22%;">Nama Instansi</th>
            <th style="width: 13%;">Nama Pembeli</th>
            <th class="text-center" style="width: 9%;">Paket</th>
            <th class="text-right" style="width: 11%;">Debit (Rp)</th>
            <th class="text-right" style="width: 11%;">Saldo (Rp)</th>
         </tr>
    </thead>
    <tbody>
        @forelse($mutasiList as $i => $m)
        <tr class="{{ $i % 2 == 0 ? 'row-even' : 'row-odd' }}">
            <td class="text-center" style="color: #94a3b8; font-size: 8pt;">{{ $i + 1 }}</td>
            <td><span class="order-id">{{ $m->order_id }}</span></td>
            <td class="text-center" style="font-size: 8pt; color: #475569;">
                {{ Carbon::parse($m->tanggal)->format('d/m/Y') }}
              </td>
            <td style="font-weight: 600; color: #1e293b;">
                {{ $m->school_name ?? '—' }}
                @if($m->buyer_email)
                    <div style="font-size: 7pt; color: #94a3b8; font-weight: 400; margin-top: 1px;">
                        {{ $m->buyer_email }}
                    </div>
                @endif
              </td>
            <td>{{ $m->buyer_name ?? '—' }}</td>
            <td class="text-center">
                @php
                    $bc = match($m->package_type) {
                        'monthly'  => 'b-monthly',
                        'yearly'   => 'b-yearly',
                        'lifetime' => 'b-lifetime',
                        default    => 'b-monthly',
                    };
                @endphp
                <span class="badge {{ $bc }}">{{ $m->package_label }}</span>
              </td>
            <td class="text-right" style="color: #065f46; font-weight: bold; font-family: 'Courier New', monospace;">
                + {{ number_format($m->debit, 0, ',', '.') }}
              </td>
            <td class="text-right" style="color: #1e3a8a; font-weight: bold; font-family: 'Courier New', monospace;">
                {{ number_format($m->saldo, 0, ',', '.') }}
              </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center"
                style="padding: 20px; color: #94a3b8; font-style: italic;">
                Tidak ada data untuk periode yang dipilih.
              </td>
        </tr>
        @endforelse

        {{-- Baris total --}}
        @if($mutasiList->isNotEmpty())
        <tr class="row-total">
            <td colspan="6" class="text-right" style="letter-spacing: .4px;">
                TOTAL PEMASUKAN KESELURUHAN
              </td>
            <td class="text-right">{{ $fmt($totalDebit) }}</td>
            <td class="text-right">{{ $fmt($saldoTerkini) }}</td>
        </tr>
        @endif
    </tbody>
</table>

{{-- ═══════════════ RINGKASAN ═══════════════ --}}
<table class="ringkasan">
    <tr>
        <td class="lbl">Total Transaksi</td>
        <td class="val">{{ number_format($totalTrx, 0, ',', '.') }} transaksi</td>
        <td class="lbl">Total Pemasukan</td>
        <td class="val">{{ $fmt($totalDebit) }}</td>
    </tr>
    <tr>
        <td class="lbl">Rata-rata / Transaksi</td>
        <td class="val">{{ $totalTrx > 0 ? $fmt(round($totalDebit / $totalTrx)) : 'Rp 0' }}</td>
        <td class="lbl">Saldo Terkini</td>
        <td class="val" style="color: #1e3a8a; font-weight: bold;">{{ $fmt($saldoTerkini) }}</td>
    </tr>
</table>

{{-- ═══════════════ TANDA TANGAN - SAMPING KIRI DAN KANAN SEJAJAR ═══════════════ --}}
<div class="ttd-container">
    <div class="ttd-row">
        {{-- KOLOM KIRI: MENGETAHUI / PIMPINAN --}}
        <div class="ttd-col">
            <div class="ttd-content">
                <div class="ttd-title">
                    Mengetahui,<br>
                    <span style="font-weight: 600;">{{ $namaKepala ? 'Pimpinan / Kepala Instansi' : 'Administrator Sistem' }}</span>
                </div>
                <div class="ttd-signature-area">
                    @if($ttdKepalaBase64)
                        <img src="{{ $ttdKepalaBase64 }}" alt="Tanda Tangan Pimpinan">
                    @else
                        <span class="ttd-spacer"></span>
                    @endif
                </div>
                <div class="ttd-garis">
                    <div class="ttd-nama">{{ $namaKepala ?: 'Super Admin' }}</div>
                    @if($nipKepala)
                        <div class="ttd-nip">NIP. {{ $nipKepala }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: DIBUAT OLEH / BENDAHARA --}}
        <div class="ttd-col">
            <div class="ttd-content">
                <div class="ttd-title">
                    {{ $kota ?: $namaSekolah }}, {{ Carbon::now()->translatedFormat('d F Y') }}<br>
                    <span style="font-weight: 600;">{{ $namaBendahara ? 'Bendahara' : 'Pemilik Sistem' }}</span>
                </div>
                <div class="ttd-signature-area">
                    @if($ttdBendaharaBase64)
                        <img src="{{ $ttdBendaharaBase64 }}" alt="Tanda Tangan Bendahara">
                    @else
                        <span class="ttd-spacer"></span>
                    @endif
                </div>
                <div class="ttd-garis">
                    <div class="ttd-nama">{{ $namaBendahara ?: $namaSekolah }}</div>
                    @if($nipBendahara)
                        <div class="ttd-nip">NIP. {{ $nipBendahara }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════ FOOTER ═══════════════ --}}
<div class="footer">
    Diterbitkan otomatis oleh Sistem {{ $namaSekolah }} &bull;
    {{ now()->translatedFormat('d F Y H:i') }} WIB &bull;
    <strong>RAHASIA — Hanya untuk Super Admin</strong>
</div>

</body>
</html>