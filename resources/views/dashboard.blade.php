@extends('layouts.app')

@section('title', 'Dashboard - EduFinance')

@push('styles')
<style>
.license-badge:hover {
    transform: scale(1.02);
    background: rgba(0, 0, 0, 0.85) !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3) !important;
}
.fs-9 {
    font-size: 0.75rem;
}
.dashboard-filter-bar {
    background: #fff;
    border-bottom: 2px solid #e9ecef;
    padding: 10px 24px;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.filter-bar-inner {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.filter-label {
    font-size: 13px;
    font-weight: 600;
    color: #555;
    white-space: nowrap;
}
.filter-bar-inner select.form-select {
    width: auto;
    min-width: 130px;
    font-size: 13px;
    border-radius: 8px;
    border: 1.5px solid #dee2e6;
    padding: 6px 28px 6px 10px;
    cursor: pointer;
    transition: border-color .2s;
}
.filter-bar-inner select.form-select:focus {
    border-color: #3454D1;
    box-shadow: 0 0 0 3px rgba(52,84,209,.12);
    outline: none;
}
.filter-bar-inner select.form-select:disabled {
    background: #f8f9fa;
    cursor: not-allowed;
    opacity: .6;
}
.filter-active-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(52,84,209,.1);
    color: #3454D1;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
}
.btn-reset-filter {
    font-size: 12px;
    padding: 5px 12px;
    border-radius: 8px;
    border: 1.5px solid #dee2e6;
    background: #fff;
    color: #6c757d;
    cursor: pointer;
    transition: all .2s;
    white-space: nowrap;
}
.btn-reset-filter:hover {
    border-color: #dc3545;
    color: #dc3545;
    background: rgba(220,53,69,.05);
}
.filter-loading {
    display: none;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #3454D1;
}
.filter-loading.show { display: flex; }
.filter-spinner {
    width: 14px; height: 14px;
    border: 2px solid rgba(52,84,209,.2);
    border-top-color: #3454D1;
    border-radius: 50%;
    animation: filterSpin .7s linear infinite;
}
@keyframes filterSpin { to { transform: rotate(360deg); } }
@media (max-width: 576px) {
    .dashboard-filter-bar { padding: 10px 12px; }
    .filter-bar-inner select.form-select { min-width: 100px; font-size: 12px; }
}

#superAdminDashboard {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

#superAdminDashboard .page-header {
    margin-top: 0 !important;
}

/* Avatar sizes */
.avatar-sm {
    width: 36px;
    height: 36px;
    font-size: 14px;
}

.avatar-lg {
    width: 60px;
    height: 60px;
    font-size: 24px;
}

/* Soft backgrounds for badges and cards */
.bg-soft-primary { background-color: rgba(52, 84, 209, 0.1); }
.bg-soft-success { background-color: rgba(37, 176, 3, 0.1); }
.bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }
.bg-soft-warning { background-color: rgba(252, 108, 0, 0.1); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }

/* Quick Action Card Animation */
.card.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: transform 0.3s ease;
}

.card.bg-primary:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

/* Progress bar animation */
.progress-bar {
    transition: width 1s ease-in-out;
}

/* Financial Health Cards */
.border.rounded {
    border-color: #e9ecef !important;
    transition: all 0.3s ease;
}

.border.rounded:hover {
    border-color: #3454D1 !important;
    box-shadow: 0 5px 15px rgba(52, 84, 209, 0.1);
    transform: translateY(-3px);
}

/* Button group styling */
.btn-group .btn {
    transition: all 0.3s ease;
}

.btn-group .btn.active {
    background-color: #3454D1;
    border-color: #3454D1;
    color: white;
}

/* Chart card hover effect */
.card:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

/* Alert info in Financial Health */
.alert-info {
    background-color: rgba(23, 162, 184, 0.1);
    border-color: rgba(23, 162, 184, 0.2);
    color: #0c5460;
}
</style>
@endpush

@section('content')
@if(Auth::user()->role != 'super_admin')
<div class="nxl-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Dashboard Keuangan Instansi</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Dashboard</li>
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

                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>

        {{-- NOTIFIKASI LISENSI --}}
        @if(isset($notification) && $notification)
        <div class="alert alert-{{ $notification['type'] }} alert-dismissible {{ $notification['dismissible'] ? 'fade show' : '' }} mb-3 mx-3" role="alert">
            <div class="d-flex align-items-start gap-3">
                <div class="avatar-text avatar-sm bg-{{ $notification['type'] }} text-white">
                    <i class="{{ $notification['icon'] }} fs-5"></i>
                </div>
                <div class="flex-fill">
                    <strong>{{ $notification['title'] }}</strong>
                    <p class="mb-0 mt-1">{{ $notification['message'] }}</p>
                    @if($notification['button_text'])
                        <a href="{{ $notification['button_url'] }}" class="btn btn-sm btn-{{ $notification['type'] }} mt-2">
                            <i class="feather-shopping-cart me-1"></i>
                            {{ $notification['button_text'] }}
                        </a>
                    @endif
                </div>
                @if($notification['dismissible'])
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                @endif
            </div>
        </div>
        @endif

        {{-- BADGE STATUS LISENSI DI SUDUT KANAN BAWAH --}}
        @if(isset($license) && $license)
        <div class="position-fixed bottom-0 end-0 m-3 z-3" style="z-index: 1050;">
            <div class="license-badge shadow-lg border-0 rounded-3 overflow-hidden"
                style="background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(12px); min-width: 200px;
                        transition: all 0.2s ease-in-out; cursor: pointer;"
                data-bs-toggle="tooltip"
                title="Detail lisensi">
                <div class="px-3 py-2">
                    <div class="d-flex align-items-center gap-3">
                        {{-- Icon --}}
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-1"
                                style="background: {{ $license->isActive() ? 'rgba(25, 135, 84, 0.2)' : 'rgba(220, 53, 69, 0.2)' }}">
                                <i class="feather-{{ $license->isActive() ? 'check-circle' : 'alert-circle' }} fs-5"
                                style="color: {{ $license->isActive() ? '#198754' : '#dc3545' }}"></i>
                            </div>
                        </div>

                        {{-- Konten --}}
                        <div class="flex-grow-1">
                            <div class="text-white small fw-semibold lh-1 mb-1">Status Lisensi</div>
                            @if($license->isActive())
                                <span class="badge bg-success bg-opacity-75 px-2 py-1 fw-semibold">
                                    <i class="feather-check-circle me-1 fs-9"></i> AKTIF
                                </span>
                                <div class="text-white-50 small mt-1">
                                    ⏱️ Sisa {{ $license->daysLeft() }} hari
                                </div>
                            @else
                                <span class="badge bg-danger bg-opacity-75 px-2 py-1 fw-semibold">
                                    <i class="feather-x-circle me-1 fs-9"></i> EXPIRED
                                </span>
                                <div class="text-white-50 small mt-1">
                                    ⚠️ Segera perpanjang
                                </div>
                            @endif
                        </div>

                        {{-- Indikator kecil --}}
                        <div class="flex-shrink-0">
                            <div class="rounded-circle"
                                style="width: 8px; height: 8px; background: {{ $license->isActive() ? '#198754' : '#dc3545' }};
                                        box-shadow: 0 0 6px {{ $license->isActive() ? '#198754' : '#dc3545' }};"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="dashboard-filter-bar">
        <div class="filter-bar-inner">

            <i class="feather-sliders" style="color:#3454D1;font-size:16px;"></i>
            <span class="filter-label">Filter:</span>

            {{-- ── SELECT TAHUN ── --}}
            <select class="form-select" id="fb_tahun">
                <option value="all" {{ $selectedYear == 'all' ? 'selected' : '' }}>🌟 Semua Tahun</option>
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ (string)$year == (string)$selectedYear ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>

            {{-- ── SELECT BULAN ── --}}
            <select class="form-select" id="fb_bulan"
                {{ $selectedYear == 'all' ? 'disabled' : '' }}>
                <option value="0" {{ $selectedMonth == 0 ? 'selected' : '' }}>Semua Bulan</option>
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ (int)$selectedMonth === $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>

            {{-- ── BADGE PERIODE AKTIF ── --}}
            <span class="filter-active-badge" id="fb_badge"
                style="{{ ($selectedYear == 'all' && $selectedMonth == 0) ? 'display:none' : '' }}">
                <i class="feather-check-circle"></i>
                <span id="fb_badge_text">
                    {{ $selectedYear == 'all' ? 'Semua Tahun' : $selectedYear }}
                    {{ $selectedMonth > 0 ? '· ' . $selectedMonthName : '' }}
                </span>
            </span>

            {{-- ── TOMBOL RESET ── --}}
            <button class="btn-reset-filter" id="fb_reset"
                onclick="fbReset()"
                style="{{ ($selectedYear == 'all' && $selectedMonth == 0) ? 'display:none' : '' }}">
                <i class="feather-x me-1"></i>Reset
            </button>

            {{-- ── LOADING ── --}}
            <div class="filter-loading" id="fb_loading">
                <div class="filter-spinner"></div>
                <span>Memuat...</span>
            </div>

            {{-- ── INFO PERIODE (kanan) ── --}}
            <div class="ms-auto d-none d-md-flex align-items-center gap-2">
                <small class="text-muted">
                    <i class="feather-calendar me-1"></i>
                    Periode: <strong id="fb_periode_text">
                        {{ $selectedYear == 'all' ? 'Semua Tahun' : $selectedYear }}
                        {{ $selectedMonth > 0 ? '- ' . $selectedMonthName : '' }}
                    </strong>
                </small>
                <button class="btn btn-sm btn-outline-secondary" onclick="fbRefresh()" title="Refresh">
                    <i class="feather-refresh-cw"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-lg bg-primary text-white">
                                    <i class="feather-target fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-12 fw-medium text-muted">Total Perencanaan</div>
                                    <h4 class="fw-bold mb-0">{{ $totalRencanaFiltered }}</h4>
                                    <small class="fs-11 text-muted">Kegiatan</small>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3 mt-3 border-top border-dashed">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-11 text-muted">{{ $persenRencana }}% dari total</span>
                                <span class="badge bg-soft-primary text-primary">{{ $totalRencana }} total</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-lg bg-success text-white">
                                    <i class="feather-check-circle fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-12 fw-medium text-muted">Progress Realisasi</div>
                                    <h4 class="fw-bold mb-0">{{ $progressPersen }}%</h4>
                                    <small class="fs-11 text-muted">{{ $selesaiFiltered }}/{{ $totalRencanaFiltered }} kegiatan</small>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3 mt-3 border-top border-dashed">
                            <div class="progress ht-3">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPersen }}%"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <span class="fs-11 text-muted">Target tercapai</span>
                                <span class="badge bg-soft-success text-success">{{ $progressPersen }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-lg bg-info text-white">
                                    <i class="feather-trending-up fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-12 fw-medium text-muted">Total Pemasukan</div>
                                    <h4 class="fw-bold mb-0">Rp {{ number_format($stats['total_mutasi_debit'] ?? 0, 0, ',', '.') }}</h4>
                                    <small class="fs-11 text-muted">{{ $totalTransaksiDebit }} transaksi</small>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3 mt-3 border-top border-dashed">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-11 text-muted">{{ $persenPemasukan }}% dari total</span>
                                <span class="badge bg-soft-info text-info">
                                    <i class="feather-trending-up"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-lg bg-danger text-white">
                                    <i class="feather-trending-down fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-12 fw-medium text-muted">Total Pengeluaran</div>
                                    <h4 class="fw-bold mb-0">Rp {{ number_format($stats['total_mutasi_kredit'] ?? 0, 0, ',', '.') }}</h4>
                                    <small class="fs-11 text-muted">{{ $totalTransaksiKredit }} transaksi</small>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3 mt-3 border-top border-dashed">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-11 text-muted">{{ $persenPengeluaran }}% dari total</span>
                                <span class="badge bg-soft-danger text-danger">
                                    <i class="feather-trending-down"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card bg-primary text-white stretch stretch-full">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h5 class="text-white mb-2">
                                    <i class="feather-zap me-2"></i>Quick Actions
                                </h5>
                                <p class="text-white-50 mb-3">Akses cepat ke fitur-fitur utama sistem keuangan sekolah</p>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('mutasi-kas.create') }}" class="btn btn-light btn-sm">
                                        <i class="feather-plus-circle me-1"></i>Tambah Transaksi
                                    </a>
                                    <a href="{{ route('perencanaan.create') }}" class="btn btn-light btn-sm">
                                        <i class="feather-file-plus me-1"></i>Buat Perencanaan
                                    </a>
                                    <a href="{{ route('laporan.mutasi') }}" class="btn btn-light btn-sm">
                                        <i class="feather-bar-chart-2 me-1"></i>Lihat Laporan
                                    </a>
                                    <a href="{{ route('kode-transaksi.create') }}" class="btn btn-light btn-sm">
                                        <i class="feather-hash me-1"></i>Tambah Kode
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 text-end">
                                <i class="feather-activity" style="font-size: 120px; opacity: 0.1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Chart Area: Trend Perencanaan -->
            <div class="col-xxl-8">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-trending-up me-2 text-primary"></i>
                            Trend Perencanaan {{ $selectedYear == 'all' ? 'Semua Tahun' : $selectedYear }}
                        </h5>
                        <div class="card-header-action">
                            <div class="dropdown">
                                <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <i class="feather-more-vertical"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:void(0);" class="dropdown-item" onclick="downloadChart('chartJumlahRencana')">
                                        <i class="feather-download me-3"></i>
                                        <span>Download PNG</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chartJumlahRencana"></div>
                    </div>
                </div>
            </div>

            <!-- Radial Progress Chart -->
            <div class="col-xxl-4">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-pie-chart me-2 text-success"></i>
                            Progress Realisasi
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div id="chartProgress"></div>
                        <div class="row mt-4">
                            <div class="col-6">
                                <div class="p-3 bg-soft-success rounded">
                                    <h4 class="text-success mb-1">{{ $selesaiFiltered }}</h4>
                                    <small class="text-muted">Selesai</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-soft-warning rounded">
                                    <h4 class="text-warning mb-1">{{ $totalRencanaFiltered - $selesaiFiltered }}</h4>
                                    <small class="text-muted">Belum</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Flow Visualization -->
        <div class="row">
            <div class="col-xxl-8">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-dollar-sign me-2 text-info"></i>
                            Cash Flow {{ $selectedYear == 'all' ? 'Semua Tahun' : $selectedYear }}
                        </h5>
                        <div class="card-header-action">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" onclick="switchChartType('area')">Area</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchChartType('line')">Line</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchChartType('bar')">Bar</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chartMutasi"></div>
                        <div class="row mt-4 text-center">
                            <div class="col-4">
                                <div class="p-3 bg-soft-success rounded">
                                    <i class="feather-arrow-down-circle fs-4 text-success mb-2"></i>
                                    <h5 class="mb-1">Rp {{ number_format($stats['total_mutasi_debit'] ?? 0, 0, ',', '.') }}</h5>
                                    <small class="text-muted">Total Pemasukan</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-soft-danger rounded">
                                    <i class="feather-arrow-up-circle fs-4 text-danger mb-2"></i>
                                    <h5 class="mb-1">Rp {{ number_format($stats['total_mutasi_kredit'] ?? 0, 0, ',', '.') }}</h5>
                                    <small class="text-muted">Total Pengeluaran</small>
                                </div>
                            </div>
                            <div class="col-4">
                                @php $netCashFlow = ($stats['total_mutasi_debit'] ?? 0) - ($stats['total_mutasi_kredit'] ?? 0); @endphp
                                <div class="p-3 bg-soft-{{ $netCashFlow >= 0 ? 'primary' : 'warning' }} rounded">
                                    <i class="feather-activity fs-4 text-{{ $netCashFlow >= 0 ? 'primary' : 'warning' }} mb-2"></i>
                                    <h5 class="mb-1">Rp {{ number_format(abs($netCashFlow), 0, ',', '.') }}</h5>
                                    <small class="text-muted">Net Cash Flow</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kategori Distribution Radar Chart -->
            <div class="col-xxl-4">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-layers me-2 text-warning"></i>
                            Distribusi Kategori
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="chartKategori"></div>

                    <div class="mt-4">
                        @forelse($kategoriAnggaran ?? [] as $kategori)
                            @php
                                $colorData  = $kategoriColors[$kategori->nama_kategori] ?? ['bg' => 'primary', 'icon' => 'folder'];
                                $totalKode  = $kategori->kodeTransaksi->count();
                                // Hitung total nominal dari mutasi_kas lewat kode transaksi kategori ini
                                $totalNominal = $kategori->kodeTransaksi->sum(function($kode) {
                                    return $kode->mutasiKas->sum('debit') + $kode->mutasiKas->sum('kredit');
                                });
                            @endphp
                            <div class="mb-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-text avatar-sm bg-soft-{{ $colorData['bg'] }} text-{{ $colorData['bg'] }}">
                                        <i class="feather-{{ $colorData['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ Str::limit($kategori->nama_kategori, 22) }}</div>
                                        <small class="text-muted">{{ $totalKode }} kode transaksi</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-soft-{{ $colorData['bg'] }} text-{{ $colorData['bg'] }} d-block mb-1">
                                        {{ $totalKode }} kode
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3">
                                <i class="feather-inbox fs-3 text-muted"></i>
                                <p class="text-muted mt-2 small">Belum ada kategori</p>
                            </div>
                        @endforelse
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Multi-Metric Comparison & Calendar -->
        <div class="row">
            <!-- Multi-Metric Comparison Chart -->
            <div class="col-xxl-6">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-bar-chart-2 me-2 text-primary"></i>
                            Multi-Metric Comparison
                        </h5>
                        <div class="card-header-action">
                            <span class="badge bg-soft-info text-info">
                                <i class="feather-trending-up me-1"></i>
                                Periode: {{ $selectedYear == 'all' ? 'Semua Tahun' : $selectedYear }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chartMultiMetric"></div>
                        <div class="row mt-4 text-center">
                            <div class="col-4">
                                <div class="p-3 bg-soft-primary rounded">
                                    <i class="feather-target fs-4 text-primary mb-2"></i>
                                    <h6 class="mb-1">Planning</h6>
                                    <small class="text-muted">Total Kegiatan</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-soft-success rounded">
                                    <i class="feather-arrow-down-circle fs-4 text-success mb-2"></i>
                                    <h6 class="mb-1">Income</h6>
                                    <small class="text-muted">Pemasukan</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-soft-danger rounded">
                                    <i class="feather-arrow-up-circle fs-4 text-danger mb-2"></i>
                                    <h6 class="mb-1">Expense</h6>
                                    <small class="text-muted">Pengeluaran</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Widget -->
            <div class="col-xxl-6">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-calendar me-2 text-danger"></i>
                            Kalender Perencanaan
                        </h5>
                        <div class="card-header-action">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="btnCalendarMonth">Bulan</button>
                                <button type="button" class="btn btn-outline-primary" id="btnCalendarYear">Tahun</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="calendarView"></div>

                        <div class="mt-4">
                            <h6 class="mb-3">
                                <i class="feather-list me-2"></i>
                                Upcoming Events
                            </h6>
                            <div id="upcomingEvents"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Activities (moved from previous position) -->
        <div class="row">
            <div class="col-xxl-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-star me-2 text-warning"></i>
                            Aktivitas Teratas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xxl-12">
                                <div class="card stretch stretch-full">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="feather-star me-2 text-warning"></i>
                                            Aktivitas Teratas
                                        </h5>
                                        <div class="card-header-action">
                                            <span class="badge bg-soft-info text-info">
                                                {{ $selectedYear == 'all' ? 'Semua Tahun' : $selectedYear }}
                                                {{ $selectedMonth > 0 ? '- ' . $selectedMonthName : '' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @forelse($topAktivitas as $index => $akt)
                                                <div class="col-md-6 col-xl-4 mb-3">
                                                    <div class="p-3 border rounded h-100">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'info' : 'secondary') }}">
                                                                #{{ $index + 1 }}
                                                            </span>
                                                            <span class="badge bg-soft-primary text-primary">{{ $akt['jumlah'] }} aktivitas</span>
                                                        </div>
                                                        <h6 class="mb-1">{{ $akt['bulan'] }} {{ $akt['tahun'] }}</h6>
                                                        <div class="d-flex gap-2 mb-2">
                                                            <small class="text-muted">
                                                                <i class="feather-target me-1 text-primary"></i>
                                                                {{ $akt['jumlah_rencana'] }} rencana
                                                            </small>
                                                            <small class="text-muted">
                                                                <i class="feather-check-circle me-1 text-success"></i>
                                                                {{ $akt['jumlah_realisasi'] }} realisasi
                                                            </small>
                                                        </div>
                                                        <div class="progress" style="height: 8px;">
                                                            @php
                                                                $maxVal = collect($topAktivitas)->max('jumlah');
                                                                $pct = $maxVal > 0 ? ($akt['jumlah'] / $maxVal) * 100 : 0;
                                                            @endphp
                                                            <div class="progress-bar bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'info' : 'primary') }}"
                                                                style="width: {{ $pct }}%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12 text-center py-5">
                                                    <i class="feather-inbox fs-3 text-muted"></i>
                                                    <p class="text-muted mt-2">Belum ada data aktivitas</p>
                                                </div>
                                            @endforelse
                                        </div>

                                        <div class="mt-4 p-3 bg-soft-info rounded">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="feather-info text-info"></i>
                                                <small class="text-muted">
                                                    Total {{ $totalRencanaFiltered }} perencanaan &amp; {{ $selesaiFiltered }} realisasi selesai
                                                    {{ $selectedYear != 'all' ? 'di tahun ' . $selectedYear : 'di semua tahun' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Health Indicators -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-heart me-2 text-danger"></i>
                            Indikator Kesehatan Keuangan
                        </h5>
                        <div class="card-header-action">
                            <span class="badge bg-soft-info text-info">
                                <i class="feather-info me-1"></i>
                                Periode: {{ $selectedYear == 'all' ? 'Semua Tahun' : $selectedYear }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @php
                                $debit = $stats['total_mutasi_debit'] ?? 0;
                                $kredit = $stats['total_mutasi_kredit'] ?? 0;

                                // Cash Ratio = (Pemasukan / Pengeluaran) × 100
                                // Jika > 100% = Sehat, < 100% = Perlu perhatian
                                $cashRatio = $kredit > 0 ? ($debit / $kredit) * 100 : ($debit > 0 ? 999 : 100);
                                $cashRatioStatus = $cashRatio >= 100 ? 'success' : ($cashRatio >= 80 ? 'warning' : 'danger');

                                // Saving Rate = ((Pemasukan - Pengeluaran) / Pemasukan) × 100
                                // Menunjukkan berapa persen yang bisa ditabung
                                $savingRate = $debit > 0 ? (($debit - $kredit) / $debit) * 100 : 0;
                                $savingRateStatus = $savingRate >= 20 ? 'success' : ($savingRate >= 10 ? 'warning' : 'danger');

                                // Total Transaksi (UNIQUE)
                                $totalTransaksi = $totalTransaksiDebit + $totalTransaksiKredit;

                                // Net Cash Flow (Surplus/Defisit)
                                $netCashFlow = $debit - $kredit;
                                $netCashFlowStatus = $netCashFlow >= 0 ? 'success' : 'danger';
                            @endphp

                            <!-- Cash Ratio -->
                            <div class="col-lg-3">
                                <div class="p-4 text-center border rounded h-100">
                                    <div class="avatar-text avatar-lg bg-soft-{{ $cashRatioStatus }} text-{{ $cashRatioStatus }} mx-auto mb-3">
                                        <i class="feather-trending-up fs-4"></i>
                                    </div>
                                    <h4 class="fw-bold mb-2 text-{{ $cashRatioStatus }}">
                                        {{ $cashRatio >= 999 ? '∞' : number_format($cashRatio, 1) }}%
                                    </h4>
                                    <p class="text-muted mb-2 fw-semibold">Cash Ratio</p>
                                    <small class="text-muted d-block">Kemampuan bayar</small>
                                    <div class="mt-3">
                                        <span class="badge bg-soft-{{ $cashRatioStatus }} text-{{ $cashRatioStatus }}">
                                            @if($cashRatio >= 100)
                                                <i class="feather-check-circle me-1"></i>Sehat
                                            @elseif($cashRatio >= 80)
                                                <i class="feather-alert-circle me-1"></i>Perhatian
                                            @else
                                                <i class="feather-x-circle me-1"></i>Kritis
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Saving Rate -->
                            <div class="col-lg-3">
                                <div class="p-4 text-center border rounded h-100">
                                    <div class="avatar-text avatar-lg bg-soft-{{ $savingRateStatus }} text-{{ $savingRateStatus }} mx-auto mb-3">
                                        <i class="feather-save fs-4"></i>
                                    </div>
                                    <h4 class="fw-bold mb-2 text-{{ $savingRateStatus }}">
                                        {{ number_format($savingRate, 1) }}%
                                    </h4>
                                    <p class="text-muted mb-2 fw-semibold">Saving Rate</p>
                                    <small class="text-muted d-block">Tingkat penghematan</small>
                                    <div class="mt-3">
                                        <span class="badge bg-soft-{{ $savingRateStatus }} text-{{ $savingRateStatus }}">
                                            @if($savingRate >= 20)
                                                <i class="feather-thumbs-up me-1"></i>Baik
                                            @elseif($savingRate >= 10)
                                                <i class="feather-minus-circle me-1"></i>Cukup
                                            @elseif($savingRate >= 0)
                                                <i class="feather-alert-triangle me-1"></i>Rendah
                                            @else
                                                <i class="feather-trending-down me-1"></i>Defisit
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Transaksi -->
                            <div class="col-lg-3">
                                <div class="p-4 text-center border rounded h-100">
                                    <div class="avatar-text avatar-lg bg-soft-warning text-warning mx-auto mb-3">
                                        <i class="feather-activity fs-4"></i>
                                    </div>
                                    <h4 class="fw-bold mb-2 text-warning">{{ number_format($totalTransaksi) }}</h4>
                                    <p class="text-muted mb-2 fw-semibold">Total Transaksi</p>
                                    <small class="text-muted d-block">Periode ini</small>
                                    <div class="mt-3">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="bg-soft-success rounded p-2">
                                                    <small class="text-success fw-semibold d-block">{{ $totalTransaksiDebit }}</small>
                                                    <small class="text-muted">Debit</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="bg-soft-danger rounded p-2">
                                                    <small class="text-danger fw-semibold d-block">{{ $totalTransaksiKredit }}</small>
                                                    <small class="text-muted">Kredit</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Net Cash Flow -->
                            <div class="col-lg-3">
                                <div class="p-4 text-center border rounded h-100">
                                    <div class="avatar-text avatar-lg bg-soft-{{ $netCashFlowStatus }} text-{{ $netCashFlowStatus }} mx-auto mb-3">
                                        <i class="feather-{{ $netCashFlow >= 0 ? 'arrow-up-circle' : 'arrow-down-circle' }} fs-4"></i>
                                    </div>
                                    <h4 class="fw-bold mb-2 text-{{ $netCashFlowStatus }}">
                                        Rp {{ number_format(abs($netCashFlow) / 1000000, 1) }}Jt
                                    </h4>
                                    <p class="text-muted mb-2 fw-semibold">Net Cash Flow</p>
                                    <small class="text-muted d-block">{{ $netCashFlow >= 0 ? 'Surplus' : 'Defisit' }}</small>
                                    <div class="mt-3">
                                        <div class="progress" style="height: 6px;">
                                            @php
                                                $total = abs($debit) + abs($kredit);
                                                $surplusPercent = $total > 0 ? (abs($netCashFlow) / $total) * 100 : 0;
                                            @endphp
                                            <div class="progress-bar bg-{{ $netCashFlowStatus }}"
                                                 role="progressbar"
                                                 style="width: {{ min($surplusPercent, 100) }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ number_format($surplusPercent, 1) }}% dari total</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Footer -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <div class="d-flex align-items-start gap-3">
                                        <i class="feather-info fs-4"></i>
                                        <div class="flex-fill">
                                            <h6 class="mb-2">Penjelasan Indikator:</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="d-block mb-1">
                                                        <strong>Cash Ratio:</strong> Perbandingan pemasukan dengan pengeluaran.
                                                        >100% = Sehat, 80-100% = Perhatian, <80% = Kritis
                                                    </small>
                                                    <small class="d-block">
                                                        <strong>Saving Rate:</strong> Persentase dana yang dapat ditabung.
                                                        >20% = Baik, 10-20% = Cukup, <10% = Rendah
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="d-block mb-1">
                                                        <strong>Total Transaksi:</strong> Jumlah seluruh transaksi debit dan kredit dalam periode
                                                    </small>
                                                    <small class="d-block">
                                                        <strong>Net Cash Flow:</strong> Selisih antara pemasukan dan pengeluaran (Surplus/Defisit)
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Perencanaan Table -->
        <div class="row">
            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Ringkasan Perencanaan Terbaru</h5>
                        <div class="card-header-action">
                            <a href="{{ route('perencanaan.index') }}" class="btn btn-sm btn-primary">
                                <span>Lihat Semua</span>
                                <i class="feather-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Bulan</th>
                                        <th>Kegiatan</th>
                                        <th>Target</th>
                                        <th>Pelaksanaan</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentPerencanaan as $detail)
                                    <tr>
                                        <td>
                                            <span class="badge bg-soft-info text-info">
                                                {{ $detail->bulan }}/{{ $detail->tahun }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $detail->perencanaan }}</div>
                                            <small class="text-muted">{{ Str::limit($detail->deskripsi, 40) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-primary text-primary">{{ $detail->target }}</span>
                                        </td>
                                        <td>{{ $detail->pelaksanaan }}</td>
                                        <td>
                                            @php
                                                // Cek apakah detail ini sudah punya realisasi
                                                $realisasiDetail = \App\Models\Realisasi::where('detail_perencanaan_id', $detail->detail_id)
                                                    ->orderBy('tanggal_realisasi', 'desc')
                                                    ->first();
                                            @endphp
                                            @if($realisasiDetail)
                                                <span class="badge bg-soft-{{ $realisasiDetail->status_color }} text-{{ $realisasiDetail->status_color }}">
                                                    <i class="feather-{{ $realisasiDetail->status_target == 'sesuai' ? 'check-circle' : ($realisasiDetail->status_target == 'sebagian' ? 'alert-circle' : 'x-circle') }} me-1"></i>
                                                    {{ $realisasiDetail->status_label }}
                                                </span>
                                            @else
                                                <span class="badge bg-soft-warning text-warning">
                                                    <i class="feather-clock me-1"></i>Belum Realisasi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('perencanaan.show', $detail->perencanaan_id) }}" class="btn btn-sm btn-light-brand">
                                                <i class="feather-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="feather-inbox fs-1 text-muted"></i>
                                            <p class="text-muted mt-2">Belum ada data perencanaan</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════
     SUPER ADMIN DASHBOARD — hanya tampil jika role = super_admin
     ═══════════════════════════════════════════════════════════ --}}
@if(Auth::user()->role == 'super_admin')
<div class="nxl-content" id="superAdminDashboard">

    {{-- ── HEADER ── --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">
                    <i class="feather-shield me-2 text-danger"></i>
                    Super Admin Overview
                </h5>
            </div>
        </div>
        <div class="page-header-right ms-auto">
            <span class="badge bg-danger px-3 py-2">
                <i class="feather-lock me-1"></i>Super Admin Only
            </span>
        </div>
    </div>

    <div class="main-content">

        {{-- ── STAT CARDS ── --}}
        <div class="row">

            {{-- Total User Aktif --}}
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-primary text-white">
                                <i class="feather-users fs-4"></i>
                            </div>
                            <div>
                                <div class="fs-12 fw-medium text-muted">User Berlisensi Aktif</div>
                                <h4 class="fw-bold mb-0">{{ $sa['totalUserAktif'] }}</h4>
                                <small class="fs-11 text-muted">dari {{ $sa['totalUser'] }} total user</small>
                            </div>
                        </div>
                        <div class="pt-3 mt-3 border-top border-dashed">
                            <div class="d-flex justify-content-between">
                                <span class="fs-11 text-muted">Baru bulan ini</span>
                                <span class="badge bg-soft-primary text-primary">+{{ $sa['userBaruBulanIni'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Lisensi Aktif --}}
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-success text-white">
                                <i class="feather-check-circle fs-4"></i>
                            </div>
                            <div>
                                <div class="fs-12 fw-medium text-muted">Lisensi Aktif</div>
                                <h4 class="fw-bold mb-0">{{ $sa['lisensiAktif'] }}</h4>
                                <small class="fs-11 text-muted">{{ $sa['lisensiExpired'] }} expired</small>
                            </div>
                        </div>
                        <div class="pt-3 mt-3 border-top border-dashed">
                            <div class="progress ht-3">
                                @php $pctLisensi = $sa['totalUser'] > 0 ? round(($sa['lisensiAktif'] / $sa['totalUser']) * 100) : 0; @endphp
                                <div class="progress-bar bg-success" style="width: {{ $pctLisensi }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span class="fs-11 text-muted">Coverage</span>
                                <span class="badge bg-soft-success text-success">{{ $pctLisensi }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Revenue --}}
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-warning text-white">
                                <i class="feather-dollar-sign fs-4"></i>
                            </div>
                            <div>
                                <div class="fs-12 fw-medium text-muted">Total Revenue</div>
                                <h4 class="fw-bold mb-0">Rp {{ number_format($sa['totalRevenue'] / 1000000, 1) }}Jt</h4>
                                <small class="fs-11 text-muted">{{ $sa['totalTransaksiPembayaran'] }} transaksi</small>
                            </div>
                        </div>
                        <div class="pt-3 mt-3 border-top border-dashed">
                            <div class="d-flex justify-content-between">
                                <span class="fs-11 text-muted">Bulan ini</span>
                                <span class="badge bg-soft-warning text-warning">
                                    Rp {{ number_format($sa['revenueBulanIni'] / 1000000, 1) }}Jt
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- User Suspended --}}
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-danger text-white">
                                <i class="feather-user-x fs-4"></i>
                            </div>
                            <div>
                                <div class="fs-12 fw-medium text-muted">Lisensi Suspended</div>
                                <h4 class="fw-bold mb-0">{{ $sa['userSuspended'] }}</h4>
                                <small class="fs-11 text-muted">Disuspend super admin</small>
                            </div>
                        </div>
                        <div class="pt-3 mt-3 border-top border-dashed">
                            <div class="d-flex justify-content-between">
                                <span class="fs-11 text-muted">Lisensi akan expired (&lt;30 hari)</span>
                                <span class="badge bg-soft-danger text-danger">{{ $sa['lisensiAkanExpired'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── CHARTS ROW ── --}}
        <div class="row">

            {{-- Chart: User Baru Per Bulan --}}
            <div class="col-xxl-8">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-trending-up me-2 text-primary"></i>
                            Pertumbuhan User Per Bulan
                        </h5>
                        <div class="card-header-action">
                            <span class="badge bg-soft-primary text-primary">{{ date('Y') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="saChartUserGrowth"></div>
                    </div>
                </div>
            </div>

            {{-- Distribusi Paket Lisensi --}}
            <div class="col-xxl-4">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-pie-chart me-2 text-warning"></i>
                            Distribusi Paket Lisensi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="saChartPaket"></div>
                        <div class="mt-3">
                            @foreach($sa['distribusiPaket'] as $paket)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-semibold">{{ $paket['nama'] }}</span>
                                <span class="badge bg-soft-primary text-primary">{{ $paket['jumlah'] }} user</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── REVENUE CHART + RECENT USERS ── --}}
        <div class="row">

            {{-- Chart Revenue Bulanan --}}
            <div class="col-xxl-8">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-dollar-sign me-2 text-success"></i>
                            Revenue Bulanan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="saChartRevenue"></div>
                    </div>
                </div>
            </div>

            {{-- Status Lisensi Summary --}}
            <div class="col-xxl-4">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-shield me-2 text-info"></i>
                            Status Lisensi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="saChartLisensiStatus"></div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:10px;height:10px;border-radius:50%;background:#25B003;"></div>
                                    <small>Aktif</small>
                                </div>
                                <span class="badge bg-soft-success text-success">{{ $sa['lisensiAktif'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:10px;height:10px;border-radius:50%;background:#fc6c00;"></div>
                                    <small>Akan Expired</small>
                                </div>
                                <span class="badge bg-soft-warning text-warning">{{ $sa['lisensiAkanExpired'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:10px;height:10px;border-radius:50%;background:#dc3545;"></div>
                                    <small>Expired</small>
                                </div>
                                <span class="badge bg-soft-danger text-danger">{{ $sa['lisensiExpired'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── USER TERBARU + LISENSI AKAN EXPIRED ── --}}
        <div class="row">

            {{-- User Terbaru --}}
            <div class="col-xxl-6">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-user-plus me-2 text-primary"></i>
                            User Terbaru
                        </h5>
                        <div class="card-header-action">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">
                                Lihat Semua <i class="feather-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Daftar</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sa['userTerbaru'] as $u)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $u->name }}</div>
                                            <small class="text-muted">{{ $u->email }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($u->created_at)->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($u->license_status == 'suspended')
                                                <span class="badge bg-soft-danger text-danger">Suspended</span>
                                            @elseif($u->license_status == 'active')
                                                <span class="badge bg-soft-success text-success">Aktif</span>
                                            @elseif($u->license_status == 'expired')
                                                <span class="badge bg-soft-warning text-warning">Expired</span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary">Belum Berlangganan</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">Belum ada user</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lisensi Akan Expired --}}
            <div class="col-xxl-6">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-alert-triangle me-2 text-warning"></i>
                            Lisensi Akan Expired (≤30 hari)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Paket</th>
                                        <th>Expired</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sa['lisensiMauExpired'] as $lic)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $lic->user->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $lic->user->email ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-info text-info">{{ $lic->package_name ?? $lic->package ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @php $sisaHari = \Carbon\Carbon::now()->diffInDays($lic->end_date, false); @endphp
                                            <span class="badge bg-soft-{{ $sisaHari <= 7 ? 'danger' : 'warning' }} text-{{ $sisaHari <= 7 ? 'danger' : 'warning' }}">
                                                {{ $sisaHari }} hari
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            <i class="feather-check-circle text-success me-1"></i>
                                            Tidak ada yang akan expired
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// Global Variables
const selectedYear = '{{ $selectedYear }}';
const selectedMonth = {{ $selectedMonth }};
const chartJumlahRencanaData = @json($chartJumlahRencana ?? []);
const chartMutasiKasData = @json($chartMutasiKas ?? []);
const totalRencana = {{ $totalRencanaFiltered }};
const selesai = {{ $selesaiFiltered }};
const kategoriData = @json($kategoriAnggaran ?? []);
const perencanaanData = @json($recentPerencanaan ?? []);

// ApexCharts Instances
let jumlahRencanaChart;
let progressChart;
let mutasiChart;
let kategoriChart;
let multiMetricChart;

// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    initChartJumlahRencana();
    initChartProgress();
    initChartMutasi();
    initChartKategori();
    initChartMultiMetric();
    initCalendarView();
});

// Chart 1: Area Chart dengan Gradient - Trend Perencanaan
function initChartJumlahRencana() {
    if (!chartJumlahRencanaData || chartJumlahRencanaData.length === 0) {
        document.querySelector("#chartJumlahRencana").innerHTML = '<div class="alert alert-info">Data perencanaan belum tersedia</div>';
        return;
    }

    const options = {
        series: [{
            name: 'Jumlah Kegiatan',
            data: chartJumlahRencanaData.map(item => item.jumlah)
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: true
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: chartJumlahRencanaData.map(item => item.bulan)
        },
        yaxis: {
            title: {
                text: 'Jumlah Kegiatan'
            }
        },
        colors: ['#3454D1'],
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + ' kegiatan';
                }
            }
        },
        markers: {
            size: 5,
            colors: ['#3454D1'],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: {
                size: 7
            }
        }
    };

    jumlahRencanaChart = new ApexCharts(document.querySelector("#chartJumlahRencana"), options);
    jumlahRencanaChart.render();
}

// Chart 2: Radial Bar - Progress Realisasi
function initChartProgress() {
    const percentage = totalRencana > 0 ? Math.round((selesai / totalRencana) * 100) : 0;

    const options = {
        series: [percentage],
        chart: {
            height: 280,
            type: 'radialBar',
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            }
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                hollow: {
                    size: '70%',
                    background: 'transparent',
                },
                track: {
                    background: '#f2f2f2',
                    strokeWidth: '100%',
                },
                dataLabels: {
                    name: {
                        offsetY: -10,
                        color: '#888',
                        fontSize: '13px'
                    },
                    value: {
                        color: '#111',
                        fontSize: '30px',
                        fontWeight: 'bold',
                        show: true,
                        formatter: function(val) {
                            return val + '%';
                        }
                    }
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'horizontal',
                shadeIntensity: 0.5,
                gradientToColors: ['#25B003'],
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100]
            }
        },
        stroke: {
            lineCap: 'round'
        },
        labels: ['Progress']
    };

    progressChart = new ApexCharts(document.querySelector("#chartProgress"), options);
    progressChart.render();
}

// Chart 3: Area Chart dengan Multiple Series - Cash Flow
let currentChartType = 'area';

function initChartMutasi(chartType = 'area') {
    if (!chartMutasiKasData || chartMutasiKasData.length === 0) {
        document.querySelector("#chartMutasi").innerHTML = '<div class="alert alert-info">Data mutasi kas belum tersedia</div>';
        return;
    }

    const options = {
        series: [
            {
                name: 'Pemasukan',
                data: chartMutasiKasData.map(item => item.pemasukan)
            },
            {
                name: 'Pengeluaran',
                data: chartMutasiKasData.map(item => item.pengeluaran)
            }
        ],
        chart: {
            type: chartType,
            height: 350,
            toolbar: {
                show: true
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: chartType === 'area' || chartType === 'line' ? 3 : 0
        },
        fill: {
            type: chartType === 'area' ? 'gradient' : 'solid',
            opacity: chartType === 'area' ? 0.4 : 1
        },
        xaxis: {
            categories: chartMutasiKasData.map(item => item.bulan)
        },
        yaxis: {
            title: {
                text: 'Jumlah (Rp)'
            },
            labels: {
                formatter: function(val) {
                    if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'Jt';
                    if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'Rb';
                    return 'Rp ' + val;
                }
            }
        },
        colors: ['#25B003', '#FC6C00'],
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                }
            }
        }
    };

    if (mutasiChart) {
        mutasiChart.destroy();
    }

    mutasiChart = new ApexCharts(document.querySelector("#chartMutasi"), options);
    mutasiChart.render();
}

function switchChartType(type) {
    currentChartType = type;
    initChartMutasi(type);

    // Update button states
    document.querySelectorAll('.btn-group button').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
}

const chartKategoriData = @json($chartKategoriData ?? []);

// Chart 4: Radar Chart - Distribusi Kategori
function initChartKategori() {
    const el = document.querySelector("#chartKategori");
    if (!el) return;

    // Jika tidak ada data nominal, tampilkan fallback jumlah kode transaksi
    if (!chartKategoriData || chartKategoriData.length === 0) {
        // Fallback: tampilkan berdasar jumlah kode transaksi per kategori
        const fallbackLabels = kategoriData.map(k => k.nama_kategori || 'Kategori');
        const fallbackValues = kategoriData.map(k => k.kode_transaksi ? k.kode_transaksi.length : 0);

        if (fallbackValues.every(v => v === 0)) {
            el.innerHTML = '<div class="alert alert-info text-center">Belum ada data kategori</div>';
            return;
        }

        renderKategoriChart(el, fallbackLabels, fallbackValues);
        return;
    }

    const labels = chartKategoriData.map(k => k.nama);
    const values = chartKategoriData.map(k => k.nominal);
    renderKategoriChart(el, labels, values);
}

function renderKategoriChart(el, labels, values) {
    const options = {
        series: values,
        chart: {
            type: 'donut',
            height: 280,
        },
        labels: labels,
        colors: ['#25B003', '#17a2b8', '#dc3545', '#fc6c00'],
        legend: {
            position: 'bottom',
            fontSize: '12px',
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function (w) {
                                const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                }
            }
        },
        dataLabels: {
            enabled: false,
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { width: 200 },
                legend: { position: 'bottom' }
            }
        }]
    };

    kategoriChart = new ApexCharts(el, options);
    kategoriChart.render();
}

// Chart 5: Multi-Metric Comparison - Compare Planning, Income, Expense
function initChartMultiMetric() {
    if (!chartJumlahRencanaData || chartJumlahRencanaData.length === 0) {
        document.querySelector("#chartMultiMetric").innerHTML = '<div class="alert alert-info">Data belum tersedia</div>';
        return;
    }

    // Prepare data
    const categories = chartJumlahRencanaData.map(item => item.bulan);
    const jumlahKegiatan = chartJumlahRencanaData.map(item => item.jumlah);

    // Normalize cash flow data to match planning data length
    let pemasukanData = [];
    let pengeluaranData = [];

    if (chartMutasiKasData && chartMutasiKasData.length > 0) {
        // Create a map for easy lookup
        const mutasiMap = {};
        chartMutasiKasData.forEach(item => {
            mutasiMap[item.bulan] = {
                pemasukan: item.pemasukan / 1000000, // Convert to millions
                pengeluaran: item.pengeluaran / 1000000
            };
        });

        // Match with planning data
        categories.forEach(bulan => {
            if (mutasiMap[bulan]) {
                pemasukanData.push(mutasiMap[bulan].pemasukan);
                pengeluaranData.push(mutasiMap[bulan].pengeluaran);
            } else {
                pemasukanData.push(0);
                pengeluaranData.push(0);
            }
        });
    } else {
        pemasukanData = new Array(categories.length).fill(0);
        pengeluaranData = new Array(categories.length).fill(0);
    }

    const options = {
        series: [
            {
                name: 'Jumlah Kegiatan',
                type: 'column',
                data: jumlahKegiatan
            },
            {
                name: 'Pemasukan (Juta)',
                type: 'line',
                data: pemasukanData
            },
            {
                name: 'Pengeluaran (Juta)',
                type: 'line',
                data: pengeluaranData
            }
        ],
        chart: {
            height: 350,
            type: 'line',
            stacked: false,
            toolbar: {
                show: true
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        stroke: {
            width: [0, 3, 3],
            curve: 'smooth'
        },
        plotOptions: {
            bar: {
                columnWidth: '50%'
            }
        },
        fill: {
            opacity: [0.85, 1, 1],
            gradient: {
                inverseColors: false,
                shade: 'light',
                type: "vertical",
                opacityFrom: 0.85,
                opacityTo: 0.55,
                stops: [0, 100, 100, 100]
            }
        },
        labels: categories,
        markers: {
            size: [0, 5, 5],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: {
                size: 7
            }
        },
        xaxis: {
            type: 'category'
        },
        yaxis: [
            {
                title: {
                    text: 'Jumlah Kegiatan'
                },
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            {
                opposite: true,
                title: {
                    text: 'Cash Flow (Juta Rupiah)'
                },
                labels: {
                    formatter: function(val) {
                        return 'Rp ' + val.toFixed(1) + 'Jt';
                    }
                }
            }
        ],
        tooltip: {
            shared: true,
            intersect: false,
            y: [
                {
                    formatter: function(val) {
                        return val + ' kegiatan';
                    }
                },
                {
                    formatter: function(val) {
                        return 'Rp ' + val.toFixed(2) + ' Juta';
                    }
                },
                {
                    formatter: function(val) {
                        return 'Rp ' + val.toFixed(2) + ' Juta';
                    }
                }
            ]
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left'
        },
        colors: ['#3454D1', '#25B003', '#FC6C00']
    };

    multiMetricChart = new ApexCharts(document.querySelector("#chartMultiMetric"), options);
    multiMetricChart.render();
}

// Calendar View - Show Planning Events
function initCalendarView() {
    const calendarEl = document.getElementById('calendarView');
    const upcomingEl = document.getElementById('upcomingEvents');

    if (!calendarEl) return;

    // Get current date or selected year/month
    const currentYear = selectedYear !== 'all' ? parseInt(selectedYear) : new Date().getFullYear();
    const currentMonth = selectedMonth > 0 ? selectedMonth - 1 : new Date().getMonth();

    // Create simple calendar view
    renderMonthCalendar(currentYear, currentMonth);
    renderUpcomingEvents();

    // Calendar navigation buttons
    document.getElementById('btnCalendarMonth')?.addEventListener('click', function() {
        document.querySelectorAll('.btn-group button').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        renderMonthCalendar(currentYear, currentMonth);
    });

    document.getElementById('btnCalendarYear')?.addEventListener('click', function() {
        document.querySelectorAll('.btn-group button').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        renderYearCalendar(currentYear);
    });
}

function renderMonthCalendar(year, month) {
    const calendarEl = document.getElementById('calendarView');
    if (!calendarEl) return;

    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    // Count events for this month
    const eventsThisMonth = perencanaanData.filter(item => {
        return item.tahun == year && item.bulan == (month + 1);
    });

    let html = `
        <div class="text-center mb-3">
            <h5 class="mb-1">${monthNames[month]} ${year}</h5>
            <span class="badge bg-soft-primary text-primary">
                <i class="feather-calendar me-1"></i>
                ${eventsThisMonth.length} Kegiatan
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm text-center mb-0">
                <thead>
                    <tr>
                        <th class="text-danger">Min</th>
                        <th>Sen</th>
                        <th>Sel</th>
                        <th>Rab</th>
                        <th>Kam</th>
                        <th>Jum</th>
                        <th class="text-success">Sab</th>
                    </tr>
                </thead>
                <tbody>`;

    // Get first day of month and days in month
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    let day = 1;
    for (let i = 0; i < 6; i++) {
        html += '<tr>';
        for (let j = 0; j < 7; j++) {
            if (i === 0 && j < firstDay) {
                html += '<td class="text-muted" style="background: #f8f9fa;"></td>';
            } else if (day > daysInMonth) {
                html += '<td class="text-muted" style="background: #f8f9fa;"></td>';
            } else {
                const isToday = day === new Date().getDate() &&
                               month === new Date().getMonth() &&
                               year === new Date().getFullYear();

                const cellClass = isToday ? 'bg-soft-primary text-primary fw-bold' : '';
                html += `<td class="${cellClass}" style="padding: 12px 8px;">${day}</td>`;
                day++;
            }
        }
        html += '</tr>';
        if (day > daysInMonth) break;
    }

    html += '</tbody></table></div>';

    if (eventsThisMonth.length > 0) {
        html += '<div class="mt-3"><div class="alert alert-info mb-0">';
        html += '<i class="feather-info-circle me-2"></i>';
        html += `Ada ${eventsThisMonth.length} kegiatan direncanakan bulan ini`;
        html += '</div></div>';
    }

    calendarEl.innerHTML = html;
}

function renderYearCalendar(year) {
    const calendarEl = document.getElementById('calendarView');
    if (!calendarEl) return;

    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    // Count events per month
    const eventsByMonth = {};
    perencanaanData.forEach(item => {
        if (item.tahun == year) {
            eventsByMonth[item.bulan] = (eventsByMonth[item.bulan] || 0) + 1;
        }
    });

    let html = `
        <div class="text-center mb-4">
            <h5 class="mb-2">Tahun ${year}</h5>
            <span class="badge bg-soft-primary text-primary">
                <i class="feather-calendar me-1"></i>
                ${Object.values(eventsByMonth).reduce((a, b) => a + b, 0)} Total Kegiatan
            </span>
        </div>
        <div class="row g-2">`;

    for (let month = 1; month <= 12; month++) {
        const eventCount = eventsByMonth[month] || 0;
        const hasEvents = eventCount > 0;

        html += `
            <div class="col-4 col-md-3">
                <div class="p-3 border rounded text-center ${hasEvents ? 'bg-soft-primary' : ''}"
                     style="cursor: ${hasEvents ? 'pointer' : 'default'};">
                    <div class="fw-semibold mb-1">${monthNames[month - 1]}</div>
                    ${hasEvents ? `
                        <span class="badge bg-primary">${eventCount}</span>
                    ` : `
                        <small class="text-muted">-</small>
                    `}
                </div>
            </div>`;
    }

    html += '</div>';
    calendarEl.innerHTML = html;
}

function renderUpcomingEvents() {
    const upcomingEl = document.getElementById('upcomingEvents');
    if (!upcomingEl) return;

    // Debug: log the data structure
    console.log('Perencanaan Data:', perencanaanData);

    // Get events from current month onwards
    const now = new Date();
    const currentYear = selectedYear !== 'all' ? parseInt(selectedYear) : now.getFullYear();
    const currentMonth = selectedMonth > 0 ? selectedMonth : now.getMonth() + 1;

    const upcoming = perencanaanData
        .filter(item => {
            if (selectedYear === 'all') {
                return item.tahun >= now.getFullYear();
            }
            return item.tahun == currentYear && item.bulan >= currentMonth;
        })
        .slice(0, 4);

    if (upcoming.length === 0) {
        upcomingEl.innerHTML = `
            <div class="text-center py-3">
                <i class="feather-calendar-x fs-3 text-muted"></i>
                <p class="text-muted mt-2 mb-0 small">Tidak ada kegiatan mendatang</p>
            </div>`;
        return;
    }

    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    let html = '';
    upcoming.forEach((item, index) => {
        console.log('Processing item:', item);

        const colors = ['primary', 'success', 'warning', 'info'];
        const color = colors[index % colors.length];

        // Try multiple ways to get the kegiatan name
        let kegiatanName = 'Kegiatan';

        // Check if perencanaan is nested object
        if (typeof item.perencanaan === 'object' && item.perencanaan !== null) {
            // If it's a nested perencanaan object, try to get the name from it
            kegiatanName = item.perencanaan.judul || item.perencanaan.perencanaan || 'Kegiatan';
        } else if (typeof item.perencanaan === 'string') {
            // If it's already a string, use it
            kegiatanName = item.perencanaan;
        } else if (item.judul) {
            // Fallback to judul
            kegiatanName = item.judul;
        }

        // Get target value safely
        const targetValue = item.target || '-';

        // Get deskripsi safely
        let deskripsiText = '';
        if (item.deskripsi && typeof item.deskripsi === 'string') {
            deskripsiText = item.deskripsi.substring(0, 50);
            if (item.deskripsi.length > 50) deskripsiText += '...';
        }

        html += `
            <div class="d-flex align-items-start gap-3 mb-3 p-3 border-start border-${color} border-3" style="background-color: rgba(var(--bs-${color}-rgb), 0.1);">
                <div class="avatar-text avatar-sm bg-${color} text-white">
                    <i class="feather-calendar"></i>
                </div>
                <div class="flex-fill">
                    <div class="fw-semibold text-dark mb-1">${kegiatanName}</div>
                    <small class="text-muted d-block mb-2">
                        <i class="feather-clock me-1"></i>
                        ${monthNames[item.bulan - 1]} ${item.tahun}
                    </small>
                    ${targetValue !== '-' ? `
                        <span class="badge bg-soft-${color} text-${color}">
                            <i class="feather-target me-1"></i>
                            Target: ${targetValue}
                        </span>
                    ` : ''}
                    ${deskripsiText ? `
                        <div class="mt-2">
                            <small class="text-muted">${deskripsiText}</small>
                        </div>
                    ` : ''}
                </div>
            </div>`;
    });

    upcomingEl.innerHTML = html;
}

// ══════════════════════════════════════════════════════════════
// SUPER ADMIN CHARTS
// ══════════════════════════════════════════════════════════════
@if(Auth::user()->role == 'super_admin')
const saUserGrowthData  = @json($sa['userGrowthChart'] ?? []);
const saRevenueData     = @json($sa['revenueChart'] ?? []);
const saDistribusiPaket = @json($sa['distribusiPaket'] ?? []);
const saLisensiStatus   = {
    aktif        : {{ $sa['lisensiAktif'] }},
    akanExpired  : {{ $sa['lisensiAkanExpired'] }},
    expired      : {{ $sa['lisensiExpired'] }},
};

document.addEventListener('DOMContentLoaded', function () {
    initSaChartUserGrowth();
    initSaChartRevenue();
    initSaChartPaket();
    initSaChartLisensiStatus();
});

function initSaChartUserGrowth() {
    const el = document.querySelector('#saChartUserGrowth');
    if (!el || !saUserGrowthData.length) {
        if (el) el.innerHTML = '<div class="alert alert-info">Belum ada data</div>';
        return;
    }
    new ApexCharts(el, {
        series: [{ name: 'User Baru', data: saUserGrowthData.map(d => d.jumlah) }],
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
        dataLabels: { enabled: false },
        xaxis: { categories: saUserGrowthData.map(d => d.bulan) },
        yaxis: { labels: { formatter: v => Math.round(v) + ' user' } },
        colors: ['#3454D1'],
        tooltip: { y: { formatter: v => v + ' user baru' } },
    }).render();
}

function initSaChartRevenue() {
    const el = document.querySelector('#saChartRevenue');
    if (!el || !saRevenueData.length) {
        if (el) el.innerHTML = '<div class="alert alert-info">Belum ada data revenue</div>';
        return;
    }
    new ApexCharts(el, {
        series: [{ name: 'Revenue', data: saRevenueData.map(d => d.nominal) }],
        chart: { type: 'area', height: 300, toolbar: { show: false },
                 animations: { enabled: true, easing: 'easeinout', speed: 800 } },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.6, opacityTo: 0.1 } },
        xaxis: { categories: saRevenueData.map(d => d.bulan) },
        yaxis: { labels: { formatter: v => 'Rp ' + (v/1000000).toFixed(1) + 'Jt' } },
        colors: ['#25B003'],
        tooltip: { y: { formatter: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } },
    }).render();
}

function initSaChartPaket() {
    const el = document.querySelector('#saChartPaket');
    if (!el || !saDistribusiPaket.length) {
        if (el) el.innerHTML = '<div class="alert alert-info">Belum ada data paket</div>';
        return;
    }
    new ApexCharts(el, {
        series: saDistribusiPaket.map(d => d.jumlah),
        chart: { type: 'donut', height: 240 },
        labels: saDistribusiPaket.map(d => d.nama),
        colors: ['#3454D1', '#25B003', '#fc6c00', '#17a2b8'],
        legend: { position: 'bottom', fontSize: '12px' },
        plotOptions: { pie: { donut: { size: '60%',
            labels: { show: true, total: { show: true, label: 'Total',
                formatter: w => w.globals.seriesTotals.reduce((a,b) => a+b, 0) + ' user'
            }}
        }}},
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: v => v + ' user' } },
    }).render();
}

function initSaChartLisensiStatus() {
    const el = document.querySelector('#saChartLisensiStatus');
    if (!el) return;
    new ApexCharts(el, {
        series: [saLisensiStatus.aktif, saLisensiStatus.akanExpired, saLisensiStatus.expired],
        chart: { type: 'donut', height: 220 },
        labels: ['Aktif', 'Akan Expired', 'Expired'],
        colors: ['#25B003', '#fc6c00', '#dc3545'],
        legend: { show: false },
        plotOptions: { pie: { donut: { size: '65%',
            labels: { show: true, total: { show: true, label: 'Total Lisensi',
                formatter: w => w.globals.seriesTotals.reduce((a,b) => a+b, 0)
            }}
        }}},
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: v => v + ' lisensi' } },
    }).render();
}
@endif

// ══════════════════════════════════════════════════════════════
// FILTER BAR — fungsi utama
// ══════════════════════════════════════════════════════════════
(function () {
    const tahunEl = document.getElementById('fb_tahun');
    const bulanEl = document.getElementById('fb_bulan');
    const loadEl  = document.getElementById('fb_loading');
    const DASHBOARD_URL = '{{ route("dashboard") }}';

    function doApply() {
        const tahun = tahunEl ? tahunEl.value : 'all';
        const bulan = (bulanEl && !bulanEl.disabled) ? bulanEl.value : '0';
        if (loadEl) loadEl.classList.add('show');
        if (tahunEl) tahunEl.disabled = true;
        if (bulanEl) bulanEl.disabled = true;
        window.location.href = DASHBOARD_URL + '?tahun=' + encodeURIComponent(tahun) + '&bulan=' + encodeURIComponent(bulan);
    }

    if (tahunEl) {
        tahunEl.addEventListener('change', function () {
            if (this.value === 'all') {
                if (bulanEl) { bulanEl.disabled = true; bulanEl.value = '0'; }
            } else {
                if (bulanEl) bulanEl.disabled = false;
            }
            doApply();
        });
    }

    if (bulanEl) {
        bulanEl.addEventListener('change', function () {
            if (tahunEl && tahunEl.value !== 'all') doApply();
        });
    }

    window.fbApply   = doApply;
    window.fbReset   = function () {
        if (loadEl) loadEl.classList.add('show');
        window.location.href = DASHBOARD_URL + '?tahun=' + new Date().getFullYear() + '&bulan=0';
    };
    window.fbRefresh = function () {
        const tahun = tahunEl ? tahunEl.value : selectedYear;
        const bulan = (bulanEl && !bulanEl.disabled) ? bulanEl.value : selectedMonth;
        if (loadEl) loadEl.classList.add('show');
        window.location.href = DASHBOARD_URL + '?tahun=' + encodeURIComponent(tahun) + '&bulan=' + encodeURIComponent(bulan);
    };
    window.applyFilter = doApply;
})();

// Refresh Dashboard
function refreshDashboard() {
    window.fbRefresh ? window.fbRefresh() : location.reload();
}

// Download Chart
function downloadChart(chartName) {
    let chart;
    if (chartName === 'chartJumlahRencana') chart = jumlahRencanaChart;
    else if (chartName === 'chartProgress') chart = progressChart;
    else if (chartName === 'chartMutasi') chart = mutasiChart;

    if (chart) {
        chart.dataURI().then(({ imgURI }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = chartName + '-' + selectedYear + '.png';
            link.click();
        });
    }
}
</script>
@endpush