@extends('layouts.app')

@section('title', 'Data Perencanaan')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10 mb-0">Perencanaan</h5>
            </div>
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Perencanaan</li>
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
                    <a href="{{ route('perencanaan.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Tambah Perencanaan</span>
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
                                <h2 class="stat-value text-primary mb-1">{{ number_format($perencanaan->total(), 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total Perencanaan</p>
                            </div>
                            <div class="stat-icon bg-primary-soft text-primary">
                                <i class="feather-layers"></i>
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
                                <h2 class="stat-value text-success mb-1">{{ number_format($totalRencana ?? 0, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total Rencana</p>
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
                                <h2 class="stat-value text-info mb-1">{{ number_format($totalDetail ?? 0, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Detail Rencana</p>
                            </div>
                            <div class="stat-icon bg-info-soft text-info">
                                <i class="feather-file-text"></i>
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
                                <h2 class="stat-value text-warning mb-1">{{ number_format($thisYearCount ?? 0, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Tahun Ini</p>
                            </div>
                            <div class="stat-icon bg-warning-soft text-warning">
                                <i class="feather-calendar"></i>
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
                    <i class="feather-filter me-2"></i>Filter Data
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('perencanaan.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach($years as $th)
                                    <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>{{ $th }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select">
                                <option value="">Semua Bulan</option>
                                @foreach($months as $k => $v)
                                    <option value="{{ $k }}" {{ request('bulan') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="feather-search me-1"></i>Cari
                                </button>
                                <a href="{{ route('perencanaan.index') }}" class="btn btn-outline-secondary px-3" title="Reset Filter">
                                    <i class="feather-refresh-cw"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Active Filters Badge -->
                    @if(request()->hasAny(['tahun', 'bulan']))
                    <div class="active-filters">
                        <span class="active-filters-label">Filter aktif:</span>
                        @if(request('tahun'))
                            <span class="filter-badge">
                                <i class="feather-calendar me-1"></i>Tahun {{ request('tahun') }}
                                <a href="{{ route('perencanaan.index', array_merge(request()->except('tahun'), ['page' => 1])) }}" class="filter-badge-remove">
                                    <i class="feather-x"></i>
                                </a>
                            </span>
                        @endif
                        @if(request('bulan'))
                            <span class="filter-badge">
                                <i class="feather-calendar me-1"></i>{{ $months[request('bulan')] ?? '' }}
                                <a href="{{ route('perencanaan.index', array_merge(request()->except('bulan'), ['page' => 1])) }}" class="filter-badge-remove">
                                    <i class="feather-x"></i>
                                </a>
                            </span>
                        @endif
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- table card -->
        <div class="card table-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="feather-list me-2"></i>Daftar Perencanaan
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                                <th style="width: 35%">JUDUL</th>
                                <th style="width: 20%">BULAN/TAHUN</th>
                                <th style="width: 20%">JUMLAH RENCANA</th>
                                <th style="width: 20%">DIBUAT</th>
                                <th style="width: 15%" class="text-end">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($perencanaan as $item)
                            @php
                                $monthName = \Carbon\Carbon::create()->month($item->bulan)->translatedFormat('F');
                                $detailCount = $item->details->count();
                            @endphp
                            <tr class="single-item align-middle">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="plan-icon">
                                            <i class="feather-folder"></i>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block plan-title">{{ $item->judul }}</span>
                                            @if($detailCount > 0)
                                                <small class="text-muted d-flex align-items-center">
                                                    <i class="feather-corner-down-right me-1" style="font-size: 10px;"></i>
                                                    {{ Str::limit($item->details->first()->perencanaan ?? 'Tidak ada detail', 40) }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-primary-light">
                                        <i class="feather-calendar me-1"></i>
                                        {{ $monthName }} {{ $item->tahun }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-success-light">
                                        <i class="feather-list me-1"></i>
                                        {{ number_format($detailCount) }} rencana
                                    </span>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <span class="date-day">{{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-' }}</span>
                                        <span class="date-time">{{ $item->created_at ? $item->created_at->format('H:i') : '' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('perencanaan.show', $item) }}"
                                           class="action-btn"
                                           data-bs-toggle="tooltip"
                                           title="Lihat Detail">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('perencanaan.edit', $item) }}"
                                           class="action-btn"
                                           data-bs-toggle="tooltip"
                                           title="Edit Perencanaan">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <button type="button"
                                                class="action-btn text-danger"
                                                data-bs-toggle="tooltip"
                                                title="Hapus Perencanaan"
                                                onclick="deletePerencanaan({{ $item->perencanaan_id }}, '{{ addslashes($item->judul) }}')">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $item->perencanaan_id }}"
                                          action="{{ route('perencanaan.destroy', $item) }}"
                                          method="POST"
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="feather-inbox"></i>
                                        </div>
                                        <h6 class="empty-state-title">Belum Ada Data</h6>
                                        <p class="empty-state-text">Belum ada data perencanaan yang tersedia</p>
                                        @if(!request()->hasAny(['tahun', 'bulan']))
                                        <a href="{{ route('perencanaan.create') }}" class="btn btn-primary mt-3">
                                            <i class="feather-plus me-2"></i>Tambah Perencanaan
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($perencanaan->hasPages())
            <div class="card-footer">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="pagination-info">
                        <i class="feather-info me-1"></i>
                        Menampilkan {{ $perencanaan->firstItem() }}–{{ $perencanaan->lastItem() }}
                        dari {{ $perencanaan->total() }} data
                    </div>
                    <div>
                        {{ $perencanaan->onEachSide(1)->links('pagination::bootstrap-5') }}
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
}

.card-header h5 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2c3e50;
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
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    transition: transform 0.2s ease;
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

.form-select {
    border-radius: 0.625rem;
    border: 1px solid #e2e8f0;
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background-color: #ffffff;
    cursor: pointer;
}

.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(52, 84, 209, 0.1);
    outline: none;
}

.form-select:hover {
    border-color: #cbd5e0;
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

/* Plan Icon */
.plan-icon {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(52, 84, 209, 0.1);
    border-radius: 0.75rem;
    color: var(--primary-color);
    transition: all 0.2s ease;
}

.plan-icon i {
    font-size: 1.25rem;
}

.plan-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #2c3e50;
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

/* Action Buttons */
.action-buttons {
    display: flex;
    align-items: center;
    justify-content: flex-end;
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

.action-btn.text-danger:hover {
    background-color: rgba(220, 53, 69, 0.1);
    color: var(--danger-color) !important;
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
        font-size: 1.25rem;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
    }

    .stat-icon i {
        font-size: 1.25rem;
    }

    .filter-card .card-body {
        padding: 1rem;
    }

    .card-header {
        padding: 0.875rem 1rem;
    }

    .table > thead > tr > th,
    .table > tbody > tr > td {
        padding: 0.75rem;
        font-size: 0.8125rem;
    }

    .action-btn {
        width: 28px;
        height: 28px;
    }

    .pagination-info {
        width: 100%;
        text-align: center;
    }

    .pagination {
        justify-content: center;
    }

    .plan-icon {
        width: 36px;
        height: 36px;
    }

    .plan-icon i {
        font-size: 1rem;
    }

    .plan-title {
        font-size: 0.8125rem;
    }
}

@media (max-width: 576px) {
    .page-header-title {
        border-right: none !important;
        margin-right: 0 !important;
        padding-right: 0 !important;
    }
    .page-header-title h5 {
        margin-bottom: 5px !important;
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

function deletePerencanaan(id, judul) {
    Swal.fire({
        title: 'Hapus Perencanaan?',
        html: `
            <div class="text-start">
                <p>Apakah Anda yakin ingin menghapus perencanaan ini?</p>
                <div class="alert alert-light border p-3 rounded-3">
                    <div class="mb-0">
                        <strong class="text-primary">Judul:</strong>
                        <span>${judul}</span>
                    </div>
                </div>
                <small class="text-muted">
                    <i class="feather-alert-triangle me-1"></i>
                    Semua detail rencana akan ikut terhapus. Data yang terhapus tidak dapat dikembalikan.
                </small>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="feather-trash-2 me-2"></i>Ya, Hapus!',
        cancelButtonText: '<i class="feather-x me-2"></i>Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
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
