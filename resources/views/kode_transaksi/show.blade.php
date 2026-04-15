@extends('layouts.app')

@section('title', 'Detail Kode Mutasi')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10 mb-0">Detail Kode Mutasi</h5>
            </div>
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kode-transaksi.index') }}">Kode Mutasi</a></li>
                <li class="breadcrumb-item active">Detail</li>
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
                    <a href="{{ route('kode-transaksi.edit', $kodeTransaksi->kode_transaksi_id) }}" class="btn btn-warning">
                        <i class="feather-edit me-2"></i>
                        <span>Edit Kode</span>
                    </a>
                    <a href="{{ route('kode-transaksi.index') }}" class="btn btn-outline-secondary">
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
        <div class="row g-4">
            <!-- kolom kiri - detail utama -->
            <div class="col-lg-8">
                <div class="card table-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-info me-2 text-primary"></i>Informasi Lengkap Kode Mutasi
                        </h5>
                        <span class="badge badge-secondary-light">ID: {{ $kodeTransaksi->kode_transaksi_id }}</span>
                    </div>
                    <div class="card-body">
                        <!-- alert informasi -->
                        <div class="alert alert-info-custom mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <i class="feather-info fs-4 text-primary mt-1"></i>
                                <div>
                                    <strong>Informasi Kode Mutasi</strong>
                                    <p class="mb-0 mt-1 small">
                                        Kode ini digunakan untuk mengidentifikasi transaksi di modul Mutasi Kas.
                                        Total <strong>{{ $kodeTransaksi->mutasiKas->count() }}</strong> transaksi menggunakan kode ini.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- detail grid -->
                        <div class="row g-4">
                            <!-- kode -->
                            <div class="col-md-6">
                                <div class="detail-info-box">
                                    <div class="detail-info-label">
                                        <i class="feather-hash"></i> Kode Mutasi
                                    </div>
                                    <div class="detail-info-value">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-initial bg-primary-soft text-primary">
                                                {{ substr($kodeTransaksi->kode, 0, 2) }}
                                            </div>
                                            <span class="fs-2 fw-bold text-primary">{{ $kodeTransaksi->kode }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- kategori -->
                            <div class="col-md-6">
                                <div class="detail-info-box">
                                    <div class="detail-info-label">
                                        <i class="feather-tag"></i> Kategori
                                    </div>
                                    <div class="detail-info-value">
                                        @if($kodeTransaksi->kategori)
                                            @php
                                                $badgeClass = 'badge-primary-light';
                                                $badgeIcon = 'tag';
                                                
                                                if(str_contains(strtolower($kodeTransaksi->kategori->nama_kategori), 'penerimaan')) {
                                                    $badgeClass = 'badge-success-light';
                                                    $badgeIcon = 'trending-up';
                                                } elseif(str_contains(strtolower($kodeTransaksi->kategori->nama_kategori), 'pengeluaran')) {
                                                    $badgeClass = 'badge-danger-light';
                                                    $badgeIcon = 'trending-down';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }} fs-6 px-3 py-2">
                                                <i class="feather-{{ $badgeIcon }} me-2"></i>
                                                {{ $kodeTransaksi->kategori->nama_kategori }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary-light">Tidak ada kategori</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- keterangan -->
                            <div class="col-12">
                                <div class="detail-info-box">
                                    <div class="detail-info-label">
                                        <i class="feather-file-text"></i> Keterangan / Deskripsi
                                    </div>
                                    <div class="detail-info-value">
                                        <div class="p-3 bg-light rounded-3">
                                            <p class="mb-0">{{ $kodeTransaksi->keterangan }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- waktu dibuat -->
                            <div class="col-md-6">
                                <div class="detail-info-box">
                                    <div class="detail-info-label">
                                        <i class="feather-calendar"></i> Dibuat Pada
                                    </div>
                                    <div class="detail-info-value">
                                        <div class="fw-semibold">{{ $kodeTransaksi->created_at ? $kodeTransaksi->created_at->format('d F Y') : '-' }}</div>
                                        <small class="text-muted">{{ $kodeTransaksi->created_at ? $kodeTransaksi->created_at->format('H:i:s') : '' }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- terakhir diupdate -->
                            <div class="col-md-6">
                                <div class="detail-info-box">
                                    <div class="detail-info-label">
                                        <i class="feather-refresh-cw"></i> Terakhir Diupdate
                                    </div>
                                    <div class="detail-info-value">
                                        <div class="fw-semibold">{{ $kodeTransaksi->updated_at ? $kodeTransaksi->updated_at->format('d F Y') : '-' }}</div>
                                        <small class="text-muted">{{ $kodeTransaksi->updated_at ? $kodeTransaksi->updated_at->format('H:i:s') : '' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- statistik transaksi -->
                        <div class="row g-3 mt-3">
                            <div class="col-12">
                                <h6 class="fw-semibold mb-3">
                                    <i class="feather-bar-chart-2 me-2 text-primary"></i>Ringkasan Transaksi
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-mini bg-primary-soft">
                                    <div class="stat-card-mini-icon">
                                        <i class="feather-repeat"></i>
                                    </div>
                                    <div class="stat-card-mini-content">
                                        <div class="stat-card-mini-label">Total Transaksi</div>
                                        <div class="stat-card-mini-value">{{ number_format($kodeTransaksi->mutasiKas->count(), 0, ',', '.') }}</div>
                                        <small class="text-muted">kali digunakan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-mini bg-success-soft">
                                    <div class="stat-card-mini-icon">
                                        <i class="feather-trending-up"></i>
                                    </div>
                                    <div class="stat-card-mini-content">
                                        <div class="stat-card-mini-label">Total Debit</div>
                                        <div class="stat-card-mini-value text-success">Rp {{ number_format($kodeTransaksi->mutasiKas->sum('debit'), 0, ',', '.') }}</div>
                                        <small class="text-muted">pemasukan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-mini bg-danger-soft">
                                    <div class="stat-card-mini-icon">
                                        <i class="feather-trending-down"></i>
                                    </div>
                                    <div class="stat-card-mini-content">
                                        <div class="stat-card-mini-label">Total Kredit</div>
                                        <div class="stat-card-mini-value text-danger">Rp {{ number_format($kodeTransaksi->mutasiKas->sum('kredit'), 0, ',', '.') }}</div>
                                        <small class="text-muted">pengeluaran</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- action buttons -->
                        <hr class="my-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="pagination-info">
                                <i class="feather-info me-1"></i>
                                Pastikan data yang diubah sudah benar
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('kode-transaksi.create') }}" class="btn btn-outline-primary">
                                    <i class="feather-plus me-2"></i>Tambah Baru
                                </a>
                                <a href="{{ route('kode-transaksi.edit', $kodeTransaksi->kode_transaksi_id) }}" class="btn btn-warning">
                                    <i class="feather-edit me-2"></i>Edit Kode
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteKode({{ $kodeTransaksi->kode_transaksi_id }}, '{{ addslashes($kodeTransaksi->kode) }}')">
                                    <i class="feather-trash-2 me-2"></i>Hapus
                                </button>
                                <form id="delete-form-{{ $kodeTransaksi->kode_transaksi_id }}" 
                                      action="{{ route('kode-transaksi.destroy', $kodeTransaksi->kode_transaksi_id) }}" 
                                      method="POST" 
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- kolom kanan - sidebar -->
            <div class="col-lg-4">
                <!-- card detail ringkas -->
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-info me-2 text-primary"></i>Detail Ringkas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">ID Kode Mutasi</div>
                            <div class="info-value-detail">
                                <span class="badge badge-primary-light">{{ $kodeTransaksi->kode_transaksi_id }}</span>
                            </div>
                        </div>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Kode Saat Ini</div>
                            <div class="info-value-detail fw-bold fs-5">{{ $kodeTransaksi->kode }}</div>
                        </div>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Kategori</div>
                            <div class="info-value-detail">
                                @if($kodeTransaksi->kategori)
                                    <span class="badge badge-info-light">{{ $kodeTransaksi->kategori->nama_kategori }}</span>
                                @else
                                    <span class="badge badge-secondary-light">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Total Transaksi</div>
                            <div class="info-value-detail">
                                <span class="badge badge-success-light">{{ number_format($kodeTransaksi->mutasiKas->count(), 0, ',', '.') }} transaksi</span>
                            </div>
                        </div>
                        <hr>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Dibuat Pada</div>
                            <div class="info-value-detail small">{{ $kodeTransaksi->created_at ? $kodeTransaksi->created_at->format('d F Y, H:i') : '-' }}</div>
                        </div>
                        <div class="info-item-detail">
                            <div class="info-label-detail">Terakhir Diubah</div>
                            <div class="info-value-detail small">{{ $kodeTransaksi->updated_at ? $kodeTransaksi->updated_at->format('d F Y, H:i') : '-' }}</div>
                        </div>
                    </div>
                </div>

                <!-- card transaksi terkait -->
                @if($kodeTransaksi->mutasiKas->count() > 0)
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-credit-card me-2 text-primary"></i>Ringkasan Keuangan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Total Debit</div>
                            <div class="info-value-detail text-success fw-semibold">
                                Rp {{ number_format($kodeTransaksi->mutasiKas->sum('debit'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Total Kredit</div>
                            <div class="info-value-detail text-danger fw-semibold">
                                Rp {{ number_format($kodeTransaksi->mutasiKas->sum('kredit'), 0, ',', '.') }}
                            </div>
                        </div>
                        <hr>
                        <div class="small text-muted">
                            <i class="feather-info me-1"></i>
                            {{ $kodeTransaksi->mutasiKas->count() }} transaksi menggunakan kode ini
                        </div>
                    </div>
                </div>

                <!-- card 5 transaksi terakhir -->
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-clock me-2 text-primary"></i>5 Transaksi Terakhir
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group-custom">
                            @foreach($kodeTransaksi->mutasiKas->sortByDesc('tanggal')->take(5) as $mutasi)
                            <div class="list-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="small text-muted">{{ $mutasi->tanggal->format('d/m/Y') }}</div>
                                        <div class="small">{{ Str::limit($mutasi->uraian, 30) }}</div>
                                    </div>
                                    <div>
                                        @if($mutasi->debit > 0)
                                            <span class="badge badge-success-light">
                                                +{{ number_format($mutasi->debit, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="badge badge-danger-light">
                                                -{{ number_format($mutasi->kredit, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('mutasi-kas.index', ['kode_transaksi_id' => $kodeTransaksi->kode_transaksi_id]) }}" class="small">
                            Lihat Semua Transaksi <i class="feather-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                @endif

                <!-- danger zone -->
                <div class="card border-danger">
                    <div class="card-header bg-danger-soft border-danger">
                        <h5 class="mb-0 text-danger">
                            <i class="feather-alert-triangle me-2"></i>Zona Berbahaya
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            Menghapus kode transaksi akan mempengaruhi semua transaksi yang terkait.
                        </p>
                        @if($kodeTransaksi->mutasiKas->count() > 0)
                            <div class="alert alert-warning-custom small mb-3">
                                <i class="feather-alert-circle me-1"></i>
                                Kode ini memiliki <strong>{{ $kodeTransaksi->mutasiKas->count() }} transaksi</strong> terkait
                            </div>
                        @endif
                        <button type="button" 
                                class="btn btn-outline-danger w-100" 
                                onclick="deleteKode({{ $kodeTransaksi->kode_transaksi_id }}, '{{ addslashes($kodeTransaksi->kode) }}')">
                            <i class="feather-trash-2 me-2"></i>Hapus Kode Mutasi
                        </button>
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

.border-danger {
    border: 1px solid rgba(220, 53, 69, 0.2) !important;
}

/* ============================================
   DETAIL INFO BOX
   ============================================ */
.detail-info-box {
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 1rem;
}

.detail-info-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.detail-info-label i {
    margin-right: 0.25rem;
}

.detail-info-value {
    font-size: 0.875rem;
    color: #2c3e50;
}

/* ============================================
   STAT CARD MINI
   ============================================ */
.stat-card-mini {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 0.75rem;
}

.stat-card-mini-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    background-color: white;
    color: inherit;
}

.stat-card-mini-icon i {
    font-size: 1.5rem;
}

.stat-card-mini-content {
    flex: 1;
}

.stat-card-mini-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.stat-card-mini-value {
    font-size: 1.25rem;
    font-weight: 700;
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

.btn-outline-primary {
    border-radius: 0.625rem;
    padding: 0.625rem 1rem;
    border-color: #e2e8f0;
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
    border-color: #e2e8f0;
    color: var(--danger-color);
    transition: all 0.2s ease;
}

.btn-outline-danger:hover {
    background-color: var(--danger-color);
    border-color: transparent;
    color: white;
    transform: translateY(-1px);
}

.btn-warning {
    background-color: #ffc107;
    border: none;
    border-radius: 0.625rem;
    padding: 0.625rem 1rem;
    font-weight: 500;
    transition: all 0.2s ease;
    color: #2c3e50;
}

.btn-warning:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.25);
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
   ALERT STYLES
   ============================================ */
.alert-info-custom {
    background: linear-gradient(135deg, rgba(52, 84, 209, 0.08) 0%, rgba(52, 84, 209, 0.05) 100%);
    border: 1px solid rgba(52, 84, 209, 0.15);
    border-radius: 1rem;
    padding: 1rem 1.25rem;
}

.alert-warning-custom {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.08) 0%, rgba(255, 193, 7, 0.05) 100%);
    border: 1px solid rgba(255, 193, 7, 0.15);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
}

/* ============================================
   INFO ITEM DETAIL
   ============================================ */
.info-item-detail {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.info-label-detail {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.info-value-detail {
    font-size: 0.875rem;
    color: #2c3e50;
}

/* ============================================
   LIST GROUP CUSTOM
   ============================================ */
.list-group-custom {
    padding: 0;
}

.list-item {
    padding: 0.75rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.list-item:last-child {
    border-bottom: none;
}

/* ============================================
   BADGES
   ============================================ */
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

/* ============================================
   SOFT BACKGROUNDS
   ============================================ */
.bg-primary-soft { background-color: rgba(52, 84, 209, 0.1); }
.bg-success-soft { background-color: rgba(37, 176, 3, 0.1); }
.bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
.bg-danger-soft { background-color: rgba(220, 53, 69, 0.08); }

/* Avatar Initial */
.avatar-initial {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 1.25rem;
}

/* ============================================
   PAGINATION INFO
   ============================================ */
.pagination-info {
    font-size: 0.75rem;
    color: #6c757d;
}

/* ============================================
   HR STYLES
   ============================================ */
hr {
    border: none;
    border-top: 1px solid var(--border-color);
    margin: 1.5rem 0;
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

    .card-body {
        padding: 1rem;
    }

    .info-item-detail {
        flex-direction: column;
        align-items: flex-start;
    }

    .stat-card-mini {
        flex-direction: column;
        text-align: center;
    }

    .avatar-initial {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    .detail-info-value .fs-2 {
        font-size: 1.5rem;
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
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function refreshPage() {
    location.reload();
}

function deleteKode(id, kode) {
    Swal.fire({
        title: 'Hapus Kode Mutasi?',
        html: `
            <div class="text-start">
                <p>Apakah Anda yakin ingin menghapus kode ini?</p>
                <div class="alert alert-light border p-3 rounded-3">
                    <div class="mb-0">
                        <strong class="text-primary">Kode:</strong>
                        <span>${kode}</span>
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