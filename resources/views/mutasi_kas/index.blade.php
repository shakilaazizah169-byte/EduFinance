@extends('layouts.app')

@section('title', 'Mutasi Kas')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10 mb-0">Mutasi Kas</h5>
            </div>
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Mutasi Kas</li>
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
                    <a href="{{ route('mutasi-kas.create') }}" class="btn btn-primary d-flex align-items-center">
                        <i class="feather-plus ms-0 ms-sm-1"></i>
                        <span class="d-none d-sm-inline ms-2">Tambah Transaksi</span>
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
                                <p class="stat-label text-muted mb-0">Total Debit</p>
                            </div>
                            <div class="stat-icon bg-success-soft text-success">
                                <i class="feather-trending-up"></i>
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
                                <h2 class="stat-value text-danger mb-1">Rp {{ number_format($totalKredit, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total Kredit</p>
                            </div>
                            <div class="stat-icon bg-danger-soft text-danger">
                                <i class="feather-trending-down"></i>
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
                                <h2 class="stat-value text-primary mb-1">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Saldo Akhir</p>
                            </div>
                            <div class="stat-icon bg-primary-soft text-primary">
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
                                <h2 class="stat-value text-info mb-1">{{ number_format($mutasi->total(), 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total Transaksi</p>
                            </div>
                            <div class="stat-icon bg-info-soft text-info">
                                <i class="feather-file-text"></i>
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
                <form action="{{ route('mutasi-kas.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Jenis Filter</label>
                            <select name="filter_type" class="form-select" id="filterType">
                                <option value="bulan" {{ request('filter_type') == 'bulan' || !request('filter_type') ? 'selected' : '' }}>Bulan/Tahun</option>
                                <option value="periode" {{ request('filter_type') == 'periode' ? 'selected' : '' }}>Periode Tanggal</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="bulanFilterGroup">
                            <label class="form-label">Bulan</label>
                            <select name="month" class="form-select">
                                <option value="">Semua Bulan</option>
                                @foreach($months as $k => $v)
                                    <option value="{{ $k }}" {{ request('month') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2" id="tahunFilterGroup">
                            <label class="form-label">Tahun</label>
                            <select name="year" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach($years as $th)
                                    <option value="{{ $th }}" {{ request('year') == $th ? 'selected' : '' }}>{{ $th }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="periodeFilterGroup" style="display: none;">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <div class="custom-date-input" id="startDateWrapper">
                                        <input type="text" 
                                               name="start_date_display" 
                                               id="startDateDisplay"
                                               class="form-control date-display" 
                                               placeholder="Pilih tanggal mulai"
                                               value="{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : '' }}"
                                               readonly>
                                        <input type="hidden" name="start_date" id="startDate" value="{{ request('start_date') ?? '' }}">
                                        <i class="feather-calendar calendar-icon"></i>
                                        
                                        <!-- Custom Date Picker -->
                                        <div class="custom-date-picker" id="startDatePicker">
                                            <div class="date-picker-header">
                                                <button type="button" class="month-nav" id="startPrevMonth">
                                                    <i class="feather-chevron-left"></i>
                                                </button>
                                                <span class="month-year" id="startMonthYear">February 2026</span>
                                                <button type="button" class="month-nav" id="startNextMonth">
                                                    <i class="feather-chevron-right"></i>
                                                </button>
                                            </div>
                                            <div class="date-picker-weekdays">
                                                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                                            </div>
                                            <div class="date-picker-days" id="startDays"></div>
                                            <div class="date-picker-footer">
                                                <button type="button" class="btn-clear" id="startClear">Clear</button>
                                                <button type="button" class="btn-today" id="startToday">Today</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Tanggal Akhir</label>
                                    <div class="custom-date-input" id="endDateWrapper">
                                        <input type="text" 
                                               name="end_date_display" 
                                               id="endDateDisplay"
                                               class="form-control date-display" 
                                               placeholder="Pilih tanggal akhir"
                                               value="{{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : '' }}"
                                               readonly>
                                        <input type="hidden" name="end_date" id="endDate" value="{{ request('end_date') ?? '' }}">
                                        <i class="feather-calendar calendar-icon"></i>
                                        
                                        <!-- Custom Date Picker -->
                                        <div class="custom-date-picker" id="endDatePicker">
                                            <div class="date-picker-header">
                                                <button type="button" class="month-nav" id="endPrevMonth">
                                                    <i class="feather-chevron-left"></i>
                                                </button>
                                                <span class="month-year" id="endMonthYear">February 2026</span>
                                                <button type="button" class="month-nav" id="endNextMonth">
                                                    <i class="feather-chevron-right"></i>
                                                </button>
                                            </div>
                                            <div class="date-picker-weekdays">
                                                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                                            </div>
                                            <div class="date-picker-days" id="endDays"></div>
                                            <div class="date-picker-footer">
                                                <button type="button" class="btn-clear" id="endClear">Clear</button>
                                                <button type="button" class="btn-today" id="endToday">Today</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="feather-search me-1"></i>Cari
                                </button>
                                <a href="{{ route('mutasi-kas.index') }}" class="btn btn-outline-secondary px-3" title="Reset Filter">
                                    <i class="feather-refresh-cw"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Active Filters Badge -->
                    @if(request()->hasAny(['month', 'year', 'start_date', 'end_date']))
                    <div class="active-filters">
                        <span class="active-filters-label">Filter aktif:</span>
                        @if(request('month'))
                            <span class="filter-badge">
                                <i class="feather-calendar me-1"></i>Bulan: {{ $months[request('month')] ?? '' }}
                                <a href="{{ route('mutasi-kas.index', array_merge(request()->except('month'), ['page' => 1])) }}" class="filter-badge-remove">
                                    <i class="feather-x"></i>
                                </a>
                            </span>
                        @endif
                        @if(request('year'))
                            <span class="filter-badge">
                                <i class="feather-calendar me-1"></i>Tahun: {{ request('year') }}
                                <a href="{{ route('mutasi-kas.index', array_merge(request()->except('year'), ['page' => 1])) }}" class="filter-badge-remove">
                                    <i class="feather-x"></i>
                                </a>
                            </span>
                        @endif
                        @if(request('start_date'))
                            <span class="filter-badge">
                                <i class="feather-calendar me-1"></i>Mulai: {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}
                                <a href="{{ route('mutasi-kas.index', array_merge(request()->except('start_date'), ['page' => 1])) }}" class="filter-badge-remove">
                                    <i class="feather-x"></i>
                                </a>
                            </span>
                        @endif
                        @if(request('end_date'))
                            <span class="filter-badge">
                                <i class="feather-calendar me-1"></i>Akhir: {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                                <a href="{{ route('mutasi-kas.index', array_merge(request()->except('end_date'), ['page' => 1])) }}" class="filter-badge-remove">
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
                    <i class="feather-list me-2"></i>Daftar Transaksi Kas
                </h5>
                @if($mutasi->count())
                <div class="d-flex gap-2">
                    <span class="badge bg-soft-success text-success">
                        <i class="feather-arrow-up-circle me-1"></i>Debit: {{ $mutasi->where('debit', '>', 0)->count() }}
                    </span>
                    <span class="badge bg-soft-danger text-danger">
                        <i class="feather-arrow-down-circle me-1"></i>Kredit: {{ $mutasi->where('kredit', '>', 0)->count() }}
                    </span>
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 12%">TANGGAL</th>
                                <th style="width: 12%">KODE</th>
                                <th style="width: 28%">URAIAN</th>
                                <th style="width: 15%" class="text-end">DEBIT</th>
                                <th style="width: 15%" class="text-end">KREDIT</th>
                                <th style="width: 18%" class="text-end">SALDO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mutasi as $m)
                            <tr class="single-item align-middle">
                                <td>
                                    <div class="date-info">
                                        <span class="date-day">{{ $m->tanggal->translatedFormat('d M Y') }}</span>
                                        <span class="date-time">{{ $m->tanggal->translatedFormat('l') }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($m->kodeTransaksi)
                                    <span class="badge badge-primary-light">
                                        <i class="feather-hash me-1"></i>{{ $m->kodeTransaksi->kode }}
                                    </span>
                                    @else
                                    <span class="badge badge-secondary-light">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $m->uraian }}</div>
                                    @if($m->kodeTransaksi && $m->kodeTransaksi->keterangan)
                                    <small class="text-muted">{{ $m->kodeTransaksi->keterangan }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($m->debit > 0)
                                    <span class="fw-bold text-success">
                                        Rp {{ number_format($m->debit, 0, ',', '.') }}
                                    </span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($m->kredit > 0)
                                    <span class="fw-bold text-danger">
                                        Rp {{ number_format($m->kredit, 0, ',', '.') }}
                                    </span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-primary">
                                        Rp {{ number_format($m->saldo, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="feather-inbox"></i>
                                        </div>
                                        <h6 class="empty-state-title">Belum Ada Data</h6>
                                        <p class="empty-state-text">Belum ada data transaksi yang tersedia</p>
                                        @if(!request()->hasAny(['month', 'year', 'start_date', 'end_date']))
                                        <a href="{{ route('mutasi-kas.create') }}" class="btn btn-primary mt-3">
                                            <i class="feather-plus me-2"></i>Tambah Transaksi
                                        </a>
                                        @else
                                        <a href="{{ route('mutasi-kas.index') }}" class="btn btn-primary mt-3">
                                            <i class="feather-refresh-cw me-2"></i>Reset Filter
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($mutasi->count() && ($totalDebit > 0 || $totalKredit > 0))
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold py-3">TOTAL:</td>
                                <td class="text-end fw-bold text-success py-3">
                                    Rp {{ number_format($totalDebit, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-danger py-3">
                                    Rp {{ number_format($totalKredit, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-primary py-3">
                                    Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            @if($mutasi->hasPages())
            <div class="card-footer">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="pagination-info">
                        <i class="feather-info me-1"></i>
                        Menampilkan {{ $mutasi->firstItem() }}–{{ $mutasi->lastItem() }}
                        dari {{ $mutasi->total() }} data
                    </div>
                    <div>
                        {{ $mutasi->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
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

/* Badge soft colors */
.badge.bg-soft-success {
    background-color: rgba(37, 176, 3, 0.1);
    color: #25B003;
    font-weight: 500;
    padding: 0.375rem 0.75rem;
    border-radius: 2rem;
}

.badge.bg-soft-danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    font-weight: 500;
    padding: 0.375rem 0.75rem;
    border-radius: 2rem;
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
   CUSTOM DATE PICKER STYLES (SAMA DENGAN LAPORAN)
   ============================================ */
.custom-date-input {
    position: relative;
    width: 100%;
}

.custom-date-input .date-display {
    background-color: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.625rem;
    padding: 0.625rem 2.5rem 0.625rem 0.875rem;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.custom-date-input .date-display:hover {
    border-color: var(--primary-color);
    background-color: #f8fafc;
}

.custom-date-input .calendar-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
}

.custom-date-input:hover .calendar-icon {
    color: var(--primary-color);
}

.custom-date-picker {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    width: 320px;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    padding: 1rem;
    display: none;
    animation: slideDown 0.2s ease;
}

.custom-date-picker.show {
    display: block;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.date-picker-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.month-nav {
    width: 32px;
    height: 32px;
    border: none;
    background: #f8f9fa;
    border-radius: 0.5rem;
    color: #495057;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.month-nav:hover {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
}

.month-year {
    font-weight: 600;
    font-size: 0.875rem;
    color: #2c3e50;
}

.date-picker-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    margin-bottom: 0.5rem;
}

.date-picker-weekdays span {
    font-size: 0.7rem;
    font-weight: 600;
    color: #9ca3af;
    padding: 0.5rem 0;
}

.date-picker-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}

.date-picker-days button {
    aspect-ratio: 1;
    border: none;
    background: transparent;
    font-size: 0.75rem;
    color: #2c3e50;
    cursor: pointer;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
}

.date-picker-days button:hover:not(.empty) {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
}

.date-picker-days button.today {
    background-color: #e9ecef;
    font-weight: 700;
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.date-picker-days button.selected {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
}

.date-picker-days button.empty {
    pointer-events: none;
    color: #cbd5e0;
}

.date-picker-days button.prev-month,
.date-picker-days button.next-month {
    color: #cbd5e0;
}

.date-picker-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.btn-clear, .btn-today {
    padding: 0.375rem 0.875rem;
    border: none;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-clear {
    background: #f8f9fa;
    color: #6c757d;
}

.btn-clear:hover {
    background: #e9ecef;
    color: var(--danger-color);
}

.btn-today {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
}

.btn-today:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(52, 84, 209, 0.3);
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
        font-size: 1.1rem;
        line-height: 1.4;
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
        white-space: nowrap;
    }

    .pagination-info {
        width: 100%;
        text-align: center;
        margin-bottom: 0.5rem;
    }

    .pagination {
        justify-content: center;
        flex-wrap: wrap;
    }

    .custom-date-picker {
        width: 260px;
        left: 50%;
        transform: translateX(-50%);
    }

    .date-picker-days button {
        font-size: 0.7rem;
    }
}

@media (max-width: 576px) {
    .stat-value {
        font-size: 0.95rem;
    }

    .page-header-title {
        border-right: none !important;
        margin-right: 0 !important;
        padding-right: 0 !important;
    }

    .page-header-title h5 {
        margin-bottom: 5px !important;
    }
    
    .card-header .badge {
        font-size: 0.65rem;
    }

    .table-responsive {
        border-radius: 0;
        margin: 0 -1rem;
        width: calc(100% + 2rem);
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

    // ========================================
    // CUSTOM DATE PICKER (SAMA DENGAN LAPORAN)
    // ========================================
    
    // Month names
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    
    // Current dates
    let startCurrentDate = new Date();
    let endCurrentDate = new Date();
    
    // Selected dates
    let startSelectedDate = document.getElementById('startDate').value ? new Date(document.getElementById('startDate').value) : null;
    let endSelectedDate = document.getElementById('endDate').value ? new Date(document.getElementById('endDate').value) : null;
    
    // Initialize if values exist
    if (startSelectedDate) {
        startCurrentDate = new Date(startSelectedDate);
    }
    if (endSelectedDate) {
        endCurrentDate = new Date(endSelectedDate);
    }
    
    // DOM Elements
    const startWrapper = document.getElementById('startDateWrapper');
    const endWrapper = document.getElementById('endDateWrapper');
    const startPicker = document.getElementById('startDatePicker');
    const endPicker = document.getElementById('endDatePicker');
    const startDisplay = document.getElementById('startDateDisplay');
    const endDisplay = document.getElementById('endDateDisplay');
    
    // Toggle date pickers
    if (startWrapper) {
        startWrapper.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-date-picker')) {
                closeAllPickers();
                if (startPicker) startPicker.classList.add('show');
                renderStartCalendar();
            }
        });
    }
    
    if (endWrapper) {
        endWrapper.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-date-picker')) {
                closeAllPickers();
                if (endPicker) endPicker.classList.add('show');
                renderEndCalendar();
            }
        });
    }
    
    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-date-input')) {
            closeAllPickers();
        }
    });
    
    function closeAllPickers() {
        if (startPicker) startPicker.classList.remove('show');
        if (endPicker) endPicker.classList.remove('show');
    }
    
    // Render Start Calendar
    function renderStartCalendar() {
        const year = startCurrentDate.getFullYear();
        const month = startCurrentDate.getMonth();
        
        const startMonthYear = document.getElementById('startMonthYear');
        if (startMonthYear) startMonthYear.textContent = `${monthNames[month]} ${year}`;
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevMonthDays = new Date(year, month, 0).getDate();
        
        let html = '';
        
        // Previous month days
        for (let i = firstDay; i > 0; i--) {
            const day = prevMonthDays - i + 1;
            html += `<button type="button" class="prev-month" data-date="${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}">${day}</button>`;
        }
        
        // Current month days
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const isToday = isSameDay(date, new Date());
            const isSelected = startSelectedDate && isSameDay(date, startSelectedDate);
            
            let classes = '';
            if (isToday) classes += ' today';
            if (isSelected) classes += ' selected';
            
            html += `<button type="button" class="${classes}" data-date="${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}">${day}</button>`;
        }
        
        // Next month days
        const totalDays = firstDay + daysInMonth;
        const remainingCells = 42 - totalDays;
        for (let day = 1; day <= remainingCells; day++) {
            html += `<button type="button" class="next-month" data-date="${year}-${String(month+2).padStart(2,'0')}-${String(day).padStart(2,'0')}">${day}</button>`;
        }
        
        const startDays = document.getElementById('startDays');
        if (startDays) startDays.innerHTML = html;
        
        // Add click handlers
        document.querySelectorAll('#startDays button').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const dateStr = this.dataset.date;
                const selectedDate = new Date(dateStr);
                
                document.querySelectorAll('#startDays button').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                
                startSelectedDate = selectedDate;
                
                const day = String(selectedDate.getDate()).padStart(2, '0');
                const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
                const year = selectedDate.getFullYear();
                startDisplay.value = `${day}/${month}/${year}`;
                document.getElementById('startDate').value = `${year}-${month}-${day}`;
                
                startCurrentDate = new Date(selectedDate);
                if (startPicker) startPicker.classList.remove('show');
            });
        });
    }
    
    // Render End Calendar
    function renderEndCalendar() {
        const year = endCurrentDate.getFullYear();
        const month = endCurrentDate.getMonth();
        
        const endMonthYear = document.getElementById('endMonthYear');
        if (endMonthYear) endMonthYear.textContent = `${monthNames[month]} ${year}`;
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevMonthDays = new Date(year, month, 0).getDate();
        
        let html = '';
        
        for (let i = firstDay; i > 0; i--) {
            const day = prevMonthDays - i + 1;
            html += `<button type="button" class="prev-month" data-date="${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}">${day}</button>`;
        }
        
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const isToday = isSameDay(date, new Date());
            const isSelected = endSelectedDate && isSameDay(date, endSelectedDate);
            
            let classes = '';
            if (isToday) classes += ' today';
            if (isSelected) classes += ' selected';
            
            html += `<button type="button" class="${classes}" data-date="${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}">${day}</button>`;
        }
        
        const totalDays = firstDay + daysInMonth;
        const remainingCells = 42 - totalDays;
        for (let day = 1; day <= remainingCells; day++) {
            html += `<button type="button" class="next-month" data-date="${year}-${String(month+2).padStart(2,'0')}-${String(day).padStart(2,'0')}">${day}</button>`;
        }
        
        const endDays = document.getElementById('endDays');
        if (endDays) endDays.innerHTML = html;
        
        document.querySelectorAll('#endDays button').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const dateStr = this.dataset.date;
                const selectedDate = new Date(dateStr);
                
                document.querySelectorAll('#endDays button').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                
                endSelectedDate = selectedDate;
                
                const day = String(selectedDate.getDate()).padStart(2, '0');
                const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
                const year = selectedDate.getFullYear();
                endDisplay.value = `${day}/${month}/${year}`;
                document.getElementById('endDate').value = `${year}-${month}-${day}`;
                
                endCurrentDate = new Date(selectedDate);
                if (endPicker) endPicker.classList.remove('show');
            });
        });
    }
    
    // Helper function
    function isSameDay(date1, date2) {
        return date1.getDate() === date2.getDate() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getFullYear() === date2.getFullYear();
    }
    
    // Month navigation
    const startPrevMonth = document.getElementById('startPrevMonth');
    const startNextMonth = document.getElementById('startNextMonth');
    const endPrevMonth = document.getElementById('endPrevMonth');
    const endNextMonth = document.getElementById('endNextMonth');
    
    if (startPrevMonth) {
        startPrevMonth.addEventListener('click', function(e) {
            e.stopPropagation();
            startCurrentDate.setMonth(startCurrentDate.getMonth() - 1);
            renderStartCalendar();
        });
    }
    
    if (startNextMonth) {
        startNextMonth.addEventListener('click', function(e) {
            e.stopPropagation();
            startCurrentDate.setMonth(startCurrentDate.getMonth() + 1);
            renderStartCalendar();
        });
    }
    
    if (endPrevMonth) {
        endPrevMonth.addEventListener('click', function(e) {
            e.stopPropagation();
            endCurrentDate.setMonth(endCurrentDate.getMonth() - 1);
            renderEndCalendar();
        });
    }
    
    if (endNextMonth) {
        endNextMonth.addEventListener('click', function(e) {
            e.stopPropagation();
            endCurrentDate.setMonth(endCurrentDate.getMonth() + 1);
            renderEndCalendar();
        });
    }
    
    // Clear buttons
    const startClear = document.getElementById('startClear');
    const endClear = document.getElementById('endClear');
    
    if (startClear) {
        startClear.addEventListener('click', function(e) {
            e.stopPropagation();
            startDisplay.value = '';
            document.getElementById('startDate').value = '';
            startSelectedDate = null;
            renderStartCalendar();
        });
    }
    
    if (endClear) {
        endClear.addEventListener('click', function(e) {
            e.stopPropagation();
            endDisplay.value = '';
            document.getElementById('endDate').value = '';
            endSelectedDate = null;
            renderEndCalendar();
        });
    }
    
    // Today buttons
    const startToday = document.getElementById('startToday');
    const endToday = document.getElementById('endToday');
    
    if (startToday) {
        startToday.addEventListener('click', function(e) {
            e.stopPropagation();
            const today = new Date();
            startCurrentDate = new Date(today);
            startSelectedDate = new Date(today);
            
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            startDisplay.value = `${day}/${month}/${year}`;
            document.getElementById('startDate').value = `${year}-${month}-${day}`;
            
            renderStartCalendar();
            if (startPicker) startPicker.classList.remove('show');
        });
    }
    
    if (endToday) {
        endToday.addEventListener('click', function(e) {
            e.stopPropagation();
            const today = new Date();
            endCurrentDate = new Date(today);
            endSelectedDate = new Date(today);
            
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            endDisplay.value = `${day}/${month}/${year}`;
            document.getElementById('endDate').value = `${year}-${month}-${day}`;
            
            renderEndCalendar();
            if (endPicker) endPicker.classList.remove('show');
        });
    }

    // Filter type toggle
    const filterType = document.getElementById('filterType');
    const bulanFilterGroup = document.getElementById('bulanFilterGroup');
    const tahunFilterGroup = document.getElementById('tahunFilterGroup');
    const periodeFilterGroup = document.getElementById('periodeFilterGroup');

    function toggleFilterGroups() {
        if (filterType.value === 'periode') {
            if (bulanFilterGroup) bulanFilterGroup.style.display = 'none';
            if (tahunFilterGroup) tahunFilterGroup.style.display = 'none';
            if (periodeFilterGroup) periodeFilterGroup.style.display = 'block';
        } else {
            if (bulanFilterGroup) bulanFilterGroup.style.display = 'block';
            if (tahunFilterGroup) tahunFilterGroup.style.display = 'block';
            if (periodeFilterGroup) periodeFilterGroup.style.display = 'none';
        }
    }

    if (filterType) {
        filterType.addEventListener('change', toggleFilterGroups);
        toggleFilterGroups();
    }

    // Handle long numbers in stat cards
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(el => {
        const text = el.innerText;
        if (text.length > 18) {
            el.style.fontSize = '1.25rem';
        }
    });
});

function refreshPage() {
    location.reload();
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

@if(session('warning'))
    Swal.fire({
        icon: 'warning',
        title: 'Perhatian',
        text: '{{ session('warning') }}',
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