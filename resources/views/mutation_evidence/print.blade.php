<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Bukti Mutasi</title>
    <style>
        @page {
            margin: 18mm 15mm 22mm 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #000;
            margin: 0;
        }

        /* ── KOP ────────────────────────────────────────── */
        .kop {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .kop td {
            vertical-align: middle;
            padding: 4px;
        }

        .logo-cell {
            width: 80px;
            text-align: center;
        }

        .logo-cell img {
            max-width: 72px;
            max-height: 72px;
        }

        .logo-placeholder {
            width: 72px;
            height: 72px;
            border: 1px dashed #bbb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7.5pt;
            color: #aaa;
            text-align: center;
        }

        .school-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .school-info {
            font-size: 8.5pt;
            color: #333;
        }

        hr.double {
            border: none;
            border-top: 3px double #000;
            margin: 8px 0 12px;
        }

        /* ── JUDUL ──────────────────────────────────────── */
        .report-title {
            text-align: center;
            margin-bottom: 14px;
        }

        .report-title h2 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .report-title p {
            margin: 3px 0 0;
            font-size: 9pt;
        }

        /* ── BUKTI CARD ─────────────────────────────────── */
        .evidence-card {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px 12px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .evidence-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .evidence-number {
            font-weight: bold;
            font-size: 10pt;
            color: #1e3a8a;
        }

        .evidence-type-badge {
            font-size: 7.5pt;
            padding: 2px 8px;
            border-radius: 20px;
            background: #e0e7ff;
            color: #3730a3;
            font-weight: bold;
        }

        .evidence-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .evidence-grid td {
            padding: 2px 4px;
        }

        .evidence-grid .label {
            width: 30%;
            color: #555;
        }

        .evidence-grid .value {
            font-weight: 500;
        }

        .amount {
            font-size: 10pt;
            font-weight: bold;
            color: #166534;
        }

        .file-note {
            margin-top: 6px;
            font-size: 8pt;
            color: #666;
            font-style: italic;
        }

        /* ── TOTAL ──────────────────────────────────────── */
        .total-row {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            margin-bottom: 14px;
        }

        .total-row td {
            padding: 6px 10px;
            font-weight: bold;
            font-size: 10pt;
        }

        .total-box {
            background: #e0e7ff;
            border: 2px solid #1e3a8a;
            border-radius: 4px;
            text-align: right;
        }

        /* ── TTD ────────────────────────────────────────── */
        .ttd {
            width: 100%;
            border-collapse: collapse;
            margin-top: 28px;
        }

        .ttd td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 9pt;
        }

        .ttd img {
            max-height: 55px;
        }

        .footer {
            margin-top: 16px;
            font-size: 8pt;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            color: #666;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    @php
    use Carbon\Carbon;
    $fmt = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $fmtDate = fn($d) => $d ? Carbon::parse($d)->translatedFormat('d F Y') : 'Belum diisi';

    $typeLabels = \App\Models\MutationEvidence::$typeLabels;
    @endphp

    {{-- Print Button --}}
    <div class="no-print" style="position:fixed;top:16px;right:16px;z-index:99">
        <button onclick="window.print()"
            style="background:#1e3a8a;color:#fff;border:none;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:11pt">
            🖨️ Cetak
        </button>
        <button onclick="window.close()"
            style="background:#6b7280;color:#fff;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;font-size:11pt;margin-left:8px">
            ✕ Tutup
        </button>
    </div>

    {{-- ── KOP ──────────────────────────────────────── --}}
    <table class="kop">
        <tr>
            <td class="logo-cell">
                @if(isset($setting) && method_exists($setting, 'logoSekolahBase64') && $setting->logoSekolahBase64())
                <img src="{{ $setting->logoSekolahBase64() }}">
                @else
                <div class="logo-placeholder">LOGO<br>SEKOLAH</div>
                @endif
            </td>
            <td style="text-align:center;">
                <div class="school-name">{{ $setting->nama_sekolah ?? 'NAMA INSTANSI' }}</div>
                <div class="school-info">{{ $setting->alamat ?? '' }}</div>
                <div class="school-info">
                    {{ $setting->telepon ?? '' }}
                    @if(isset($setting->email) && $setting->email) | {{ $setting->email }} @endif
                    @if(isset($setting->npsn) && $setting->npsn) | NPSN: {{ $setting->npsn }} @endif
                </div>
            </td>
            <td class="logo-cell">
                @if(isset($setting) && method_exists($setting, 'logoYayasanBase64') && $setting->logoYayasanBase64())
                <img src="{{ $setting->logoYayasanBase64() }}">
                @else
                <div class="logo-placeholder" style="opacity:.4">LOGO<br>YAYASAN</div>
                @endif
            </td>
        </tr>
    </table>

    <hr class="double">

    {{-- ── JUDUL ─────────────────────────────────────── --}}
    <div class="report-title">
        <h2>Laporan Bukti Mutasi</h2>
        @if($startDate && $endDate)
        <p>Periode {{ $fmtDate($startDate) }} s/d {{ $fmtDate($endDate) }}</p>
        @else
        <p>Seluruh Periode</p>
        @endif
        <p>Dicetak: {{ Carbon::now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>

    {{-- ── DAFTAR BUKTI ─────────────────────────────── --}}
    @forelse($evidences as $i => $ev)
    <div class="evidence-card">
        <div class="evidence-card-header">
            <div>
                <span class="evidence-number">{{ $ev->evidence_number }}</span>
                <span style="margin-left:8px;font-size:8.5pt;color:#666">
                    {{ $fmtDate($ev->evidence_date) }}
                </span>
            </div>
            <span class="evidence-type-badge">{{ $typeLabels[$ev->evidence_type] ?? $ev->evidence_type }}</span>
        </div>

        <table class="evidence-grid">
            <tr>
                <td class="label">Judul</td>
                <td class="value">{{ $ev->evidence_title }}</td>
                <td class="label" style="width:20%;padding-left:16px">Nominal</td>
                <td class="amount" style="text-align:right;width:25%">{{ $fmt($ev->evidence_amount) }}</td>
            </tr>
            <tr>
                <td class="label">Mutasi Terkait</td>
                <td class="value" colspan="3">
                    @if($ev->mutasiKas)
                    {{ Carbon::parse($ev->mutasiKas->tanggal)->format('d/m/Y') }}
                    — {{ $ev->mutasiKas->uraian }}
                    @if($ev->mutasiKas->kodeTransaksi)
                    ({{ $ev->mutasiKas->kodeTransaksi->kode }})
                    @endif
                    @else
                    —
                    @endif
                </td>
            </tr>
            @if($ev->notes)
            <tr>
                <td class="label">Catatan</td>
                <td class="value" colspan="3">{{ $ev->notes }}</td>
            </tr>
            @endif
        </table>

        @if($ev->evidence_file)
        <div class="file-note">
            📎 File terlampir: {{ basename($ev->evidence_file) }}
            — Lihat di: {{ config('app.url') }}/storage/{{ $ev->evidence_file }}
        </div>
        @endif
    </div>
    @empty
    <p style="text-align:center;color:#999;padding:30px">Tidak ada bukti mutasi untuk periode ini.</p>
    @endforelse

    {{-- ── TOTAL ────────────────────────────────────── --}}
    <table class="total-row">
        <tr>
            <td style="text-align:right;padding-right:16px">Total Nominal Seluruh Bukti ({{ $evidences->count() }} item):</td>
            <td class="total-box" style="width:240px">{{ $fmt($totalAmount) }}</td>
        </tr>
    </table>

    {{-- ── TTD ──────────────────────────────────────── --}}
    <table class="ttd">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Instansi<br><br><br>
                @if(isset($setting) && method_exists($setting, 'ttdKepalaBase64') && $setting->ttdKepalaBase64())
                <img src="{{ $setting->ttdKepalaBase64() }}"><br>
                @else
                <br><br>
                @endif
                <strong>{{ $setting->nama_kepala_sekolah ?? '( ....................... )' }}</strong>
                @if(isset($setting->nip_kepala_sekolah) && $setting->nip_kepala_sekolah)
                <br>NIP. {{ $setting->nip_kepala_sekolah }}
                @endif
            </td>
            <td>
                {{ $setting->kota ?? '' }}, {{ Carbon::now()->translatedFormat('d F Y') }}<br>
                Bendahara<br><br><br>
                @if(isset($setting) && method_exists($setting, 'ttdBendaharaBase64') && $setting->ttdBendaharaBase64())
                <img src="{{ $setting->ttdBendaharaBase64() }}"><br>
                @else
                <br><br>
                @endif
                <strong>{{ $setting->nama_bendahara ?? '( ....................... )' }}</strong>
                @if(isset($setting->nip_bendahara) && $setting->nip_bendahara)
                <br>NIP. {{ $setting->nip_bendahara }}
                @endif
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh sistem | {{ Carbon::now()->translatedFormat('d F Y H:i') }} WIB
    </div>

</body>

</html>