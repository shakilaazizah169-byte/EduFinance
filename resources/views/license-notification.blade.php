@if(Auth::check() && Auth::user()->role !== 'super_admin')
    @php
        $user = Auth::user();
        $hasActive = $user->hasActiveLicense();
        $daysLeft = $user->licenseDaysLeft();
        $isExpired = $user->isLicenseExpired() && $user->lisensi_status !== 'never';
    @endphp

    @if(!$hasActive && !$isExpired)
        {{-- Belum punya lisensi --}}
        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
            <div class="container">
                <i class="feather-alert-triangle me-2"></i>
                <strong>Belum Memiliki Lisensi!</strong> 
                Anda belum memiliki lisensi aktif. Silakan beli lisensi untuk mengakses fitur lengkap.
                <a href="{{ route('pricing') }}" class="alert-link ms-2">Beli Lisensi →</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @elseif($isExpired)
        {{-- Lisensi expired --}}
        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
            <div class="container">
                <i class="feather-clock me-2"></i>
                <strong>Lisensi Telah Habis!</strong>
                Lisensi Anda telah berakhir. Anda hanya bisa melihat data, tidak bisa menambah/mengubah data.
                <a href="{{ route('pricing') }}" class="alert-link ms-2">Perpanjang Sekarang →</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @elseif($hasActive && $daysLeft <= 7)
        {{-- Lisensi akan segera habis --}}
        <div class="alert alert-warning alert-dismissible fade show mb-0 rounded-0" role="alert">
            <div class="container">
                <i class="feather-alert-circle me-2"></i>
                <strong>Peringatan!</strong>
                Lisensi Anda akan habis dalam <strong>{{ $daysLeft }} hari</strong> ({{ $user->lisensi_expired_at->format('d/m/Y') }}).
                <a href="{{ route('pricing') }}" class="alert-link ms-2">Perpanjang Sekarang →</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @elseif($hasActive && $daysLeft <= 30)
        {{-- Pengingat perpanjang --}}
        <div class="alert alert-info alert-dismissible fade show mb-0 rounded-0" role="alert">
            <div class="container">
                <i class="feather-info me-2"></i>
                <strong>Info Lisensi:</strong>
                Lisensi Anda akan habis dalam {{ $daysLeft }} hari.
                <a href="{{ route('pricing') }}" class="alert-link ms-2">Lihat Paket Perpanjangan</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    {{-- Floating badge untuk status --}}
    <div class="position-fixed bottom-0 end-0 m-3 z-3" style="z-index: 1050;">
        <div class="card shadow-sm border-0" style="background: rgba(52,84,209,0.95); backdrop-filter: blur(8px); min-width: 160px;">
            <div class="card-body p-2 text-center">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="feather-shield text-white fs-5"></i>
                    <div class="text-white small">
                        <strong>Status Lisensi</strong><br>
                        @if($hasActive)
                            <span class="badge bg-success">
                                <i class="feather-check-circle me-1"></i>AKTIF
                            </span>
                            <small class="d-block text-white-50">
                                Sisa: {{ $daysLeft }} hari
                            </small>
                        @elseif($isExpired)
                            <span class="badge bg-danger">
                                <i class="feather-x-circle me-1"></i>EXPIRED
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="feather-help-circle me-1"></i>BELUM
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif