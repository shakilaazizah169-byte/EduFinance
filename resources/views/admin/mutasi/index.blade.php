@extends('layouts.app')

@section('title', 'Mutasi Pemasukan')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Mutasi Pemasukan</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Mutasi Pemasukan</li>
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
                                <h2 class="stat-value text-success mb-1">Rp {{ number_format($totalDebit, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total Pemasukan</p>
                                <small class="text-muted">{{ number_format($totalTrx, 0, ',', '.') }} transaksi</small>
                            </div>
                            <div class="stat-icon bg-success-soft text-success">
                                <i class="feather-dollar-sign"></i>
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
                                <h2 class="stat-value text-primary mb-1">Rp {{ number_format($thisMonth, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Bulan Ini</p>
                                <small class="text-muted">Periode berjalan</small>
                            </div>
                            <div class="stat-icon bg-primary-soft text-primary">
                                <i class="feather-calendar"></i>
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
                                <h2 class="stat-value text-warning mb-1">{{ number_format($totalTrx, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total Transaksi</p>
                                <small class="text-muted">Semua waktu</small>
                            </div>
                            <div class="stat-icon bg-warning-soft text-warning">
                                <i class="feather-shopping-bag"></i>
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
                                <h2 class="stat-value text-info mb-1">Rp {{ number_format($saldoTerkini, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Saldo Terkini</p>
                                <small class="text-muted">Saldo kas saat ini</small>
                            </div>
                            <div class="stat-icon bg-info-soft text-info">
                                <i class="feather-credit-card"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- period info alert (added for consistency) -->
        @if(request()->hasAny(['tahun', 'bulan', 'paket', 'search']))
        <div class="alert alert-info-custom mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="feather-filter fs-4"></i>
                    <div>
                        <span class="fw-semibold">Filter aktif:</span>
                        @if(request('tahun')) Tahun: {{ request('tahun') }} @endif
                        @if(request('bulan')) Bulan: {{ $bulanList[request('bulan')] ?? '' }} @endif
                        @if(request('paket')) Paket: {{ ucfirst(request('paket')) }} @endif
                        @if(request('search')) Pencarian: "{{ request('search') }}" @endif
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge badge-primary-light">
                        <i class="feather-file-text me-1"></i>{{ number_format($mutasiList->total(), 0, ',', '.') }} Transaksi
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- filter card -->
        <div class="card filter-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="feather-filter me-2"></i>Filter Data Mutasi
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.mutasi.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">
                                <i class="feather-calendar me-1"></i>Tahun
                            </label>
                            <select name="tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach($tahunList as $th)
                                    <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>{{ $th }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">
                                <i class="feather-calendar me-1"></i>Bulan
                            </label>
                            <select name="bulan" class="form-select">
                                <option value="">Semua Bulan</option>
                                @foreach($bulanList as $k => $v)
                                    <option value="{{ $k }}" {{ request('bulan') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">
                                <i class="feather-tag me-1"></i>Paket
                            </label>
                            <select name="paket" class="form-select">
                                <option value="">Semua Paket</option>
                                <option value="monthly" {{ request('paket') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                <option value="yearly" {{ request('paket') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                                <option value="lifetime" {{ request('paket') == 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="feather-search me-1"></i>Pencarian
                            </label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Nama sekolah atau Order ID..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="feather-search me-1"></i>Tampilkan
                                </button>
                                <a href="{{ route('admin.mutasi.index') }}" class="btn btn-icon btn-outline-secondary" title="Reset Filter">
                                    <i class="feather-refresh-cw"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Filter (added for consistency) -->
                    <div class="quick-filter-wrapper mt-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="quick-filter-label">Quick Filter:</span>
                            <a href="javascript:void(0)" onclick="setQuickFilter('bulanIni')" class="quick-filter-btn">
                                <i class="feather-calendar me-1"></i>Bulan Ini
                            </a>
                            <a href="javascript:void(0)" onclick="setQuickFilter('bulanLalu')" class="quick-filter-btn">
                                <i class="feather-calendar me-1"></i>Bulan Lalu
                            </a>
                            <a href="javascript:void(0)" onclick="setQuickFilter('30Hari')" class="quick-filter-btn">
                                <i class="feather-clock me-1"></i>30 Hari
                            </a>
                            <a href="javascript:void(0)" onclick="setQuickFilter('7Hari')" class="quick-filter-btn">
                                <i class="feather-clock me-1"></i>7 Hari
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- table card -->
        <div class="card table-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="feather-list me-2"></i>Riwayat Mutasi Pemasukan
                </h5>
                <span class="badge badge-info-light">
                    <i class="feather-activity me-1"></i>Laporan Real-time
                </span>
            </div>
            <div class="card-body p-0">
                @if($mutasiList->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%" class="text-center">NO</th>
                                <th style="width: 12%">TANGGAL</th>
                                <th style="width: 13%">ORDER ID</th>
                                <th style="width: 20%">NAMA SEKOLAH</th>
                                <th style="width: 12%">PEMBELI</th>
                                <th style="width: 10%">PAKET</th>
                                <th style="width: 15%" class="text-end">DEBIT</th>
                                <th style="width: 13%" class="text-end">SALDO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mutasiList as $item)
                            <tr class="single-item align-middle">
                                <td class="text-center">{{ ($mutasiList->currentPage() - 1) * $mutasiList->perPage() + $loop->iteration }}</td>
                                <td>
                                    <div class="date-info">
                                        <span class="date-day">{{ $item->tanggal->translatedFormat('d M Y') }}</span>
                                        <span class="date-time">{{ $item->tanggal->translatedFormat('l') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <code class="fw-semibold text-primary">{{ $item->order_id }}</code>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $item->school_name ?? '—' }}</div>
                                    <small class="text-muted">{{ $item->buyer_email ?? '' }}</small>
                                </td>
                                <td class="text-muted">{{ $item->buyer_name ?? '—' }}</td>
                                <td>
                                    @php
                                        $packageClass = match($item->package_type) {
                                            'monthly' => 'badge-primary-light',
                                            'yearly' => 'badge-success-light',
                                            'lifetime' => 'badge-warning-light',
                                            default => 'badge-secondary-light'
                                        };
                                        $packageLabel = match($item->package_type) {
                                            'monthly' => 'Bulanan',
                                            'yearly' => 'Tahunan',
                                            'lifetime' => 'Lifetime',
                                            default => ucfirst($item->package_type)
                                        };
                                    @endphp
                                    <span class="badge {{ $packageClass }}">
                                        {{ $packageLabel }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-success">
                                        Rp {{ number_format($item->debit, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-primary">
                                        Rp {{ number_format($item->saldo, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-end fw-semibold">TOTAL HALAMAN INI:</td>
                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($mutasiList->sum('debit'), 0, ',', '.') }}
                                </td>
                                <td class="text-end">—</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- footer summary -->
                <div class="card-footer">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="pagination-info">
                            <i class="feather-calendar me-1"></i>
                            Menampilkan {{ $mutasiList->firstItem() }}–{{ $mutasiList->lastItem() }}
                            dari {{ $mutasiList->total() }} transaksi
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="exportPDF()">
                                <i class="feather-file-text me-1"></i>PDF
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="exportExcel()">
                                <i class="feather-file me-1"></i>Excel
                            </button>
                        </div>
                    </div>
                </div>

                @if($mutasiList->hasPages())
                <div class="card-footer border-top">
                    <div class="d-flex justify-content-center">
                        {{ $mutasiList->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif

                @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="feather-inbox"></i>
                    </div>
                    <h6 class="empty-state-title">Belum Ada Data Mutasi</h6>
                    <p class="empty-state-text">
                        Mutasi akan muncul otomatis saat ada pembayaran sukses dari user
                    </p>
                </div>
                @endif
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
   STATISTICS CARDS
   ============================================ */
.stat-card .card-body {
    padding: 1.25rem;
}

.stat-value {
    font-size: 1.25rem;
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

/* ============================================
   FILTER CARD STYLES
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
    width: 42px;
    height: 42px;
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

.btn-outline-danger, .btn-outline-success {
    border-radius: 0.5rem;
    padding: 0.375rem 0.875rem;
    font-size: 0.8125rem;
    transition: all 0.2s ease;
}

.btn-outline-danger:hover, .btn-outline-success:hover {
    transform: translateY(-1px);
}

/* Dropdown Button */
.dropdown-toggle.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
}

.dropdown-menu {
    border-radius: 0.75rem;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    padding: 0.5rem;
}

.dropdown-item {
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: var(--bg-soft);
}

.dropdown-item i {
    width: 20px;
}

/* Quick Filter */
.quick-filter-wrapper {
    padding-top: 1rem;
    margin-top: 0.5rem;
    border-top: 1px solid var(--border-color);
}

.quick-filter-label {
    font-size: 0.7rem;
    font-weight: 500;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.quick-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 1rem;
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 2rem;
    color: #495057;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
}

.quick-filter-btn:hover {
    background-color: #e9ecef;
    color: var(--primary-color);
    border-color: var(--primary-color);
    transform: translateY(-1px);
}

/* Alert Info Custom */
.alert-info-custom {
    background: linear-gradient(135deg, rgba(52, 84, 209, 0.05) 0%, rgba(30, 58, 138, 0.05) 100%);
    border: 1px solid rgba(52, 84, 209, 0.15);
    border-radius: 1rem;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
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

.table > tfoot > tr > td {
    background-color: var(--bg-soft);
    font-weight: 600;
    border-top: 2px solid var(--border-color);
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

.badge-info-light {
    background-color: rgba(23, 162, 184, 0.08);
    color: var(--info-color);
}

.badge-warning-light {
    background-color: rgba(255, 193, 7, 0.08);
    color: var(--warning-color);
}

.badge-secondary-light {
    background-color: rgba(108, 117, 125, 0.08);
    color: #6c757d;
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

/* Code styling */
code {
    background-color: rgba(52, 84, 209, 0.08);
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
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

    .quick-filter-btn {
        padding: 0.3rem 0.8rem;
        font-size: 0.7rem;
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

// ========================================
// QUICK FILTER
// ========================================
window.setQuickFilter = function(type) {
    let start = new Date();
    let end = new Date();
    
    switch(type) {
        case 'bulanIni':
            start = new Date(start.getFullYear(), start.getMonth(), 1);
            end = new Date();
            break;
        case 'bulanLalu':
            start = new Date(start.getFullYear(), start.getMonth() - 1, 1);
            end = new Date(start.getFullYear(), start.getMonth(), 0);
            break;
        case '30Hari':
            start.setDate(start.getDate() - 30);
            end = new Date();
            break;
        case '7Hari':
            start.setDate(start.getDate() - 7);
            end = new Date();
            break;
        default:
            return;
    }
    
    // Format dates to YYYY-MM-DD
    const startYear = start.getFullYear();
    const startMonth = String(start.getMonth() + 1).padStart(2, '0');
    const startDay = String(start.getDate()).padStart(2, '0');
    
    const endYear = end.getFullYear();
    const endMonth = String(end.getMonth() + 1).padStart(2, '0');
    const endDay = String(end.getDate()).padStart(2, '0');
    
    // Redirect with date parameters
    window.location.href = "{{ route('admin.mutasi.index') }}?tanggal_mulai=" + startYear + "-" + startMonth + "-" + startDay + "&tanggal_akhir=" + endYear + "-" + endMonth + "-" + endDay;
};

// ========================================
// EXPORT FUNCTIONS
// ========================================
function exportPDF() {
    let params = new URLSearchParams(window.location.search);
    window.open("{{ route('admin.mutasi.export.pdf') }}?" + params.toString(), '_blank');
}

function exportExcel() {
    let params = new URLSearchParams(window.location.search);
    window.open("{{ route('admin.mutasi.export.excel') }}?" + params.toString(), '_blank');
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
</script>
@endpush