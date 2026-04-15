@extends('layouts.app')

@section('title', 'Laporan Mutasi Kas (Stored Procedure)')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5>Laporan Mutasi Kas (Menggunakan Stored Procedure)</h5>
            <p class="text-muted mb-0">
                Periode: Tahun {{ $tahun }} {{ $bulan > 0 ? '- ' . $namaBulan : '- Semua Bulan' }}
            </p>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="feather-info me-2"></i>
                Laporan ini dihasilkan menggunakan <strong>Stored Procedure GetLaporanMutasiKas()</strong>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Uraian</th>
                            <th>Debit (Rp)</th>
                            <th>Kredit (Rp)</th>
                            <th>Saldo (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $row)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $row->uraian }}</td>
                            <td class="text-end">{{ number_format($row->debit, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row->kredit, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row->saldo, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">TOTAL</th>
                            <th class="text-end">{{ number_format($totalDebit, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($totalKredit, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($saldoAkhir, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('laporan.mutasi') }}" class="btn btn-secondary">
                    ← Kembali ke Laporan Biasa
                </a>
            </div>
        </div>
    </div>
</div>
@endsection