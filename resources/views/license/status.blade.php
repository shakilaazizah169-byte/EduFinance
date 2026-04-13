@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Status Lisensi</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-{{ $licenseStatus['status_color'] }} text-center">
                        <h5>
                            Status: <strong>{{ $licenseStatus['status_label'] }}</strong>
                        </h5>
                        @if($licenseStatus['is_active'])
                            <p>Lisensi aktif hingga: {{ \Carbon\Carbon::parse($licenseStatus['expired_at'])->format('d/m/Y') }}</p>
                            <p>Sisa: <strong>{{ $licenseStatus['days_left'] }} hari</strong></p>
                        @elseif($licenseStatus['status'] !== 'never')
                            <p>Lisensi telah berakhir pada: {{ \Carbon\Carbon::parse($licenseStatus['expired_at'])->format('d/m/Y') }}</p>
                            <a href="{{ route('pricing') }}" class="btn btn-primary">Perpanjang Lisensi</a>
                        @else
                            <a href="{{ route('pricing') }}" class="btn btn-primary">Beli Lisensi</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Riwayat Pembelian</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Paket</th>
                                <th>Durasi</th>
                                <th>Harga</th>
                                <th>Berlaku hingga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->purchased_at->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($transaction->package_type) }}</td>
                                <td>{{ $transaction->license_duration_days }} hari</td>
                                <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                <td>{{ $transaction->expired_at?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada riwayat pembelian</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection