@php
use Carbon\Carbon;
Carbon::setLocale('id');

$getNilai = fn(string $col, string $default = '') => 
    (isset($setting) && ($setting instanceof \App\Models\SchoolSetting))
        ? ($setting->getRawOriginal($col) ?: $default)
        : $default;

$namaSekolah    = $getNilai('nama_sekolah',      'SMKN');
$alamat         = $getNilai('alamat',            'Dr. Prasidhika No. 123, Jakarta');
$telepon        = $getNilai('telepon',           '08966223380');
$emailSekolah   = $getNilai('email',             'info@smkn.id');
$kota           = $getNilai('kota',              'Jakarta');
$namaKepala     = $getNilai('nama_kepala_sekolah', 'Pemilik');
$nipKepala      = $getNilai('nip_kepala_sekolah', '');

$logoBase64 = (isset($setting) && method_exists($setting, 'logoSekolahBase64'))
    ? $setting->logoSekolahBase64() : null;
$ttdBase64  = (isset($setting) && method_exists($setting, 'ttdKepalaBase64'))
    ? $setting->ttdKepalaBase64() : null;

$items    = $invoice->items ?? collect();
$subtotal = $invoice->subtotal ?? 0;
$taxRate  = $invoice->tax_rate ?? 0;
$salesTax = $invoice->sales_tax ?? 0;
$other    = $invoice->other ?? 0;
$total    = $invoice->total ?? 0;

$fmt = fn($n) => number_format($n, 2, ',', '.');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number ?? '' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page { 
            size: A4;
            margin: 0; /* Margin dinonaktifkan di page agar tidak dobel, kita pakai margin body */
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000;
            background-color: #fff;
            margin: 40px 50px; /* Atas/Bawah 40px, Kiri/Kanan 50px - Margin langsung di body sangat stabil untuk PDF */
        }

        /* Container dibiarkan natural tanpa width 100% 
           sehingga tidak akan pernah nabrak ke sisi kanan kertas */
        .container {
            display: block;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .header-table {
            margin-bottom: 35px;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo img {
            max-width: 180px;
            max-height: 90px;
            object-fit: contain;
        }

        .invoice-text {
            font-size: 34pt;
            color: #777;
            font-weight: normal;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        .invoice-no {
            font-size: 11pt;
            font-weight: bold;
            color: #333;
        }
        
        .invoice-no i {
            font-weight: normal;
            margin-right: 5px;
            font-style: italic;
        }

        .address-table {
            margin-bottom: 35px;
        }

        .address-table td {
            vertical-align: top;
            line-height: 1.5;
        }

        .company-name {
            font-weight: bold;
            font-size: 11.5pt;
            color: #222;
            margin-bottom: 4px;
        }

        /* TABEL ITEMS */
        table.items {
            border: 2px solid #000;
            margin-bottom: 25px; /* Jarak antara tabel dan info pembayaran */
        }

        table.items th, table.items td {
            border: 1px solid #000;
            padding: 8px 10px; /* Padding lebih besar agar teks tidak nempel tepi baris */
        }

        table.items th {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            padding: 12px 10px;
        }

        .col-qty { text-align: center; width: 8%; }
        .col-rp { width: 35px; border-right: none !important; }
        .col-val { text-align: right; border-left: none !important; width: 18%; }
        .col-desc { width: 40%; text-align: left; }

        .items tbody tr.item-row td {
            vertical-align: top;
        }

        /* Baris padding agar ada jeda ruang ke Subtotal */
        .pad-row td {
            height: 35px;
        }

        /* SUMMARY / TOTALS */
        .summary-label {
            font-style: italic;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .summary-total {
            font-weight: bold;
        }

        .terbilang-cell {
            vertical-align: top;
            text-align: left;
            font-style: italic;
            padding: 15px !important;
            padding-right: 30px !important;
            line-height: 1.6;
        }

        /* FOOTER INFO */
        .payment-info {
            font-size: 9.5pt;
            line-height: 1.5;
            width: 60%;
            float: left;
        }

        .thank-you {
            margin-top: 25px;
            font-weight: bold;
            font-style: italic;
            font-size: 11pt;
        }

        .signature-box {
            width: 250px;
            float: right;
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            height: 80px; 
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- ==================== HEADER ==================== -->
    <table class="header-table">
        <tr>
            <td width="55%">
                @if($logoBase64)
                <div class="logo">
                    <img src="{{ $logoBase64 }}" alt="Logo">
                </div>
                @endif
            </td>
            <td width="45%" style="text-align: right; padding-top: 10px;">
                <div class="invoice-text">INVOICE</div>
                <div class="invoice-no">
                    <i>INVOICE #</i> {{ $invoice->invoice_number ?? 'INV-001' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- ==================== ADDRESS ==================== -->
    <table class="address-table">
        <tr>
            <td width="55%" style="padding-right: 20px;">
                <div class="company-name">{{ $namaSekolah }}</div>
                <div>{!! nl2br(e($alamat)) !!}</div>
                <div style="margin-top: 5px;">Phone {{ $telepon }}</div>
            </td>
            <td width="45%">
                <table style="width: 100%;">
                    <tr>
                        <td style="font-style: italic; width: 75px; color:#333; font-weight: bold;">BILL TO:</td>
                        <td>
                            {{ $invoice->bill_to_nama ?? '-' }}<br>
                            @if($invoice->bill_to_alamat ?? false){!! nl2br(e($invoice->bill_to_alamat)) !!}<br>@endif
                            @if($invoice->bill_to_telepon ?? false)Telp: {{ $invoice->bill_to_telepon }}<br>@endif
                            @if($invoice->bill_to_email ?? false){{ $invoice->bill_to_email }}@endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ==================== TABEL ITEMS ==================== -->
    <table class="items">
        <thead>
            <tr>
                <th class="col-desc">DESCRIPTION</th>
                <th class="col-qty">QTY</th>
                <th colspan="2">UNIT COST</th>
                <th colspan="2">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr class="item-row">
                <td>{{ $item->description ?? '-' }}</td>
                <td class="col-qty">{{ !empty($item->qty) ? $item->qty : '' }}</td>
                <td class="col-rp">{{ !empty($item->unit_cost) ? 'Rp' : '' }}</td>
                <td class="col-val">{{ !empty($item->unit_cost) ? $fmt($item->unit_cost) : '' }}</td>
                <td class="col-rp">{{ !empty($item->amount) ? 'Rp' : '' }}</td>
                <td class="col-val">{{ !empty($item->amount) ? $fmt($item->amount) : '' }}</td>
            </tr>
            @empty
            <tr class="item-row">
                <td colspan="6" style="text-align: center; padding: 25px;">Tidak ada item</td>
            </tr>
            @endforelse
            
            <!-- Baris kosong sebagai pembatas/padding seperti pada gambar -->
            <tr class="pad-row">
                <td>&nbsp;</td>
                <td></td>
                <td class="col-rp"></td><td class="col-val"></td>
                <td class="col-rp"></td><td class="col-val"></td>
            </tr>

            <!-- Totals block -->
            @php
                $terbilang = $invoice->terbilang ?? '';
                // Menghapus kutip ganda/tunggal bawaan agar tidak numpuk
                $terbilang = trim(trim($terbilang), '"\'');
            @endphp
            <tr>
                <td colspan="2" rowspan="5" class="terbilang-cell">
                    @if($terbilang)
                    Terbilang : "{{ $terbilang }}"
                    @endif
                </td>
                <td colspan="2" class="summary-label">SUBTOTAL</td>
                <td class="col-rp">Rp</td>
                <td class="col-val">{{ $fmt($subtotal) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="summary-label">TAX RATE</td>
                <td class="col-rp">Rp</td>
                <td class="col-val">{{ $taxRate > 0 ? $fmt($salesTax) : '-' }}</td>
            </tr>
            <tr>
                <td colspan="2" class="summary-label">SALES TAX</td>
                <td class="col-rp">Rp</td>
                <td class="col-val">{{ $taxRate > 0 ? $fmt($salesTax) : '-' }}</td>
            </tr>
            <tr>
                <td colspan="2" class="summary-label">OTHER</td>
                <td class="col-rp">Rp</td>
                <td class="col-val">{{ $other > 0 ? $fmt($other) : '-' }}</td>
            </tr>
            <tr>
                <td colspan="2" class="summary-total" style="text-align: center;">TOTAL</td>
                <td class="col-rp summary-total">Rp</td>
                <td class="col-val summary-total">{{ $fmt($total) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- ==================== FOOTER ==================== -->
    <div class="clearfix">
        <div class="payment-info">
            @if(isset($invoice->pesan_pembayaran) && !empty($invoice->pesan_pembayaran))
                {!! nl2br(e($invoice->pesan_pembayaran)) !!}
            @else
                Make all checks payable to BRI Syariah<br>
                4204204201 On Behalf Of {{ $namaSekolah }}
            @endif
            
            <div class="thank-you">
                {{ $invoice->pesan_penutup ?? 'Terima kasih atas kerjasamanya' }}
            </div>
        </div>

        <div class="signature-box">
            <div>{{ $kota }}, {{ \Carbon\Carbon::parse($invoice->invoice_date ?? now())->translatedFormat('d F Y') }}</div>
            
            <div class="signature-line">
                @if($ttdBase64)
                    <img src="{{ $ttdBase64 }}" style="height: 70px; object-fit: contain; margin-bottom: 5px;">
                @endif
            </div>
            <div style="font-weight: bold;">{{ $namaKepala }}</div>
            @if($nipKepala)
            <div style="font-size: 9pt;">NIP: {{ $nipKepala }}</div>
            @else
            <div style="font-size: 9pt;">Admin</div>
            @endif
        </div>
    </div>

</div> <!-- end container -->
</body>
</html>