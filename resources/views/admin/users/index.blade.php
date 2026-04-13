@extends('layouts.app')

@section('title', 'Manajemen User Instansi')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Manajemen User Instansi</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Manajemen User</li>
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
        <!-- statistics cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <h2 class="stat-value text-primary mb-1">{{ number_format($stats['total'], 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total User</p>
                            </div>
                            <div class="stat-icon bg-primary-soft text-primary">
                                <i class="feather-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <h2 class="stat-value text-success mb-1">{{ number_format($stats['active'], 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Lisensi Aktif</p>
                            </div>
                            <div class="stat-icon bg-success-soft text-success">
                                <i class="feather-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <h2 class="stat-value text-warning mb-1">{{ number_format($stats['expired'], 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Expired</p>
                            </div>
                            <div class="stat-icon bg-warning-soft text-warning">
                                <i class="feather-alert-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <h2 class="stat-value text-info mb-1">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total Pendapatan</p>
                            </div>
                            <div class="stat-icon bg-info-soft text-info">
                                <i class="feather-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- filter card -->
        <div class="card filter-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="feather-filter me-2"></i>Filter User
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Pencarian</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="feather-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0"
                                       placeholder="Cari nama, email, atau sekolah..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status Lisensi</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="feather-search me-1"></i>Cari
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-3" title="Reset Filter">
                                    <i class="feather-refresh-cw"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Active Filters Badge -->
                    @if(request()->hasAny(['search', 'status']))
                    <div class="active-filters">
                        <span class="active-filters-label">Filter aktif:</span>
                        @if(request('search'))
                            <span class="filter-badge">
                                <i class="feather-search me-1"></i>Cari: {{ request('search') }}
                                <a href="{{ route('admin.users.index', array_merge(request()->except('search'), ['page' => 1])) }}" class="filter-badge-remove">
                                    <i class="feather-x"></i>
                                </a>
                            </span>
                        @endif
                        @if(request('status'))
                            <span class="filter-badge">
                                <i class="feather-tag me-1"></i>Status: {{ request('status') == 'active' ? 'Aktif' : (request('status') == 'expired' ? 'Expired' : 'Suspended') }}
                                <a href="{{ route('admin.users.index', array_merge(request()->except('status'), ['page' => 1])) }}" class="filter-badge-remove">
                                    <i class="feather-x"></i>
                                </a>
                            </span>
                        @endif
                    </div>
                    @endif
                </form>
            </div>
        </div>

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

        <!-- table card -->
        <div class="card table-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="feather-list me-2"></i>Daftar User Instansi
                </h5>
                <span class="badge badge-primary-light">
                    <i class="feather-users me-1"></i>{{ number_format($users->total(), 0, ',', '.') }} user
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 20%">USER / SEKOLAH</th>
                                <th style="width: 18%">KONTAK</th>
                                <th style="width: 12%">PAKET</th>
                                <th style="width: 18%">MASA BERLAKU</th>
                                <th style="width: 12%">STATUS</th>
                                <th style="width: 15%" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                @php
                                    $license = $user->licenses->first();
                                    if (! $license) {
                                        $statusLabel = 'Belum Beli';
                                        $statusColor = 'secondary';
                                        $statusIcon = 'help-circle';
                                    } elseif ($license->status === 'suspended') {
                                        $statusLabel = 'Suspended';
                                        $statusColor = 'danger';
                                        $statusIcon = 'slash';
                                    } elseif ($license->status === 'expired' || ($license->end_date && $license->end_date < now())) {
                                        $statusLabel = 'Expired';
                                        $statusColor = 'warning';
                                        $statusIcon = 'alert-circle';
                                    } else {
                                        $statusLabel = 'Aktif';
                                        $statusColor = 'success';
                                        $statusIcon = 'check-circle';
                                    }

                                    $packageLabel = match($license->package_type ?? null) {
                                        'monthly' => 'Bulanan',
                                        'yearly' => 'Tahunan',
                                        'lifetime' => 'Lifetime',
                                        default => '-'
                                    };
                                    $packageClass = match($license->package_type ?? null) {
                                        'monthly' => 'badge-primary-light',
                                        'yearly' => 'badge-success-light',
                                        'lifetime' => 'badge-warning-light',
                                        default => 'badge-secondary-light'
                                    };
                                @endphp
                                <tr class="single-item align-middle">
                                    <td class="text-center text-muted">
                                        {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-initial bg-primary-soft text-primary">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $user->name }}</div>
                                                <small class="text-muted">
                                                    <i class="feather-home me-1"></i>{{ $user->school_name ?? '-' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="small">
                                                <i class="feather-mail me-1 text-muted"></i>{{ $user->email }}
                                            </div>
                                            @if($user->phone)
                                                <div class="small text-muted">
                                                    <i class="feather-phone me-1"></i>{{ $user->phone }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($license)
                                            <span class="badge {{ $packageClass }}">
                                                {{ $packageLabel }}
                                            </span>
                                            <div class="small text-muted mt-1">
                                                Rp {{ number_format($license->price, 0, ',', '.') }}
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($license)
                                            <div>
                                                <div class="small">
                                                    {{ \Carbon\Carbon::parse($license->start_date)->format('d/m/Y') }}
                                                </div>
                                                <div class="small text-muted">
                                                    s/d {{ \Carbon\Carbon::parse($license->end_date)->format('d/m/Y') }}
                                                </div>
                                                @if($statusLabel === 'Aktif' && $license->end_date)
                                                    <div class="small text-success">
                                                        <i class="feather-clock me-1"></i>
                                                        {{ \Carbon\Carbon::now()->diffInDays($license->end_date) }} hari lagi
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $statusColor }}-light">
                                            <i class="feather-{{ $statusIcon }} me-1"></i>
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons justify-content-center">
                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                               class="action-btn"
                                               data-bs-toggle="tooltip"
                                               title="Detail User">
                                                <i class="feather-eye"></i>
                                            </a>

                                            @if($license && $license->status !== 'suspended')
                                                <button type="button"
                                                        class="action-btn text-warning"
                                                        data-bs-toggle="tooltip"
                                                        title="Suspend User"
                                                        onclick="confirmSuspend({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                                    <i class="feather-slash"></i>
                                                </button>
                                                <form id="suspend-form-{{ $user->id }}"
                                                      action="{{ route('admin.users.suspend', $user->id) }}"
                                                      method="POST"
                                                      style="display: none;">
                                                    @csrf
                                                </form>
                                            @elseif($license && $license->status === 'suspended')
                                                <button type="button"
                                                        class="action-btn text-success"
                                                        data-bs-toggle="tooltip"
                                                        title="Aktifkan User"
                                                        onclick="confirmActivate({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                                    <i class="feather-check"></i>
                                                </button>
                                                <form id="activate-form-{{ $user->id }}"
                                                      action="{{ route('admin.users.activate', $user->id) }}"
                                                      method="POST"
                                                      style="display: none;">
                                                    @csrf
                                                </form>
                                            @endif

                                            @if($license)
                                                <a href="{{ route('admin.users.invoice', [$user->id, $license->id]) }}"
                                                   class="action-btn"
                                                   data-bs-toggle="tooltip"
                                                   title="Lihat Invoice"
                                                   target="_blank">
                                                    <i class="feather-file-text"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-state-icon">
                                                <i class="feather-users"></i>
                                            </div>
                                            <h6 class="empty-state-title">Tidak Ada Data User</h6>
                                            <p class="empty-state-text">
                                                Tidak ada data user yang ditemukan sesuai filter
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($users->hasPages())
            <div class="card-footer">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="pagination-info">
                        <i class="feather-info me-1"></i>
                        Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }}
                        dari {{ $users->total() }} data
                    </div>
                    <div>
                        {{ $users->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
            @endif
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
   STATISTICS CARDS
   ============================================ */
.stat-card .card-body {
    padding: 1.25rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1.3;
    word-break: break-word;
    white-space: normal;
}

.stat-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.25rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    transition: transform 0.2s ease;
    flex-shrink: 0;
}

.stat-card:hover .stat-icon {
    transform: scale(1.05);
}

.stat-icon i {
    font-size: 1.5rem;
}

/* Soft Background Colors */
.bg-primary-soft { background-color: rgba(52, 84, 209, 0.1); }
.bg-success-soft { background-color: rgba(37, 176, 3, 0.1); }
.bg-info-soft { background-color: rgba(23, 162, 184, 0.1); }
.bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
.bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }

/* Avatar Initial */
.avatar-initial {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 1rem;
    flex-shrink: 0;
}

/* ============================================
   FILTER STYLES
   ============================================ */
.filter-card {
    margin-bottom: 1.5rem;
}

.filter-card .card-body {
    padding: 1.5rem;
}

.form-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #6c757d;
    margin-bottom: 0.5rem;
    display: block;
}

.form-select, .form-control {
    border-radius: 0.625rem;
    border: 1px solid #e2e8f0;
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background-color: #ffffff;
}

.form-select:focus, .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(52, 84, 209, 0.1);
    outline: none;
}

.form-select:hover, .form-control:hover {
    border-color: #cbd5e0;
}

.input-group-text {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-right: none;
    border-radius: 0.625rem 0 0 0.625rem;
}

.input-group .form-control {
    border-left: none;
    border-radius: 0 0.625rem 0.625rem 0;
}

/* Buttons */
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
    padding: 0.625rem 1rem;
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

/* Active Filters */
.active-filters {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.75rem;
    margin-top: 1.25rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--border-color);
}

.active-filters-label {
    font-size: 0.7rem;
    font-weight: 500;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.75rem;
    background-color: #f1f5f9;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 500;
    color: #1e293b;
}

.filter-badge i {
    font-size: 0.75rem;
}

.filter-badge-remove {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background-color: #cbd5e1;
    color: white;
    text-decoration: none;
    transition: all 0.2s ease;
}

.filter-badge-remove i {
    font-size: 0.625rem;
}

.filter-badge-remove:hover {
    background-color: var(--danger-color);
    transform: scale(1.1);
}

/* ============================================
   ALERT STYLES
   ============================================ */
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

.alert-success-custom .btn-close,
.alert-warning-custom .btn-close,
.alert-danger-custom .btn-close {
    font-size: 0.75rem;
}

/* ============================================
   TABLE STYLES
   ============================================ */
.table-card {
    overflow: hidden;
}

.table {
    margin-bottom: 0;
}

.table > thead > tr > th {
    background-color: var(--bg-soft);
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.table > tbody > tr > td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--border-color);
}

.table > tbody > tr:hover {
    background-color: rgba(52, 84, 209, 0.02);
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

/* Action Buttons */
.action-buttons {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

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

.action-btn.text-success:hover {
    background-color: rgba(37, 176, 3, 0.1);
    color: var(--success-color) !important;
}

.action-btn.text-warning:hover {
    background-color: rgba(255, 193, 7, 0.1);
    color: var(--warning-color) !important;
}

/* ============================================
   PAGINATION STYLES
   ============================================ */
.pagination-info {
    font-size: 0.75rem;
    color: #6c757d;
}

.pagination {
    margin-bottom: 0;
    gap: 0.25rem;
}

.page-link {
    border: 1px solid var(--border-color);
    border-radius: 0.5rem !important;
    padding: 0.5rem 0.875rem;
    font-size: 0.8125rem;
    font-weight: 500;
    color: #4a5568;
    background-color: white;
    transition: all 0.2s ease;
}

.page-link:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    transform: translateY(-1px);
}

.page-item.active .page-link {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border-color: var(--primary-color);
    color: white;
}

.page-item.disabled .page-link {
    color: #cbd5e0;
    background-color: #f8fafc;
}

/* ============================================
   EMPTY STATE STYLES
   ============================================ */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--bg-soft);
    border-radius: 2rem;
    color: #9ca3af;
}

.empty-state-icon i {
    font-size: 2.5rem;
}

.empty-state-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.empty-state-text {
    font-size: 0.875rem;
    color: #9ca3af;
    margin-bottom: 0;
}

/* ============================================
   RESPONSIVE STYLES
   ============================================ */
@media (max-width: 768px) {
    .stat-card .card-body {
        padding: 1rem;
    }

    .stat-value {
        font-size: 1rem;
        line-height: 1.2;
    }

    .stat-icon {
        width: 36px;
        height: 36px;
    }

    .stat-icon i {
        font-size: 1rem;
    }

    .stat-label {
        font-size: 0.6rem;
    }

    .filter-card .card-body {
        padding: 1rem;
    }

    .card-header {
        padding: 0.875rem 1rem;
        flex-direction: column;
        align-items: flex-start;
    }

    .table > thead > tr > th,
    .table > tbody > tr > td {
        padding: 0.75rem;
        font-size: 0.8125rem;
    }

    .pagination-info {
        width: 100%;
        text-align: center;
    }

    .pagination {
        justify-content: center;
    }

    .action-buttons {
        gap: 0.25rem;
    }

    .action-btn {
        width: 28px;
        height: 28px;
    }

    .avatar-initial {
        width: 32px;
        height: 32px;
        font-size: 0.875rem;
    }
}

@media (max-width: 576px) {
    .stat-value {
        font-size: 0.875rem;
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
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="feather-slash me-2"></i>Ya, Suspend!',
        cancelButtonText: '<i class="feather-x me-2"></i>Batal',
        reverseButtons: true
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
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="feather-check me-2"></i>Ya, Aktifkan!',
        cancelButtonText: '<i class="feather-x me-2"></i>Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('activate-form-' + id).submit();
        }
    });
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

@if(session('warning'))
    Swal.fire({
        icon: 'warning',
        title: 'Perhatian',
        text: '{{ session('warning') }}',
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
</script>
@endpush