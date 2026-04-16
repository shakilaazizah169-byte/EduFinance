<?php
ini_set('memory_limit', '1024M')
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
@page { margin: 18mm 15mm 22mm 15mm; }
* { box-sizing: border-box; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9pt;
    color: #000;
    margin: 0;
}

/* ================= KOP ================= */
.kop { width:100%; border-collapse:collapse; }
.kop td { vertical-align:middle; padding:4px; }
.logo-cell { width:80px; text-align:center; }
.logo-cell img { max-width:75px; max-height:75px; }
.logo-placeholder {
    width:75px; height:75px;
    border:1px dashed #999;
    display:flex; align-items:center; justify-content:center;
    font-size:8pt; color:#999;
}
.sekolah-name {
    font-size:15pt; font-weight:bold;
    text-transform:uppercase;
}
.sekolah-info { font-size:8.5pt; color:#333; }

hr { border:none; border-top:3px double #000; margin:8px 0 15px; }

/* ================= JUDUL ================= */
.judul { text-align:center; margin-bottom:10px; }
.judul h2 {
    font-size:12pt;
    font-weight:bold;
    text-transform:uppercase;
    margin:0;
}
.judul p { margin:3px 0 0; font-size:9pt; }

/* ================= TABEL ================= */
table.data { width:100%; border-collapse:collapse; font-size:8.5pt; }
table.data th {
    background:#1e3a8a;
    color:#fff;
    padding:6px;
    border:1px solid #1e3a8a;
}
table.data td {
    padding:4px 6px;
    border:1px solid #ccc;
}
.text-center { text-align:center; }
.text-right { text-align:right; }

.row-saldo-awal td { background:#fff7cc; font-weight:bold; }
.row-total td {
    background:#e0e7ff;
    font-weight:bold;
    border-top:2px solid #1e3a8a;
}
.row-saldo-akhir td {
    background:#dcfce7;
    font-weight:bold;
}

/* ================= RINGKASAN ================= */
.ringkasan {
    margin-top:12px;
    width:100%;
    border-collapse:collapse;
    font-size:8.5pt;
}
.ringkasan td {
    border:1px solid #000;
    padding:5px 8px;
}
.ringkasan .label { background:#f5f5f5; }

/* ================= TTD ================= */
.ttd { margin-top:25px; width:100%; border-collapse:collapse; }
.ttd td { width:50%; text-align:center; vertical-align:top; }
.ttd img { max-height:55px; }

.footer {
    margin-top:15px;
    font-size:8pt;
    text-align:center;
    border-top:1px solid #ccc;
    padding-top:5px;
}
</style>
</head>
<body>

@php
use Carbon\Carbon;

$fmt = fn($n) => 'Rp ' . number_format($n,0,',','.');
$fmtDate = fn($d) => Carbon::parse($d)->translatedFormat('d F Y');

$perubahan = $saldoAkhir - $saldoAwal;
$persentase = $saldoAwal != 0 ? ($perubahan / $saldoAwal) * 100 : 0;
@endphp

{{-- ================= KOP ================= --}}
<table class="kop">
<tr>
<td class="logo-cell">
@if($setting->logoSekolahBase64())
<img src="{{ $setting->logoSekolahBase64() }}">
@else
<div class="logo-placeholder">LOGO<br>SEKOLAH</div>
@endif
</td>

<td style="text-align:center;">
<div class="sekolah-name">{{ $setting->nama_sekolah ?? 'NAMA SEKOLAH' }}</div>
<div class="sekolah-info">
{{ $setting->alamat ?? '' }}
</div>
<div class="sekolah-info">
{{ $setting->telepon ?? '' }}
@if($setting->email) | {{ $setting->email }} @endif
@if($setting->npsn) | NPSN: {{ $setting->npsn }} @endif
</div>
</td>

<td class="logo-cell">
@if($setting->logoYayasanBase64())
<img src="{{ $setting->logoYayasanBase64() }}">
@else
<div class="logo-placeholder" style="opacity:.4;">LOGO<br>YAYASAN</div>
@endif
</td>
</tr>
</table>

<hr>

{{-- ================= JUDUL ================= --}}
<div class="judul">
<h2>Laporan Mutasi Kas</h2>
<p>Periode {{ $fmtDate($startDate) }} s/d {{ $fmtDate($endDate) }}</p>
</div>

{{-- ================= TABEL ================= --}}
<table class="data">
<thead>
<tr>
<th>No</th>
<th>Tanggal</th>
<th>Kode</th>
<th>Uraian</th>
<th>Debit</th>
<th>Kredit</th>
<th>Saldo</th>
</tr>
</thead>
<tbody>

<tr class="row-saldo-awal">
<td class="text-center">-</td>
<td class="text-center">{{ Carbon::parse($startDate)->format('d/m/Y') }}</td>
<td class="text-center">-</td>
<td>Saldo Awal Periode</td>
<td class="text-right">-</td>
<td class="text-right">-</td>
<td class="text-right">{{ $fmt($saldoAwal) }}</td>
</tr>

@foreach($mutasi as $i => $m)
<tr>
<td class="text-center">{{ $i+1 }}</td>
<td class="text-center">{{ Carbon::parse($m->tanggal)->format('d/m/Y') }}</td>
<td class="text-center">{{ $m->kodeTransaksi->kode ?? '-' }}</td>
<td>{{ $m->uraian }}</td>
<td class="text-right">{{ $m->debit > 0 ? $fmt($m->debit) : '-' }}</td>
<td class="text-right">{{ $m->kredit > 0 ? $fmt($m->kredit) : '-' }}</td>
<td class="text-right">{{ $fmt($m->saldo_perhitungan ?? 0) }}</td>
</tr>
@endforeach

<tr class="row-total">
<td colspan="4" class="text-right">TOTAL</td>
<td class="text-right">{{ $fmt($totalDebit) }}</td>
<td class="text-right">{{ $fmt($totalKredit) }}</td>
<td></td>
</tr>

<tr class="row-saldo-akhir">
<td colspan="6" class="text-right">SALDO AKHIR</td>
<td class="text-right">{{ $fmt($saldoAkhir) }}</td>
</tr>

</tbody>
</table>

{{-- ================= RINGKASAN ================= --}}
<table class="ringkasan">
<tr>
<td class="label">Saldo Awal</td>
<td>{{ $fmt($saldoAwal) }}</td>
<td class="label">Total Debit</td>
<td>{{ $fmt($totalDebit) }}</td>
</tr>
<tr>
<td class="label">Saldo Akhir</td>
<td>{{ $fmt($saldoAkhir) }}</td>
<td class="label">Perubahan</td>
<td>
{{ $perubahan >= 0 ? '+' : '-' }}
{{ $fmt(abs($perubahan)) }}
({{ number_format($persentase,2) }}%)
</td>
</tr>
</table>

{{-- ================= TTD ================= --}}
<table class="ttd">
<tr>
<td>
Mengetahui,<br>
Kepala Instansi<br><br>
@if($setting->ttdKepalaBase64())
<img src="{{ $setting->ttdKepalaBase64() }}"><br>
@endif
<strong>{{ $setting->nama_kepala_sekolah ?? '....................' }}</strong><br>
@if($setting->nip_kepala_sekolah)
NIP. {{ $setting->nip_kepala_sekolah }}
@endif
</td>

<td>
{{ $setting->kota ?? '' }}, {{ Carbon::now()->translatedFormat('d F Y') }}<br>
Bendahara<br><br>
@if($setting->ttdBendaharaBase64())
<img src="{{ $setting->ttdBendaharaBase64() }}"><br>
@endif
<strong>{{ $setting->nama_bendahara ?? '....................' }}</strong><br>
@if($setting->nip_bendahara)
NIP. {{ $setting->nip_bendahara }}
@endif
</td>
</tr>
</table>

<div class="footer">
Dicetak otomatis pada {{ Carbon::now()->translatedFormat('d F Y H:i') }} WIB
</div>

<table class="ringkasan">
<tr>
<td class="label">Keterangan :</td>
<td>Perubahan Saldo = Saldo Akhir - Saldo Awal</td>
<td>Cash Ratio = Pemasukan / Pengeluaran x 100</td>
</tr>

</table>
</body>
</html>