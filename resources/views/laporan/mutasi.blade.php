@extends('layouts.app')

@section('title', 'Laporan Mutasi Kas')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Laporan Mutasi Kas</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Laporan Mutasi Kas</li>
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
                    <a href="{{ route('laporan.mutasi') }}" class="btn btn-icon btn-light-brand" onclick="refreshPage()" data-bs-toggle="tooltip" title="Refresh Halaman">
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
        <!-- filter card -->
        <div class="card filter-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="feather-filter me-2"></i>Filter Periode Laporan
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('laporan.mutasi') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="feather-calendar me-1"></i>Dari Tanggal
                            </label>
                            <div class="custom-date-input" id="startDateWrapper">
                                <input type="text" 
                                       name="tanggal_mulai_display" 
                                       id="startDateDisplay"
                                       class="form-control date-display" 
                                       placeholder="Pilih tanggal mulai"
                                       value="{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '' }}"
                                       readonly>
                                <input type="hidden" name="tanggal_mulai" id="startDate" value="{{ $startDate ?? '' }}">
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
                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="feather-calendar me-1"></i>Sampai Tanggal
                            </label>
                            <div class="custom-date-input" id="endDateWrapper">
                                <input type="text" 
                                       name="tanggal_akhir_display" 
                                       id="endDateDisplay"
                                       class="form-control date-display" 
                                       placeholder="Pilih tanggal akhir"
                                       value="{{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '' }}"
                                       readonly>
                                <input type="hidden" name="tanggal_akhir" id="endDate" value="{{ $endDate ?? '' }}">
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
                </form>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                            name="with_evidences" id="withEvidences" value="1"
                            {{ request('with_evidences') ? 'checked' : '' }}>
                        <label class="form-check-label" for="withEvidences">
                            <i class="feather-paperclip me-1 text-muted"></i>
                            Sertakan Bukti Mutasi dalam Laporan
                        </label>
                    </div>
                </div>
            </div>
        </div>

        @if ($startDate && $endDate)
        <!-- statistics cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <h2 class="stat-value text-primary mb-1">Rp {{ number_format($saldoAwal ?? 0, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Saldo Awal</p>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</small>
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
                                <h2 class="stat-value text-success mb-1">Rp {{ number_format($totalDebit ?? 0, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total Debit</p>
                                <small class="text-muted">{{ isset($mutasi) ? $mutasi->where('debit', '>', 0)->count() : 0 }} transaksi</small>
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
                                <h2 class="stat-value text-danger mb-1">Rp {{ number_format($totalKredit ?? 0, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Total Kredit</p>
                                <small class="text-muted">{{ isset($mutasi) ? $mutasi->where('kredit', '>', 0)->count() : 0 }} transaksi</small>
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
                                <h2 class="stat-value text-warning mb-1">Rp {{ number_format($saldoAkhir ?? 0, 0, ',', '.') }}</h2>
                                <p class="stat-label text-muted mb-0">Saldo Akhir</p>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</small>
                            </div>
                            <div class="stat-icon bg-warning-soft text-warning">
                                <i class="feather-bar-chart-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- period info alert -->
        <div class="alert alert-info-custom mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="feather-calendar fs-4"></i>
                    <div>
                        <span class="fw-semibold">Periode Laporan:</span>
                        {{ \Carbon\Carbon::parse($startDate)->translatedFormat('l, d F Y') }} 
                        <i class="feather-arrow-right mx-2"></i>
                        {{ \Carbon\Carbon::parse($endDate)->translatedFormat('l, d F Y') }}
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge badge-primary-light">
                        <i class="feather-file-text me-1"></i>{{ isset($mutasi) ? $mutasi->count() : 0 }} Transaksi
                    </span>
                    @php $selisih = ($saldoAkhir ?? 0) - ($saldoAwal ?? 0); @endphp
                    <span class="badge {{ $selisih >= 0 ? 'badge-success-light' : 'badge-danger-light' }}">
                        <i class="feather-{{ $selisih >= 0 ? 'trending-up' : 'trending-down' }} me-1"></i>
                        Perubahan: Rp {{ number_format(abs($selisih), 0, ',', '.') }}
                    </span>
                    <span class="badge badge-info-light">
                        <i class="feather-clock me-1"></i>
                        {{ \Carbon\Carbon::parse($startDate)->diffInDays($endDate) + 1 }} Hari
                    </span>
                </div>
            </div>
        </div>

        <!-- table card -->
        <div class="card table-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="feather-list me-2"></i>Detail Transaksi Mutasi Kas
                </h5>
                @if(isset($mutasi) && $mutasi->count())
                <span class="badge badge-info-light">
                    <i class="feather-activity me-1"></i>Laporan Real-time
                </span>
                @endif
            </div>
            <div class="card-body p-0">
                @if(isset($mutasi) && $mutasi->count())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 5%" class="text-center">NO</th>
                                    <th style="width: 12%">TANGGAL</th>
                                    <th style="width: 10%">KODE</th>
                                    <th style="width: 30%">URAIAN</th>
                                    <th style="width: 15%" class="text-end">DEBIT</th>
                                    <th style="width: 15%" class="text-end">KREDIT</th>
                                    <th style="width: 13%" class="text-end">SALDO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $saldoBerjalan = $saldoAwal; @endphp

                                {{-- Saldo Awal Row --}}
                                <tr class="bg-soft-info">
                                    <td class="text-center">-</td>
                                    <td>
                                        <span class="badge badge-secondary-light">{{ date('d/m/Y', strtotime($startDate)) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info-light">SALDO AWAL</span>
                                    </td>
                                    <td>
                                        <span class="fst-italic">Saldo awal periode</span>
                                    </td>
                                    <td class="text-end text-muted">-</td>
                                    <td class="text-end text-muted">-</td>
                                    <td class="text-end">
                                        <span class="fw-bold text-primary">
                                            Rp {{ number_format($saldoBerjalan, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>

                                @foreach ($mutasi as $index => $m)
                                    @php $saldoBerjalan += $m->debit - $m->kredit; @endphp
                                    <tr class="single-item align-middle">
                                        <td class="text-center">{{ $index + 1 }}</td>
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
                                                Rp {{ number_format($saldoBerjalan, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Saldo Akhir Row --}}
                                <tr class="bg-soft-warning fw-bold">
                                    <td colspan="6" class="text-end">
                                        <i class="feather-check-circle me-2"></i>SALDO AKHIR PERIODE
                                    </td>
                                    <td class="text-end text-primary fs-6">
                                        Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- footer summary -->
                    <div class="card-footer">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="pagination-info">
                                <i class="feather-calendar me-1"></i>
                                Periode: {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}
                            </div>
                            <div class="pagination-info">
                                <i class="feather-file-text me-1"></i>
                                Total: {{ $mutasi->count() }} Transaksi
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
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="feather-inbox"></i>
                        </div>
                        <h6 class="empty-state-title">Tidak Ada Transaksi</h6>
                        <p class="empty-state-text">Tidak ada transaksi pada periode yang dipilih</p>
                        <a href="{{ route('mutasi-kas.create') }}" class="btn btn-primary mt-3">
                            <i class="feather-plus me-2"></i>Tambah Transaksi
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- analysis cards -->
        @if(isset($mutasi) && $mutasi->count())
        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-pie-chart me-2"></i>Perbandingan Debit & Kredit
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $total = ($totalDebit ?? 0) + ($totalKredit ?? 0);
                            $debitPercent = $total > 0 ? ($totalDebit / $total) * 100 : 0;
                            $kreditPercent = $total > 0 ? ($totalKredit / $total) * 100 : 0;
                        @endphp
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="text-center p-3 bg-success-soft rounded-3">
                                    <h6 class="fw-bold text-success mb-1">Debit</h6>
                                    <h5 class="fw-bold mb-0">Rp {{ number_format($totalDebit ?? 0, 0, ',', '.') }}</h5>
                                    <small class="text-muted">{{ number_format($debitPercent, 1) }}%</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-danger-soft rounded-3">
                                    <h6 class="fw-bold text-danger mb-1">Kredit</h6>
                                    <h5 class="fw-bold mb-0">Rp {{ number_format($totalKredit ?? 0, 0, ',', '.') }}</h5>
                                    <small class="text-muted">{{ number_format($kreditPercent, 1) }}%</small>
                                </div>
                            </div>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-success" style="width: {{ $debitPercent }}%"></div>
                            <div class="progress-bar bg-danger" style="width: {{ $kreditPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-activity me-2"></i>Perubahan Saldo
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="stat-icon {{ $selisih >= 0 ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }} mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="feather-{{ $selisih >= 0 ? 'arrow-up' : 'arrow-down' }} fs-2"></i>
                        </div>
                        <h4 class="fw-bold text-{{ $selisih >= 0 ? 'success' : 'danger' }} mb-2">
                            Rp {{ number_format(abs($selisih), 0, ',', '.') }}
                        </h4>
                        <p class="text-muted mb-3">
                            {{ $selisih >= 0 ? 'Kenaikan' : 'Penurunan' }} dari saldo awal
                        </p>
                        @if(($saldoAwal ?? 0) > 0)
                        <span class="badge {{ $selisih >= 0 ? 'badge-success-light' : 'badge-danger-light' }} fs-6">
                            {{ number_format(abs(($selisih / $saldoAwal) * 100), 2) }}%
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        @else
        <!-- empty state for no period selected -->
        <div class="card table-card">
            <div class="card-body p-5">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="feather-calendar"></i>
                    </div>
                    <h6 class="empty-state-title">Pilih Periode Laporan</h6>
                    <p class="empty-state-text">
                        Silakan pilih tanggal mulai dan tanggal akhir<br>
                        untuk menampilkan laporan mutasi kas
                    </p>
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-10">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-3 bg-primary-soft rounded-3 text-center">
                                        <i class="feather-filter fs-3 text-primary mb-2"></i>
                                        <h6 class="mb-1">Filter Fleksibel</h6>
                                        <small class="text-muted">Pilih periode sesuai kebutuhan</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-success-soft rounded-3 text-center">
                                        <i class="feather-bar-chart-2 fs-3 text-success mb-2"></i>
                                        <h6 class="mb-1">Analisis Detail</h6>
                                        <small class="text-muted">Lihat detail setiap transaksi</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-info-soft rounded-3 text-center">
                                        <i class="feather-download fs-3 text-info mb-2"></i>
                                        <h6 class="mb-1">Export Mudah</h6>
                                        <small class="text-muted">PDF & Excel tersedia</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
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

.form-control {
    border-radius: 0.625rem;
    border: 1px solid #e2e8f0;
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background-color: #ffffff;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(52, 84, 209, 0.1);
    outline: none;
}

.form-control:hover {
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

.btn-outline-danger, .btn-outline-success {
    border-radius: 0.5rem;
    padding: 0.375rem 0.875rem;
    font-size: 0.8125rem;
    transition: all 0.2s ease;
}

.btn-outline-danger:hover, .btn-outline-success:hover {
    transform: translateY(-1px);
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

.badge-secondary-light {
    background-color: rgba(108, 117, 125, 0.08);
    color: #6c757d;
}

.badge-warning-light {
    background-color: rgba(255, 193, 7, 0.08);
    color: var(--warning-color);
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

/* Alert Info Custom */
.alert-info-custom {
    background: linear-gradient(135deg, rgba(52, 84, 209, 0.05) 0%, rgba(30, 58, 138, 0.05) 100%);
    border: 1px solid rgba(52, 84, 209, 0.15);
    border-radius: 1rem;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

/* Progress Bar */
.progress {
    background-color: #e9ecef;
    border-radius: 999px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.6s ease;
}

/* Pagination Info */
.pagination-info {
    font-size: 0.75rem;
    color: #6c757d;
}

/* Empty State */
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

/* Rounded */
.rounded-3 {
    border-radius: 0.75rem !important;
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

    .custom-date-picker {
        width: 280px;
    }
    
    .quick-filter-btn {
        padding: 0.3rem 0.8rem;
        font-size: 0.7rem;
    }
    
    .alert-info-custom {
        padding: 0.875rem 1rem;
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
    // CUSTOM DATE PICKER (DI PERTAHANKAN)
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
            
            if (!start || !end) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Tanggal Belum Dipilih',
                    text: 'Silakan pilih tanggal mulai dan tanggal akhir terlebih dahulu',
                    confirmButtonColor: '#3454D1'
                });
                return;
            }
            
            if (start > end) {
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
    
    // Handle long numbers in stat cards
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(el => {
        const text = el.innerText;
        if (text.length > 18) {
            el.style.fontSize = '1.25rem';
        }
    });
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
// EXPORT FUNCTIONS
// ========================================
function exportPDF() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate || !endDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Periode Belum Dipilih',
            text: 'Silakan pilih periode laporan terlebih dahulu',
            confirmButtonColor: '#3454D1'
        });
        return;
    }
    
    window.open("{{ route('laporan.export.pdf') }}?tanggal_mulai=" + startDate + "&tanggal_akhir=" + endDate, '_blank');
}

function exportExcel() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate || !endDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Periode Belum Dipilih',
            text: 'Silakan pilih periode laporan terlebih dahulu',
            confirmButtonColor: '#3454D1'
        });
        return;
    }
    
    window.open("{{ route('laporan.export.excel') }}?tanggal_mulai=" + startDate + "&tanggal_akhir=" + endDate, '_blank');
}

// SweetAlert Notifications
@if(session('exported'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Laporan berhasil diekspor',
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
@endif

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