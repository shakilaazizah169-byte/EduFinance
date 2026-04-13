@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Detail User</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Manajemen User</a></li>
                <li class="breadcrumb-item active">Detail User</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Back</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" onclick="refreshPage()" data-bs-toggle="tooltip" title="Refresh Halaman">
                        <i class="feather-refresh-cw"></i>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- main content -->
    <div class="main-content">
        <!-- alert messages -->
        @if(session('success'))
            <div class="alert alert-success-custom mb-4">
                <div class="d-flex align-items-center gap-3">
                    <i class="feather-check-circle fs-4 text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning-custom mb-4">
                <div class="d-flex align-items-center gap-3">
                    <i class="feather-alert-triangle fs-4 text-warning"></i>
                    <div>{{ session('warning') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger-custom mb-4">
                <div class="d-flex align-items-center gap-3">
                    <i class="feather-alert-circle fs-4 text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <!-- user header card -->
        <div class="card table-card mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <div class="user-avatar bg-primary-soft text-primary">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-3 flex-wrap mb-2">
                            <h4 class="fw-bold mb-0">{{ $user->name }}</h4>
                            @php
                                $latest = $user->licenses->first();
                                if (!$latest) {
                                    $statusLabel = 'Belum Beli';
                                    $statusColor = 'secondary';
                                    $statusIcon = 'help-circle';
                                } elseif ($latest->status === 'suspended') {
                                    $statusLabel = 'Suspended';
                                    $statusColor = 'danger';
                                    $statusIcon = 'slash';
                                } elseif ($latest->status === 'expired' || ($latest->end_date && $latest->end_date < now())) {
                                    $statusLabel = 'Expired';
                                    $statusColor = 'warning';
                                    $statusIcon = 'alert-circle';
                                } else {
                                    $statusLabel = 'Aktif';
                                    $statusColor = 'success';
                                    $statusIcon = 'check-circle';
                                }
                            @endphp
                            <span class="badge badge-{{ $statusColor }}-light">
                                <i class="feather-{{ $statusIcon }} me-1"></i>
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-4 text-muted">
                            <span><i class="feather-mail me-1 text-primary"></i>{{ $user->email }}</span>
                            @if($user->phone)
                                <span><i class="feather-phone me-1 text-primary"></i>{{ $user->phone }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- kolom kiri -->
            <div class="col-lg-4">
                <!-- informasi sekolah -->
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-home me-2 text-primary"></i>Informasi Instansi
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="info-list">
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="feather-building"></i>
                                    Nama Instansi
                                </div>
                                <div class="info-value">{{ $user->school_name ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="feather-calendar"></i>
                                    Akun Dibuat
                                </div>
                                <div class="info-value">
                                    <div>{{ $user->created_at->translatedFormat('d M Y') }}</div>
                                    <small class="text-muted">{{ $user->created_at->format('H:i') }} WIB</small>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="feather-shield"></i>
                                    Role / Hak Akses
                                </div>
                                <div class="info-value">
                                    <span class="badge badge-primary-light">
                                        <i class="feather-{{ $user->role === 'admin' ? 'user' : 'shield' }} me-1"></i>
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- aksi admin -->
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-settings me-2 text-primary"></i>Aksi Admin
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            @if($latest && $latest->status !== 'suspended')
                                <button type="button"
                                        class="btn btn-outline-danger w-100"
                                        onclick="confirmSuspend({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                    <i class="feather-slash me-2"></i>Suspend User
                                </button>
                                <form id="suspend-form-{{ $user->id }}"
                                      action="{{ route('admin.users.suspend', $user->id) }}"
                                      method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            @endif

                            @if($latest && $latest->status === 'suspended')
                                <button type="button"
                                        class="btn btn-outline-success w-100"
                                        onclick="confirmActivate({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                    <i class="feather-check-circle me-2"></i>Aktifkan User
                                </button>
                                <form id="activate-form-{{ $user->id }}"
                                      action="{{ route('admin.users.activate', $user->id) }}"
                                      method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            @endif

                            @if($latest)
                                <a href="{{ route('admin.users.invoice', [$user->id, $latest->id]) }}"
                                   class="btn btn-outline-primary w-100"
                                   target="_blank">
                                    <i class="feather-file-text me-2"></i>Cetak Invoice Terbaru
                                </a>
                            @endif
                        </div>

                        <div class="info-card-custom mt-3">
                            <div class="d-flex align-items-start gap-2">
                                <i class="feather-info text-primary"></i>
                                <small class="text-muted">Tindakan suspend akan memblokir akses login user.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- kolom kanan -->
            <div class="col-lg-8">
                <!-- riwayat lisensi -->
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-key me-2 text-primary"></i>Riwayat Lisensi
                        </h5>
                        <span class="badge badge-primary-light">
                            <i class="feather-key me-1"></i>{{ $user->licenses->count() }} lisensi
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>KODE LISENSI</th>
                                        <th>PAKET</th>
                                        <th>HARGA</th>
                                        <th>MASA BERLAKU</th>
                                        <th>STATUS</th>
                                        <th class="text-center">INVOICE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->licenses as $lic)
                                        @php
                                            if ($lic->status === 'suspended') {
                                                $lsLabel = 'Suspended';
                                                $lsColor = 'danger';
                                                $lsIcon = 'slash';
                                            } elseif ($lic->status === 'expired' || ($lic->end_date && $lic->end_date < now())) {
                                                $lsLabel = 'Expired';
                                                $lsColor = 'warning';
                                                $lsIcon = 'alert-circle';
                                            } else {
                                                $lsLabel = 'Aktif';
                                                $lsColor = 'success';
                                                $lsIcon = 'check-circle';
                                            }
                                            
                                            $packageLabel = match($lic->package_type) {
                                                'monthly' => 'Bulanan',
                                                'yearly' => 'Tahunan',
                                                'lifetime' => 'Lifetime',
                                                default => ucfirst($lic->package_type)
                                            };
                                        @endphp
                                        <tr class="single-item align-middle">
                                            <td>
                                                <code class="license-code">{{ $lic->license_key }}</code>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary-light">{{ $packageLabel }}</span>
                                            </td>
                                            <td class="fw-semibold">Rp {{ number_format($lic->price, 0, ',', '.') }}</td>
                                            <td>
                                                <div class="small">{{ \Carbon\Carbon::parse($lic->start_date)->format('d/m/Y') }}</div>
                                                <div class="small text-muted">→ {{ \Carbon\Carbon::parse($lic->end_date)->format('d/m/Y') }}</div>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $lsColor }}-light">
                                                    <i class="feather-{{ $lsIcon }} me-1"></i>
                                                    {{ $lsLabel }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.users.invoice', [$user->id, $lic->id]) }}"
                                                   class="action-btn"
                                                   target="_blank"
                                                   data-bs-toggle="tooltip"
                                                   title="Lihat Invoice">
                                                    <i class="feather-file-text"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon">
                                                        <i class="feather-key"></i>
                                                    </div>
                                                    <h6 class="empty-state-title">Belum Ada Lisensi</h6>
                                                    <p class="empty-state-text">User belum memiliki lisensi</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- riwayat pembayaran -->
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-credit-card me-2 text-primary"></i>Riwayat Pembayaran
                        </h5>
                        <span class="badge badge-primary-light">
                            <i class="feather-credit-card me-1"></i>{{ $user->payments->count() }} transaksi
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ORDER ID</th>
                                        <th>PAKET</th>
                                        <th>TOTAL</th>
                                        <th>METODE</th>
                                        <th>TANGGAL</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->payments as $pay)
                                        @php
                                            $statusColor = match($pay->status) {
                                                'success' => 'success',
                                                'pending' => 'warning',
                                                'failed' => 'danger',
                                                'expired' => 'secondary',
                                                default => 'info'
                                            };
                                            $statusIcon = match($pay->status) {
                                                'success' => 'check-circle',
                                                'pending' => 'clock',
                                                'failed' => 'x-circle',
                                                'expired' => 'alert-circle',
                                                default => 'info'
                                            };
                                            $statusLabel = match($pay->status) {
                                                'success' => 'Berhasil',
                                                'pending' => 'Pending',
                                                'failed' => 'Gagal',
                                                'expired' => 'Expired',
                                                default => ucfirst($pay->status)
                                            };
                                        @endphp
                                        <tr class="single-item align-middle">
                                            <td>
                                                <code class="license-code">{{ $pay->order_id }}</code>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary-light">{{ ucfirst($pay->package_type) }}</span>
                                            </td>
                                            <td class="fw-semibold">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                                            <td>{{ strtoupper($pay->payment_type ?? '-') }}</td>
                                            <td>
                                                <div class="date-info">
                                                    <span class="date-day">{{ $pay->created_at->translatedFormat('d M Y') }}</span>
                                                    <span class="date-time">{{ $pay->created_at->format('H:i') }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $statusColor }}-light">
                                                    <i class="feather-{{ $statusIcon }} me-1"></i>
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon">
                                                        <i class="feather-credit-card"></i>
                                                    </div>
                                                    <h6 class="empty-state-title">Belum Ada Transaksi</h6>
                                                    <p class="empty-state-text">User belum melakukan pembayaran</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- informasi waktu -->
                <div class="card table-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-clock me-2 text-primary"></i>Informasi Waktu
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="info-card-custom">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <i class="feather-plus-circle text-primary"></i>
                                <span>Akun dibuat: <strong>{{ $user->created_at->translatedFormat('d M Y, H:i') }}</strong></span>
                            </div>
                            @if($user->updated_at->ne($user->created_at))
                            <div class="d-flex align-items-center gap-3">
                                <i class="feather-edit-3 text-primary"></i>
                                <span>Terakhir diperbarui: <strong>{{ $user->updated_at->translatedFormat('d M Y, H:i') }}</strong></span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ============================================
   VARIABLES
   ============================================ */
:root {
    --primary-color: #3454D1;
    --primary-dark: #1e3a8a;
    --success-color: #25B003;
    --info-color: #17a2b8;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --border-color: #e9ecef;
    --bg-soft: #f8f9fa;
}

/* ============================================
   CARD STYLES
   ============================================ */
.card {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.2s ease;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.card-header {
    background: transparent;
    border-bottom: 1px solid var(--border-color);
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.card-header h5 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0;
}

.card-body {
    padding: 1.5rem;
}

/* User Avatar */
.user-avatar {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 1rem;
    font-size: 2rem;
    font-weight: 600;
}

/* Info List */
.info-list {
    padding: 0;
}

.info-item {
    display: flex;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    width: 130px;
    min-width: 130px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-label i {
    font-size: 14px;
    color: var(--primary-color);
}

.info-value {
    flex: 1;
    font-size: 0.875rem;
    font-weight: 500;
    color: #2c3e50;
}

/* License Code */
.license-code {
    background-color: rgba(52, 84, 209, 0.08);
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-family: monospace;
    font-size: 0.75rem;
    color: var(--primary-color);
}

/* Info Card Custom */
.info-card-custom {
    background-color: var(--bg-soft);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
}

/* Buttons */
.btn-outline-primary {
    border-radius: 0.625rem;
    padding: 0.625rem 1rem;
    border: 1px solid #e2e8f0;
    color: var(--primary-color);
    transition: all 0.2s ease;
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border-color: transparent;
    color: white;
    transform: translateY(-1px);
}

.btn-outline-danger {
    border-radius: 0.625rem;
    padding: 0.625rem 1rem;
    border: 1px solid #e2e8f0;
    color: var(--danger-color);
    transition: all 0.2s ease;
}

.btn-outline-danger:hover {
    background: var(--danger-color);
    border-color: transparent;
    color: white;
    transform: translateY(-1px);
}

.btn-outline-success {
    border-radius: 0.625rem;
    padding: 0.625rem 1rem;
    border: 1px solid #e2e8f0;
    color: var(--success-color);
    transition: all 0.2s ease;
}

.btn-outline-success:hover {
    background: var(--success-color);
    border-color: transparent;
    color: white;
    transform: translateY(-1px);
}

.btn-icon {
    width: 38px;
    height: 38px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.625rem;
}

.btn-light-brand {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    color: #495057;
}

.btn-light-brand:hover {
    background-color: #e9ecef;
    color: var(--primary-color);
}

/* Action Buttons */
.action-btn {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
    color: #6c757d;
    background: transparent;
    border: none;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
}

.action-btn:hover {
    background-color: rgba(52, 84, 209, 0.1);
    color: var(--primary-color);
    transform: translateY(-1px);
}

/* Badges */
.badge {
    font-weight: 500;
    font-size: 0.7rem;
    padding: 0.375rem 0.75rem;
    border-radius: 2rem;
}

.badge-primary-light {
    background-color: rgba(52, 84, 209, 0.08);
    color: var(--primary-color);
}

.badge-success-light {
    background-color: rgba(37, 176, 3, 0.08);
    color: var(--success-color);
}

.badge-danger-light {
    background-color: rgba(220, 53, 69, 0.08);
    color: var(--danger-color);
}

.badge-warning-light {
    background-color: rgba(255, 193, 7, 0.08);
    color: var(--warning-color);
}

.badge-secondary-light {
    background-color: rgba(108, 117, 125, 0.08);
    color: #6c757d;
}

/* Alert Custom */
.alert-success-custom {
    background: linear-gradient(135deg, rgba(37, 176, 3, 0.08) 0%, rgba(37, 176, 3, 0.05) 100%);
    border: 1px solid rgba(37, 176, 3, 0.15);
    border-radius: 1rem;
    padding: 1rem 1.25rem;
}

.alert-warning-custom {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.08) 0%, rgba(255, 193, 7, 0.05) 100%);
    border: 1px solid rgba(255, 193, 7, 0.15);
    border-radius: 1rem;
    padding: 1rem 1.25rem;
}

.alert-danger-custom {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.08) 0%, rgba(220, 53, 69, 0.05) 100%);
    border: 1px solid rgba(220, 53, 69, 0.15);
    border-radius: 1rem;
    padding: 1rem 1.25rem;
}

/* Date Info */
.date-info {
    display: flex;
    flex-direction: column;
}

.date-day {
    font-size: 0.875rem;
    font-weight: 500;
    color: #2c3e50;
}

.date-time {
    font-size: 0.7rem;
    color: #9ca3af;
}

/* Soft Background */
.bg-primary-soft { background-color: rgba(52, 84, 209, 0.1); }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 2rem;
}

.empty-state-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--bg-soft);
    border-radius: 1rem;
    color: #9ca3af;
}

.empty-state-icon i {
    font-size: 1.5rem;
}

.empty-state-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.empty-state-text {
    font-size: 0.75rem;
    color: #9ca3af;
    margin-bottom: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .card-header {
        padding: 0.875rem 1rem;
        flex-direction: column;
        align-items: flex-start;
    }

    .info-item {
        flex-direction: column;
        gap: 0.5rem;
    }

    .info-label {
        width: 100%;
    }

    .user-avatar {
        width: 55px;
        height: 55px;
        font-size: 1.5rem;
    }

    .table > thead > tr > th,
    .table > tbody > tr > td {
        padding: 0.75rem;
        font-size: 0.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function refreshPage() {
    location.reload();
}

function confirmSuspend(id, userName) {
    Swal.fire({
        title: 'Suspend User?',
        html: `
            <div class="text-start">
                <p>Apakah Anda yakin ingin suspend user ini?</p>
                <div class="alert alert-light border p-3 rounded-3">
                    <div class="mb-0">
                        <strong class="text-primary">User:</strong>
                        <span>${userName}</span>
                    </div>
                </div>
                <small class="text-muted">
                    <i class="feather-alert-triangle me-1"></i>
                    User tidak dapat mengakses sistem sampai diaktifkan kembali.
                </small>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="feather-slash me-2"></i>Ya, Suspend!',
        cancelButtonText: '<i class="feather-x me-2"></i>Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('suspend-form-' + id).submit();
        }
    });
}

function confirmActivate(id, userName) {
    Swal.fire({
        title: 'Aktifkan User?',
        html: `
            <div class="text-start">
                <p>Apakah Anda yakin ingin mengaktifkan kembali user ini?</p>
                <div class="alert alert-light border p-3 rounded-3">
                    <div class="mb-0">
                        <strong class="text-primary">User:</strong>
                        <span>${userName}</span>
                    </div>
                </div>
                <small class="text-muted">
                    <i class="feather-check-circle me-1"></i>
                    User dapat kembali mengakses sistem.
                </small>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#25B003',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="feather-check me-2"></i>Ya, Aktifkan!',
        cancelButtonText: '<i class="feather-x me-2"></i>Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('activate-form-' + id).submit();
        }
    });
}

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
@endif

@if(session('warning'))
    Swal.fire({
        icon: 'warning',
        title: 'Perhatian',
        text: '{{ session('warning') }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK'
    });
@endif
</script>
@endpush