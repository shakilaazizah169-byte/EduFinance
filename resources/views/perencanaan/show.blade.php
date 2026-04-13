@extends('layouts.app')

@section('title', 'Detail Perencanaan')

@section('content')
<div class="nxl-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Detail Perencanaan</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('perencanaan.index') }}">Perencanaan</a></li>
                <li class="breadcrumb-item">Detail</li>
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
                    <a href="{{ route('perencanaan.edit', $perencanaan) }}" class="btn btn-warning">
                        <i class="feather-edit-2 me-2"></i>Edit Perencanaan
                    </a>
                    <a href="{{ route('perencanaan.index') }}" class="btn btn-icon btn-light-brand">
                        <i class="feather-list"></i>
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

    <!-- Main Content -->
    <div class="main-content">
        <!-- Success Message -->
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

        <!-- Info Card -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Informasi Perencanaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <span class="badge bg-primary mb-2">Judul Perencanaan</span>
                                    <h4 class="fw-bold">{{ $perencanaan->judul }}</h4>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="avatar-text bg-soft-primary text-primary">
                                                <i class="feather-calendar"></i>
                                            </div>
                                            <div>
                                                <span class="text-muted d-block">Periode</span>
                                                <span class="fw-semibold">{{ $monthName }} {{ $perencanaan->tahun }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="avatar-text bg-soft-success text-success">
                                                <i class="feather-layers"></i>
                                            </div>
                                            <div>
                                                <span class="text-muted d-block">Jumlah Rencana</span>
                                                <span class="fw-semibold">{{ $perencanaan->details->count() }} Detail</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="avatar-text bg-soft-info text-info">
                                                <i class="feather-clock"></i>
                                            </div>
                                            <div>
                                                <span class="text-muted d-block">Dibuat</span>
                                                <span class="fw-semibold">{{ $perencanaan->created_at->translatedFormat('l, d F Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="avatar-text bg-soft-warning text-warning">
                                                <i class="feather-refresh-cw"></i>
                                            </div>
                                            <div>
                                                <span class="text-muted d-block">Terakhir Diupdate</span>
                                                <span class="fw-semibold">{{ $perencanaan->updated_at->translatedFormat('l, d F Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <div class="avatar-text bg-primary text-white mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                                            {{ strtoupper(substr($perencanaan->judul, 0, 2)) }}
                                        </div>
                                        <h6>Status Perencanaan</h6>
                                        @php
                                            $totalRencana = $perencanaan->details->count();
                                            $totalRealisasi = \App\Models\Realisasi::whereHas('detailPerencanaan', function($q) use ($perencanaan) {
                                                $q->where('perencanaan_id', $perencanaan->id);
                                            })->count();
                                            $progress = $totalRencana > 0 ? round(($totalRealisasi / $totalRencana) * 100) : 0;
                                        @endphp
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Progress Realisasi</span>
                                                <span class="fw-bold">{{ $progress }}%</span>
                                            </div>
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-success" style="width: {{ $progress }}%;"></div>
                                            </div>
                                            <div class="mt-2 text-muted small">
                                                {{ $totalRealisasi }} dari {{ $totalRencana }} rencana telah direalisasikan
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

        <!-- Detail Rencana -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="feather-list me-2"></i>Detail Rencana
                            <span class="badge bg-primary ms-2">{{ $perencanaan->details->count() }} Rencana</span>
                        </h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-soft-success text-success">
                                <i class="feather-check-circle me-1"></i>Sesuai Target
                            </span>
                            <span class="badge bg-soft-warning text-warning">
                                <i class="feather-alert-circle me-1"></i>Sebagian
                            </span>
                            <span class="badge bg-soft-danger text-danger">
                                <i class="feather-x-circle me-1"></i>Tidak Sesuai
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @forelse($perencanaan->details as $index => $detail)
                        @php
                            $realisasi = $detail->realisasi;
                            $statusClass = '';
                            $statusIcon = '';
                            $statusText = 'Belum Direalisasikan';
                            
                            if ($realisasi) {
                                switch($realisasi->status_target) {
                                    case 'sesuai':
                                        $statusClass = 'bg-soft-success text-success';
                                        $statusIcon = 'feather-check-circle';
                                        $statusText = 'Sesuai Target';
                                        break;
                                    case 'sebagian':
                                        $statusClass = 'bg-soft-warning text-warning';
                                        $statusIcon = 'feather-alert-circle';
                                        $statusText = 'Tercapai Sebagian';
                                        break;
                                    case 'tidak':
                                        $statusClass = 'bg-soft-danger text-danger';
                                        $statusIcon = 'feather-x-circle';
                                        $statusText = 'Tidak Sesuai';
                                        break;
                                }
                            }
                        @endphp
                        <div class="border-bottom">
                            <div class="p-4">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="avatar-text bg-light-primary text-primary">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <h6 class="fw-semibold mb-0">Rencana #{{ $index + 1 }}</h6>
                                                    @if($realisasi)
                                                    <span class="badge {{ $statusClass }} py-1 px-2">
                                                        <i class="{{ $statusIcon }} me-1"></i>{{ $statusText }}
                                                    </span>
                                                    @else
                                                    <span class="badge bg-soft-secondary text-secondary py-1 px-2">
                                                        <i class="feather-minus-circle me-1"></i>Belum Realisasi
                                                    </span>
                                                    @endif
                                                </div>
                                                
                                                <h5 class="text-primary mb-3">{{ $detail->perencanaan }}</h5>
                                                
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i class="feather-target text-muted" style="width: 18px;"></i>
                                                            <span class="text-muted">Target:</span>
                                                            <span class="fw-semibold">{{ $detail->target }}</span>
                                                        </div>
                                                    </div>
                                                    @if($realisasi)
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i class="feather-percent text-muted" style="width: 18px;"></i>
                                                            <span class="text-muted">Realisasi:</span>
                                                            <span class="fw-semibold">{{ $realisasi->persentase }}%</span>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                                
                                                @if($detail->deskripsi)
                                                <div class="mt-3">
                                                    <div class="d-flex gap-2">
                                                        <i class="feather-file-text text-muted" style="width: 18px;"></i>
                                                        <div>
                                                            <span class="text-muted d-block mb-1">Deskripsi:</span>
                                                            <p class="mb-0">{{ $detail->deskripsi }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                @if($detail->pelaksanaan)
                                                <div class="mt-3">
                                                    <div class="d-flex gap-2">
                                                        <i class="feather-calendar text-muted" style="width: 18px;"></i>
                                                        <div>
                                                            <span class="text-muted d-block mb-1">Pelaksanaan:</span>
                                                            <p class="mb-0">{{ $detail->pelaksanaan }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($realisasi && $realisasi->lampiran->count() > 0)
                                                <div class="mt-3">
                                                    <div class="d-flex gap-2">
                                                        <i class="feather-paperclip text-muted" style="width: 18px;"></i>
                                                        <div>
                                                            <span class="text-muted d-block mb-1">Lampiran Realisasi:</span>
                                                            <div class="d-flex gap-2 flex-wrap">
                                                                @foreach($realisasi->lampiran as $lampiran)
                                                                <a href="{{ Storage::url($lampiran->file_path) }}" 
                                                                   class="badge bg-soft-primary text-primary py-2 px-3 text-decoration-none"
                                                                   target="_blank">
                                                                    <i class="feather-file me-1"></i>
                                                                    {{ $lampiran->nama_file }}
                                                                </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        @if($realisasi)
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <h6 class="fw-semibold mb-3">Data Realisasi</h6>
                                                <div class="mb-2">
                                                    <span class="text-muted d-block small">Judul Realisasi</span>
                                                    <span class="fw-semibold">{{ $realisasi->judul }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="text-muted d-block small">Tanggal Realisasi</span>
                                                    <span class="fw-semibold">{{ $realisasi->tanggal_realisasi->translatedFormat('d F Y') }}</span>
                                                </div>
                                                <div class="mt-3">
                                                    <a href="{{ route('realisasi.show', $realisasi) }}" 
                                                       class="btn btn-sm btn-primary w-100">
                                                        <i class="feather-eye me-2"></i>Lihat Realisasi
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="card bg-light border-0">
                                            <div class="card-body text-center py-4">
                                                <i class="feather-inbox fs-1 text-muted mb-2"></i>
                                                <p class="text-muted mb-3">Belum ada realisasi</p>
                                                <a href="{{ route('realisasi.create', ['detail_id' => $detail->id]) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="feather-plus me-2"></i>Buat Realisasi
                                                </a>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="feather-inbox fs-1 text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada detail perencanaan</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('perencanaan.index') }}" class="btn btn-light">
                    <i class="feather-arrow-left me-2"></i>Kembali ke Daftar Perencanaan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Avatar Styles */
.avatar-text {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 600;
}

.avatar-text.bg-soft-primary {
    background-color: rgba(52, 84, 209, 0.1);
    color: #3454D1;
}

.avatar-text.bg-soft-success {
    background-color: rgba(37, 176, 3, 0.1);
    color: #25B003;
}

.avatar-text.bg-soft-info {
    background-color: rgba(13, 202, 240, 0.1);
    color: #0dcaf0;
}

.avatar-text.bg-soft-warning {
    background-color: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

.avatar-text.bg-light-primary {
    background-color: rgba(52, 84, 209, 0.1);
    color: #3454D1;
}

/* Card Styles */
.card {
    border: none;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    border-radius: 16px;
}

.card-header {
    background-color: transparent;
    border-bottom: 1px solid #e9ecef;
    padding: 1.25rem 1.5rem;
}

.card-header .card-title {
    margin-bottom: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #334155;
}

.card-body {
    padding: 1.5rem;
}

/* Badge Styles */
.badge.bg-primary {
    background-color: #3454D1 !important;
    color: white;
    font-weight: 500;
    padding: 0.5rem 1rem;
}

.badge.bg-soft-success {
    background-color: rgba(37, 176, 3, 0.1);
    color: #25B003;
    font-weight: 500;
}

.badge.bg-soft-warning {
    background-color: rgba(255, 193, 7, 0.1);
    color: #856404;
    font-weight: 500;
}

.badge.bg-soft-danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    font-weight: 500;
}

.badge.bg-soft-secondary {
    background-color: rgba(108, 117, 125, 0.1);
    color: #6c757d;
    font-weight: 500;
}

/* Progress Bar */
.progress {
    background-color: #e9ecef;
    border-radius: 999px;
    height: 10px;
}

.progress-bar {
    border-radius: 999px;
    background-color: #25B003;
}

/* Table Styles */
.table-borderless tr {
    border: none;
}

.table-borderless td,
.table-borderless th {
    border: none;
    padding: 0.5rem 0;
}

/* Border Bottom */
.border-bottom:last-child {
    border-bottom: none !important;
}

/* Button Styles */
.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.btn-warning:hover {
    background-color: #ffca2c;
    border-color: #ffca2c;
    color: #000;
}

.btn-light {
    background-color: #f8f9fa;
    border-color: #f8f9fa;
    color: #6c757d;
}

.btn-light:hover {
    background-color: #e9ecef;
    border-color: #e9ecef;
    color: #495057;
}

/* Alert Styles */
.alert-success {
    background-color: rgba(37, 176, 3, 0.05);
    border: 1px solid rgba(37, 176, 3, 0.1);
    color: #25B003;
    border-radius: 12px;
}

/* Info Card */
.card.bg-light {
    background-color: #f8fafc !important;
}

/* Responsive */
@media (max-width: 768px) {
    .avatar-text {
        width: 36px;
        height: 36px;
        font-size: 14px;
    }
    
    .card-header {
        padding: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .badge {
        font-size: 0.75rem;
    }
}
</style>
@endpush