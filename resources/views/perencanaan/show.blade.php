@extends('layouts.app')

@section('title', 'Detail Perencanaan')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10 mb-0">Detail Perencanaan</h5>
            </div>
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('perencanaan.index') }}">Perencanaan</a></li>
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
                    <a href="{{ route('perencanaan.edit', $perencanaan) }}" class="btn btn-warning">
                        <i class="feather-edit me-2"></i>
                        <span>Edit Perencanaan</span>
                    </a>
                    <a href="{{ route('perencanaan.index') }}" class="btn btn-outline-secondary">
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
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="feather-check-circle me-2"></i>
                <strong>Berhasil!</strong>
            </div>
            <p class="mb-0 mt-1">{{ session('success') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @php
            $totalRencana = $perencanaan->details->count();
            $totalRealisasi = \App\Models\Realisasi::whereHas('detailPerencanaan', function($q) use ($perencanaan) {
                $q->where('perencanaan_id', $perencanaan->id);
            })->count();
            $progress = $totalRencana > 0 ? round(($totalRealisasi / $totalRencana) * 100) : 0;
            
            $statusClass = 'badge-primary-light';
            $statusText = 'Berjalan';
            if ($progress == 100) {
                $statusClass = 'badge-success-light';
                $statusText = 'Selesai';
            } elseif ($progress == 0) {
                $statusClass = 'badge-secondary-light';
                $statusText = 'Belum Dimulai';
            }
        @endphp

        <div class="row g-4">
            <!-- kolom kiri - detail utama -->
            <div class="col-lg-8">
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-info me-2 text-primary"></i>Informasi Lengkap Perencanaan
                        </h5>
                        <span class="badge badge-secondary-light">ID: {{ $perencanaan->id }}</span>
                    </div>
                    <div class="card-body">
                        <!-- alert informasi -->
                        <div class="alert alert-info-custom mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <i class="feather-info fs-4 text-primary mt-1"></i>
                                <div>
                                    <strong>Informasi Perencanaan</strong>
                                    <p class="mb-0 mt-1 small">
                                        Perencanaan ini memuat daftar rencana anggaran dan kegiatan untuk periode <strong>{{ $monthName }} {{ $perencanaan->tahun }}</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- detail grid -->
                        <div class="row g-4">
                            <!-- judul -->
                            <div class="col-md-6">
                                <div class="detail-info-box">
                                    <div class="detail-info-label">
                                        <i class="feather-bookmark"></i> Judul Perencanaan
                                    </div>
                                    <div class="detail-info-value">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-initial bg-primary-soft text-primary">
                                                {{ strtoupper(substr($perencanaan->judul, 0, 2)) }}
                                            </div>
                                            <span class="fs-4 fw-bold text-primary">{{ $perencanaan->judul }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- periode -->
                            <div class="col-md-6">
                                <div class="detail-info-box">
                                    <div class="detail-info-label">
                                        <i class="feather-calendar"></i> Periode
                                    </div>
                                    <div class="detail-info-value">
                                        <span class="badge badge-info-light fs-6 px-3 py-2">
                                            <i class="feather-clock me-2"></i>
                                            {{ $monthName }} {{ $perencanaan->tahun }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- dibuat pada -->
                            <div class="col-md-6">
                                <div class="detail-info-box">
                                    <div class="detail-info-label">
                                        <i class="feather-calendar"></i> Dibuat Pada
                                    </div>
                                    <div class="detail-info-value">
                                        <div class="fw-semibold">{{ $perencanaan->created_at ? $perencanaan->created_at->translatedFormat('d F Y') : '-' }}</div>
                                        <small class="text-muted">{{ $perencanaan->created_at ? $perencanaan->created_at->format('H:i:s') : '' }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- diupdate pada -->
                            <div class="col-md-6">
                                <div class="detail-info-box">
                                    <div class="detail-info-label">
                                        <i class="feather-refresh-cw"></i> Terakhir Diupdate
                                    </div>
                                    <div class="detail-info-value">
                                        <div class="fw-semibold">{{ $perencanaan->updated_at ? $perencanaan->updated_at->translatedFormat('d F Y') : '-' }}</div>
                                        <small class="text-muted">{{ $perencanaan->updated_at ? $perencanaan->updated_at->format('H:i:s') : '' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- statistik -->
                        <div class="row g-3 mt-3">
                            <div class="col-12">
                                <h6 class="fw-semibold mb-3">
                                    <i class="feather-bar-chart-2 me-2 text-primary"></i>Ringkasan Pelaksanaan
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-mini bg-primary-soft">
                                    <div class="stat-card-mini-icon">
                                        <i class="feather-list"></i>
                                    </div>
                                    <div class="stat-card-mini-content">
                                        <div class="stat-card-mini-label">Total Rencana</div>
                                        <div class="stat-card-mini-value">{{ $totalRencana }}</div>
                                        <small class="text-muted">item</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-mini bg-success-soft">
                                    <div class="stat-card-mini-icon">
                                        <i class="feather-check-square"></i>
                                    </div>
                                    <div class="stat-card-mini-content">
                                        <div class="stat-card-mini-label">Selesai</div>
                                        <div class="stat-card-mini-value text-success">{{ $totalRealisasi }}</div>
                                        <small class="text-muted">terealisasi</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-mini bg-warning-soft">
                                    <div class="stat-card-mini-icon text-warning">
                                        <i class="feather-percent"></i>
                                    </div>
                                    <div class="stat-card-mini-content">
                                        <div class="stat-card-mini-label">Progress</div>
                                        <div class="stat-card-mini-value text-warning">{{ $progress }}%</div>
                                        <small class="text-muted">pencapaian</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Rencana -->
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-list me-2 text-primary"></i>Detail Rencana
                            <span class="badge badge-primary-light ms-2">{{ $perencanaan->details->count() }} Rencana</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group-custom">
                            @forelse($perencanaan->details as $index => $detail)
                            @php
                                $realisasi = $detail->realisasi->first();
                                $rStatusClass = '';
                                $rStatusIcon = '';
                                $rStatusText = 'Belum Direalisasikan';
                                
                                if ($realisasi) {
                                    switch($realisasi->status_target) {
                                        case 'sesuai':
                                            $rStatusClass = 'badge-success-light';
                                            $rStatusIcon = 'feather-check-circle';
                                            $rStatusText = 'Sesuai Target';
                                            break;
                                        case 'sebagian':
                                            $rStatusClass = 'badge-warning-light';
                                            $rStatusIcon = 'feather-alert-circle';
                                            $rStatusText = 'Tercapai Sebagian';
                                            break;
                                        case 'tidak':
                                            $rStatusClass = 'badge-danger-light';
                                            $rStatusIcon = 'feather-x-circle';
                                            $rStatusText = 'Tidak Sesuai';
                                            break;
                                    }
                                } else {
                                    $rStatusClass = 'badge-secondary-light';
                                    $rStatusIcon = 'feather-minus-circle';
                                }
                            @endphp
                            <div class="list-item">
                                <div class="row align-items-center">
                                    <div class="col-md-8 mb-3 mb-md-0">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="avatar-initial bg-primary-soft text-primary" style="width: 40px; height: 40px;">
                                                {{ $index + 1 }}
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-primary">{{ $detail->perencanaan }}</h6>
                                                
                                                <div class="d-flex flex-wrap gap-2 mb-2">
                                                    <span class="badge {{ $rStatusClass }}">
                                                        <i class="{{ $rStatusIcon }} me-1"></i>{{ $rStatusText }}
                                                    </span>
                                                    <span class="badge badge-info-light">
                                                        <i class="feather-target me-1"></i>Target: {{ $detail->target }}
                                                    </span>
                                                    @if($realisasi)
                                                    <span class="badge badge-success-light">
                                                        <i class="feather-percent me-1"></i>Realisasi: {{ $realisasi->persentase }}%
                                                    </span>
                                                    @endif
                                                </div>
                                                
                                                @if($detail->deskripsi)
                                                <p class="small text-muted mb-1"><i class="feather-file-text me-1"></i> {{ $detail->deskripsi }}</p>
                                                @endif
                                                
                                                @if($detail->pelaksanaan)
                                                <p class="small text-muted mb-0"><i class="feather-calendar me-1"></i> {{ $detail->pelaksanaan }}</p>
                                                @endif
                                                
                                                @if($realisasi && $realisasi->lampiran->count() > 0)
                                                <div class="mt-2 d-flex flex-wrap gap-1">
                                                    @foreach($realisasi->lampiran as $lampiran)
                                                    <a href="{{ Storage::url($lampiran->file_path) }}" class="badge badge-primary-light text-decoration-none" target="_blank">
                                                        <i class="feather-paperclip me-1"></i> {{ $lampiran->nama_file }}
                                                    </a>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        @if($realisasi)
                                            <div class="mb-2">
                                                <span class="d-block small text-muted">Tanggal Realisasi</span>
                                                <span class="fw-semibold">{{ $realisasi->tanggal_realisasi->translatedFormat('d M Y') }}</span>
                                            </div>
                                            <a href="{{ route('realisasi.show', $realisasi) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="feather-eye me-1"></i> Lihat Data
                                            </a>
                                        @else
                                            <a href="{{ route('realisasi.create', ['detail_id' => $detail->id]) }}" class="btn btn-sm btn-primary">
                                                <i class="feather-plus me-1"></i> Buat Realisasi
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <i class="feather-inbox fs-1 text-muted mb-2"></i>
                                <p class="text-muted">Belum ada detail rencana</p>
                            </div>
                            @endforelse
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
                            <div class="info-label-detail">Tahun</div>
                            <div class="info-value-detail fw-bold fs-5">{{ $perencanaan->tahun }}</div>
                        </div>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Bulan</div>
                            <div class="info-value-detail">
                                <span class="badge badge-info-light">{{ $monthName }}</span>
                            </div>
                        </div>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Status Perencanaan</div>
                            <div class="info-value-detail">
                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold">Progress</span>
                                <span class="small fw-bold">{{ $progress }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
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

.card-body {
    padding: 1.5rem;
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
    padding: 1.25rem 1.5rem;
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

.badge-warning-light {
    background-color: rgba(255, 193, 7, 0.08);
    color: #d39e00;
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
.bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }

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

    .detail-info-value .fs-4 {
        font-size: 1.25rem !important;
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
</script>
@endpush