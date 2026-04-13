@extends('layouts.app')

@section('title', 'Daftar Invoice')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Invoice</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Invoice</li>
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
                    <a href="{{ route('invoice.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Buat Invoice</span>
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
                                <h2 class="stat-value text-primary mb-1">{{ number_format($totalInvoice ?? $invoices->total(), 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total Invoice</p>
                            </div>
                            <div class="stat-icon bg-primary-soft text-primary">
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
                                <h2 class="stat-value text-success mb-1">Rp {{ number_format($totalNilaiInvoice ?? 0, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total Nilai Invoice</p>
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
                                <h2 class="stat-value text-warning mb-1">{{ number_format($totalBulanIni ?? 0, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Invoice Bulan Ini</p>
                            </div>
                            <div class="stat-icon bg-warning-soft text-warning">
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
                                <h2 class="stat-value text-info mb-1">{{ number_format($totalCustomer ?? 0, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total Customer</p>
                            </div>
                            <div class="stat-icon bg-info-soft text-info">
                                <i class="feather-users"></i>
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
                    <i class="feather-filter me-2"></i>Filter Periode & Pencarian
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('invoice.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="feather-calendar me-1"></i>Dari Tanggal
                            </label>
                            <div class="custom-date-input" id="startDateWrapper">
                                <input type="text" 
                                       name="date_from_display" 
                                       id="startDateDisplay"
                                       class="form-control date-display" 
                                       placeholder="Pilih tanggal mulai"
                                       value="{{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') : '' }}"
                                       readonly>
                                <input type="hidden" name="date_from" id="startDate" value="{{ request('date_from') ?? '' }}">
                                <i class="feather-calendar calendar-icon"></i>
                                
                                <!-- Custom Date Picker -->
                                <div class="custom-date-picker" id="startDatePicker">
                                    <div class="date-picker-header">
                                        <button type="button" class="month-nav" id="startPrevMonth">
                                            <i class="feather-chevron-left"></i>
                                        </button>
                                        <span class="month-year" id="startMonthYear"></span>
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
                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="feather-calendar me-1"></i>Sampai Tanggal
                            </label>
                            <div class="custom-date-input" id="endDateWrapper">
                                <input type="text" 
                                       name="date_to_display" 
                                       id="endDateDisplay"
                                       class="form-control date-display" 
                                       placeholder="Pilih tanggal akhir"
                                       value="{{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') : '' }}"
                                       readonly>
                                <input type="hidden" name="date_to" id="endDate" value="{{ request('date_to') ?? '' }}">
                                <i class="feather-calendar calendar-icon"></i>
                                
                                <!-- Custom Date Picker -->
                                <div class="custom-date-picker" id="endDatePicker">
                                    <div class="date-picker-header">
                                        <button type="button" class="month-nav" id="endPrevMonth">
                                            <i class="feather-chevron-left"></i>
                                        </button>
                                        <span class="month-year" id="endMonthYear"></span>
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
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="feather-search me-1"></i>Tampilkan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Filter -->
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

                    <!-- Search Field -->
                    <div class="search-field-wrapper mt-3">
                        <label class="form-label">
                            <i class="feather-search me-1"></i>Cari Invoice
                        </label>
                        <div class="search-wrapper">
                            <i class="feather-search search-icon"></i>
                            <input type="text" name="search" class="form-control search-input"
                                   value="{{ request('search') }}"
                                   placeholder="No. invoice / customer / email...">
                            @if(request('search'))
                                <a href="{{ route('invoice.index', array_merge(request()->except('search'), ['page' => 1])) }}" class="clear-search" title="Hapus pencarian">
                                    <i class="feather-x"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Active Filters Badge -->
                    @if(request()->hasAny(['date_from', 'date_to', 'search']))
                    <div class="active-filters mt-3">
                        <span class="active-filters-label">Filter aktif:</span>
                        @if(request('date_from'))
                            <span class="filter-badge">
                                <i class="feather-calendar me-1"></i>Dari: {{ \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') }}
                                <a href="{{ route('invoice.index', array_merge(request()->except('date_from'), ['page' => 1])) }}" class="filter-badge-remove">
                                    <i class="feather-x"></i>
                                </a>
                            </span>
                        @endif
                        @if(request('date_to'))
                            <span class="filter-badge">
                                <i class="feather-calendar me-1"></i>Sampai: {{ \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }}
                                <a href="{{ route('invoice.index', array_merge(request()->except('date_to'), ['page' => 1])) }}" class="filter-badge-remove">
                                    <i class="feather-x"></i>
                                </a>
                            </span>
                        @endif
                        @if(request('search'))
                            <span class="filter-badge">
                                <i class="feather-search me-1"></i>"{{ request('search') }}"
                                <a href="{{ route('invoice.index', array_merge(request()->except('search'), ['page' => 1])) }}" class="filter-badge-remove">
                                    <i class="feather-x"></i>
                                </a>
                            </span>
                        @endif
                        <a href="{{ route('invoice.index') }}" class="filter-badge bg-danger-soft text-danger">
                            <i class="feather-refresh-cw me-1"></i>Reset Semua
                        </a>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- table card -->
        <div class="card table-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="feather-list me-2"></i>Daftar Invoice
                </h5>
                @if(request()->hasAny(['date_from', 'date_to', 'search']))
                <span class="badge badge-info-light">
                    <i class="feather-filter me-1"></i>Hasil Filter
                </span>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%">NO</th>
                                <th style="width: 15%">NO. INVOICE</th>
                                <th style="width: 10%">TANGGAL</th>
                                <th style="width: 35%">TAGIHAN KEPADA</th>
                                <th style="width: 20%" class="text-end">TOTAL</th>
                                <th style="width: 15%" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $i => $inv)
                            <tr class="single-item align-middle">
                                <td class="text-muted">{{ $invoices->firstItem() + $i }}</td>
                                <td>
                                    <a href="{{ route('invoice.show', $inv->id) }}" 
                                       class="fw-semibold text-primary text-decoration-none">
                                        {{ $inv->invoice_number }}
                                    </a>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <span class="date-day">{{ \Carbon\Carbon::parse($inv->invoice_date)->format('d M Y') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="customer-icon bg-secondary-soft">
                                            <i class="feather-user"></i>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block customer-name">{{ $inv->bill_to_nama }}</span>
                                            @if($inv->bill_to_email)
                                                <small class="text-muted">{{ $inv->bill_to_email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end fw-bold">
                                    <span class="amount-value">Rp {{ number_format($inv->total, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons justify-content-center">
                                        <a href="{{ route('invoice.print', $inv->id) }}"
                                           class="action-btn"
                                           data-bs-toggle="tooltip"
                                           title="Cetak PDF">
                                            <i class="feather-printer"></i>
                                        </a>
                                        <a href="{{ route('invoice.show', $inv->id) }}"
                                           class="action-btn"
                                           data-bs-toggle="tooltip"
                                           title="Lihat Detail">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('invoice.edit', $inv->id) }}"
                                           class="action-btn"
                                           data-bs-toggle="tooltip"
                                           title="Edit Invoice">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <button type="button"
                                                class="action-btn text-danger"
                                                data-bs-toggle="tooltip"
                                                title="Hapus Invoice"
                                                onclick="deleteInvoice({{ $inv->id }}, '{{ addslashes($inv->invoice_number) }}')">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $inv->id }}"
                                          action="{{ route('invoice.destroy', $inv->id) }}"
                                          method="POST"
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="feather-file-text"></i>
                                        </div>
                                        <h6 class="empty-state-title">Belum Ada Data</h6>
                                        <p class="empty-state-text">Belum ada invoice yang tersedia</p>
                                        @if(!request()->hasAny(['date_from', 'date_to', 'search']))
                                        <a href="{{ route('invoice.create') }}" class="btn btn-primary mt-3">
                                            <i class="feather-plus me-2"></i>Buat Invoice
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

            @if($invoices->hasPages())
            <div class="card-footer">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="pagination-info">
                        <i class="feather-info me-1"></i>
                        Menampilkan {{ $invoices->firstItem() }}–{{ $invoices->lastItem() }}
                        dari {{ $invoices->total() }} data
                    </div>
                    <div>
                        {{ $invoices->onEachSide(1)->links('pagination::bootstrap-5') }}
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
.bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
.bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
.bg-info-soft { background-color: rgba(23, 162, 184, 0.1); }
.bg-secondary-soft { background-color: rgba(108, 117, 125, 0.1); }

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

/* ============================================
   CUSTOM DATE PICKER
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

/* Search Field */
.search-field-wrapper {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.search-wrapper {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1rem;
    color: #9ca3af;
    pointer-events: none;
}

.search-input {
    padding-left: 2.5rem;
    padding-right: 2.5rem;
}

.clear-search {
    position: absolute;
    right: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.clear-search:hover {
    background-color: #e2e8f0;
    color: var(--danger-color);
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
    text-decoration: none;
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

/* Customer Icon */
.customer-icon {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    transition: all 0.2s ease;
}

.customer-icon i {
    font-size: 1.25rem;
    color: #6c757d;
}

.customer-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #2c3e50;
}

/* Amount Value */
.amount-value {
    font-weight: 700;
    color: #2c3e50;
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

/* Badges */
.badge {
    font-weight: 500;
    font-size: 0.7rem;
    padding: 0.375rem 0.75rem;
    border-radius: 2rem;
}

.badge-info-light {
    background-color: rgba(23, 162, 184, 0.08);
    color: var(--info-color);
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
        flex-direction: column;
        align-items: flex-start;
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

    .customer-icon {
        width: 36px;
        height: 36px;
    }

    .customer-icon i {
        font-size: 1rem;
    }

    .customer-name {
        font-size: 0.8125rem;
    }

    .custom-date-picker {
        width: 280px;
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

    // ========================================
    // CUSTOM DATE PICKER
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
                startPicker.classList.add('show');
                renderStartCalendar();
            }
        });
    }
    
    if (endWrapper) {
        endWrapper.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-date-picker')) {
                closeAllPickers();
                endPicker.classList.add('show');
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
                startPicker.classList.remove('show');
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
                endPicker.classList.remove('show');
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
            startPicker.classList.remove('show');
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
            endPicker.classList.remove('show');
        });
    }
    
    // ========================================
    // DATE VALIDATION
    // ========================================
    const form = document.getElementById('filterForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            
            if (start && end && start > end) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Tanggal Tidak Valid',
                    text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir',
                    confirmButtonColor: '#3454D1'
                });
            }
        });
    }
});

// ========================================
// REFRESH PAGE
// ========================================
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
    
    // Format dates to DD/MM/YYYY for display
    const startDisplay = `${startDay}/${startMonth}/${startYear}`;
    const endDisplay = `${endDay}/${endMonth}/${endYear}`;
    
    // Update display
    const startDateDisplay = document.getElementById('startDateDisplay');
    const endDateDisplay = document.getElementById('endDateDisplay');
    if (startDateDisplay) startDateDisplay.value = startDisplay;
    if (endDateDisplay) endDateDisplay.value = endDisplay;
    
    // Update hidden inputs
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    if (startDateInput) startDateInput.value = `${startYear}-${startMonth}-${startDay}`;
    if (endDateInput) endDateInput.value = `${endYear}-${endMonth}-${endDay}`;
    
    // Submit form
    const filterForm = document.getElementById('filterForm');
    if (filterForm) filterForm.submit();
};

// ========================================
// DELETE INVOICE
// ========================================
function deleteInvoice(id, invoiceNumber) {
    Swal.fire({
        title: 'Hapus Invoice?',
        html: `
            <div class="text-start">
                <p>Apakah Anda yakin ingin menghapus invoice ini?</p>
                <div class="alert alert-light border p-3 rounded-3">
                    <div class="mb-0">
                        <strong class="text-primary">No. Invoice:</strong>
                        <span>${invoiceNumber}</span>
                    </div>
                </div>
                <small class="text-muted">
                    <i class="feather-alert-triangle me-1"></i>
                    Data yang terhapus tidak dapat dikembalikan.
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
</script>
@endpush