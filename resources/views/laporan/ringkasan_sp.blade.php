@extends('layouts.app')

@section('title', 'Ringkasan Keuangan (Stored Procedure)')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5>Ringkasan Keuangan (Menggunakan Stored Procedure)</h5>
            <p class="text-muted mb-0">Tahun: {{ $tahun }}</p>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="feather-info me-2"></i>
                Laporan ini dihasilkan menggunakan <strong>Stored Procedure GetRingkasanKeuangan()</strong>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <canvas id="ringkasanChart" height="300"></canvas>
                </div>
                <div class="col-md-4">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Pemasukan</th>
                                    <th>Pengeluaran</th>
                                    <th>Surplus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ringkasan as $item)
                                @php
                                    $bulanNama = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                                                   'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'][$item->bulan-1];
                                @endphp
                                <tr>
                                    <td>{{ $bulanNama }}</td>
                                    <td class="text-end">{{ number_format($item->total_pemasukan, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($item->total_pengeluaran, 0, ',', '.') }}</td>
                                    <td class="text-end {{ $item->surplus >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($item->surplus, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('laporan.mutasi') }}" class="btn btn-secondary">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ringkasanChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($bulanLabels),
            datasets: [
                {
                    label: 'Pemasukan',
                    data: @json($pemasukanData),
                    backgroundColor: 'rgba(37, 176, 3, 0.5)',
                    borderColor: '#25B003',
                    borderWidth: 1
                },
                {
                    label: 'Pengeluaran',
                    data: @json($pengeluaranData),
                    backgroundColor: 'rgba(220, 53, 69, 0.5)',
                    borderColor: '#dc3545',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'Jt';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection