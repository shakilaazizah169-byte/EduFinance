@extends('layouts.app')

@section('title', 'Profile Details')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Profile Details</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Profile Details</li>
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
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="feather-edit me-2"></i>
                        <span>Edit Profile</span>
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
        @if(session('success'))
            <div class="alert alert-success-custom mb-4">
                <div class="d-flex align-items-center gap-3">
                    <i class="feather-check-circle fs-4 text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <div class="row g-4">
            <!-- Left Column - Profile Summary -->
            <div class="col-lg-4">
                <div class="card stat-card">
                    <div class="card-body text-center p-4">
                        <!-- Profile Photo -->
                        <div class="mb-4">
                            <div class="position-relative d-inline-block">
                                <div id="profilePreview" 
                                     style="background-image: url('{{ $user->getAvatarUrl() }}');
                                            width: 120px; 
                                            height: 120px; 
                                            border-radius: 50%; 
                                            background-size: cover; 
                                            background-position: center;
                                            border: 3px solid var(--primary-color);
                                            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                                            margin: 0 auto;">
                                </div>
                                <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-2 border border-white" 
                                      style="width: 18px; height: 18px;"></span>
                            </div>
                        </div>

                        <!-- Profile Info -->
                        <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-3">{{ $user->email }}</p>

                        <!-- Role Badge -->
                        <div class="mb-3">
                            <span class="badge badge-primary-light px-3 py-2">
                                <i class="feather-{{ $user->role === 'admin' ? 'user-check' : 'shield' }} me-1"></i>
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>

                        <!-- Account Status -->
                        <div class="d-flex justify-content-center gap-4 mt-4 pt-2 border-top">
                            <div class="text-center">
                                <div class="stat-label text-muted mb-1">Status</div>
                                <div class="fw-semibold text-success">
                                    <i class="feather-check-circle me-1"></i>Active
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="stat-label text-muted mb-1">Member Since</div>
                                <div class="fw-semibold">{{ $user->created_at->format('M Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Detailed Info -->
            <div class="col-lg-8">
                <div class="card table-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-user me-2"></i>Informasi Lengkap
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Nama Lengkap -->
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon bg-primary-soft text-primary">
                                        <i class="feather-user"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Nama Lengkap</div>
                                        <div class="info-value">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon bg-info-soft text-info">
                                        <i class="feather-mail"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Email</div>
                                        <div class="info-value">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Role -->
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon bg-warning-soft text-warning">
                                        <i class="feather-shield"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Role / Hak Akses</div>
                                        <div class="info-value">
                                            <span class="badge badge-primary-light">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Created -->
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon bg-success-soft text-success">
                                        <i class="feather-calendar"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Akun Dibuat</div>
                                        <div class="info-value">
                                            {{ $user->created_at->format('d F Y') }}
                                            <small class="text-muted ms-2">{{ $user->created_at->format('H:i') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Last Updated -->
                            <div class="col-md-12">
                                <div class="info-card">
                                    <div class="info-icon bg-secondary-soft text-secondary">
                                        <i class="feather-clock"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Terakhir Diperbarui</div>
                                        <div class="info-value">
                                            {{ $user->updated_at->format('d F Y') }}
                                            <small class="text-muted ms-2">{{ $user->updated_at->format('H:i') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="pagination-info">
                                <i class="feather-info me-1"></i>
                                Terakhir login: {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Belum pernah login' }}
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                    <i class="feather-edit me-2"></i>Edit Profile
                                </a>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="feather-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
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

.card-footer {
    background: transparent;
    border-top: 1px solid var(--border-color);
    padding: 1rem 1.5rem;
}

/* ============================================
   STATISTICS CARDS (untuk profile summary)
   ============================================ */
.stat-card .card-body {
    padding: 1.5rem;
}

.stat-card .border-top {
    border-top-color: var(--border-color) !important;
}

/* Badge */
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

/* ============================================
   INFO CARD STYLES
   ============================================ */
.info-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background-color: var(--bg-soft);
    border-radius: 0.75rem;
    transition: all 0.2s ease;
}

.info-card:hover {
    background-color: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transform: translateY(-2px);
}

.info-icon {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    flex-shrink: 0;
}

.info-icon i {
    font-size: 1.25rem;
}

.info-content {
    flex: 1;
}

.info-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: #2c3e50;
    word-break: break-word;
}

/* Soft Background Colors */
.bg-primary-soft { background-color: rgba(52, 84, 209, 0.1); }
.bg-success-soft { background-color: rgba(37, 176, 3, 0.1); }
.bg-info-soft { background-color: rgba(23, 162, 184, 0.1); }
.bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
.bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
.bg-secondary-soft { background-color: rgba(108, 117, 125, 0.1); }

/* ============================================
   ALERT STYLES
   ============================================ */
.alert-success-custom {
    background: linear-gradient(135deg, rgba(37, 176, 3, 0.08) 0%, rgba(37, 176, 3, 0.05) 100%);
    border: 1px solid rgba(37, 176, 3, 0.15);
    border-radius: 1rem;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.alert-success-custom .btn-close {
    font-size: 0.75rem;
}

/* ============================================
   BUTTON STYLES
   ============================================ */
.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border: none;
    border-radius: 0.625rem;
    padding: 0.625rem 1.25rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(52, 84, 209, 0.25);
}

.btn-outline-secondary {
    border-radius: 0.625rem;
    padding: 0.625rem 1.25rem;
    border-color: #e2e8f0;
    color: #6c757d;
    transition: all 0.2s ease;
}

.btn-outline-secondary:hover {
    background-color: var(--bg-soft);
    border-color: #cbd5e0;
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

/* ============================================
   PAGINATION INFO
   ============================================ */
.pagination-info {
    font-size: 0.75rem;
    color: #6c757d;
}

/* ============================================
   RESPONSIVE STYLES
   ============================================ */
@media (max-width: 768px) {
    .card-header {
        padding: 0.875rem 1rem;
        flex-direction: column;
        align-items: flex-start;
    }

    .card-footer {
        padding: 1rem;
    }

    .info-card {
        padding: 0.875rem;
    }

    .info-icon {
        width: 36px;
        height: 36px;
    }

    .info-icon i {
        font-size: 1rem;
    }

    .info-value {
        font-size: 0.8125rem;
    }
}

@media (max-width: 576px) {
    .stat-card .card-body {
        padding: 1rem;
    }

    .info-card {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .info-icon {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function refreshPage() {
    location.reload();
}

// SweetAlert Notifications
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
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

@if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        html: '<ul class="text-start mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
        confirmButtonText: 'OK'
    });
@endif
</script>
@endpush