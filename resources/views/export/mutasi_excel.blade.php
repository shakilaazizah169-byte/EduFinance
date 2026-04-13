<table>
    <tr>
        <td colspan="7"><b>LAPORAN MUTASI KAS</b></td>
    </tr>
    <tr>
        <td colspan="7">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</td>
    </tr>
</table>

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Uraian</th>
            <th>Kode Transaksi</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mutasi as $i => $m)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ \Carbon\Carbon::parse($m->tanggal)->format('d-m-Y') }}</td>
            <td>{{ $m->uraian }}</td>
            <td>{{ $m->kodeTransaksi->kode ?? '-' }}</td>
            <td>{{ $m->debit }}</td>
            <td>{{ $m->kredit }}</td>
            <td>{{ number_format($m->saldo_hitung, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
