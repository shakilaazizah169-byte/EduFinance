@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1>Pilih Paket Lisensi</h1>
            <p class="lead">Pilih paket yang sesuai dengan kebutuhan Anda</p>
            
            @if(isset($currentLicense))
                <div class="alert alert-{{ $currentLicense['status_color'] }} mt-3">
                    <strong>Status Lisensi Saat Ini:</strong>
                    {{ $currentLicense['status_label'] }}
                    @if($currentLicense['is_active'])
                        (Sisa {{ $currentLicense['days_left'] }} hari)
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        @foreach($packages as $type => $package)
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $package['name'] }}</h3>
                    <h2 class="text-primary">Rp {{ number_format($package['price'], 0, ',', '.') }}</h2>
                    <p class="text-muted">{{ $package['duration_days'] }} hari</p>
                    
                    <form action="{{ route('license.purchase') }}" method="POST">
                        @csrf
                        <input type="hidden" name="package_type" value="{{ $type }}">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            Beli Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection