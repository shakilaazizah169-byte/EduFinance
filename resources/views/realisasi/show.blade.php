@extends('layouts.app')

@section('title', 'Detail Realisasi')

@push('styles')
<style>
    /* ======================================== */
    /* VARIABLES & RESET */
    /* ======================================== */
    :root {
        --primary: #3454D1;
        --success: #25B003;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #17a2b8;
        --dark: #334155;
        --light: #f8fafc;
        --border: #e9ecef;
    }

    /* ======================================== */
    /* CARD STYLES */
    /* ======================================== */
    .detail-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        transition: all 0.2s ease;
        background: white;
    }
    
    .detail-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
    }
    
    .detail-card .card-header {
        background: white;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
    }
    
    .detail-card .card-body {
        padding: 1.5rem;
    }

    /* ======================================== */
    /* SECTION HEADER */
    /* ======================================== */
    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 0;
    }
    
    .section-header .sh-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    
    .section-header .sh-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    /* ======================================== */
    /* BADGE STATUS */
    /* ======================================== */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    
    .badge-sesuai {
        background: rgba(37, 176, 3, 0.1);
        color: var(--success);
    }
    
    .badge-tidak {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger);
    }
    
    .badge-sebagian {
        background: rgba(255, 193, 7, 0.1);
        color: #856404;
    }

    /* ======================================== */
    /* INFO ROW */
    /* ======================================== */
    .info-row {
        display: flex;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-row .info-label {
        width: 140px;
        min-width: 140px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-row .info-label i {
        font-size: 14px;
        color: var(--primary);
    }
    
    .info-row .info-value {
        flex: 1;
        font-size: 0.9rem;
        color: var(--dark);
        font-weight: 500;
        line-height: 1.6;
    }

    /* ======================================== */
    /* PROGRESS SECTION */
    /* ======================================== */
    .progress-section {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
    }
    
    .pct-label {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -1px;
    }
    
    .progress-big {
        height: 10px;
        border-radius: 999px;
        background: #e9ecef;
        overflow: hidden;
        margin: 0.75rem 0;
    }
    
    .progress-big .bar {
        height: 100%;
        border-radius: 999px;
        transition: width 0.8s ease;
        position: relative;
        overflow: hidden;
    }
    
    .progress-big .bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        to {
            left: 100%;
        }
    }

    /* ======================================== */
    /* NOTE STYLES */
    /* ======================================== */
    .note-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.25rem;
        position: relative;
    }
    
    .note-box::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        border-radius: 3px 0 0 3px;
    }
    
    .note-box.target-note::before {
        background: var(--warning);
    }
    
    .note-box.additional-note::before {
        background: var(--info);
    }
    
    .note-box .note-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0.75rem;
    }
    
    .note-box .note-header i {
        font-size: 1rem;
    }
    
    .note-box .note-header span {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }
    
    .note-box .note-content {
        font-size: 0.9rem;
        color: var(--dark);
        line-height: 1.7;
        white-space: pre-wrap;
        margin: 0;
    }

    /* ======================================== */
    /* LAMPIRAN STYLES */
    /* ======================================== */
    .lampiran-item {
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s ease;
        background: white;
    }
    
    .lampiran-item:hover {
        border-color: var(--primary);
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 84, 209, 0.1);
    }
    
    .lampiran-thumb {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        object-fit: cover;
    }
    
    .lampiran-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .li-pdf {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger);
    }
    
    .li-doc {
        background: rgba(52, 84, 209, 0.1);
        color: var(--primary);
    }
    
    .li-xls {
        background: rgba(37, 176, 3, 0.1);
        color: var(--success);
    }
    
    .li-other {
        background: #f1f5f9;
        color: #64748b;
    }
    
    .lampiran-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 160px;
    }
    
    .lampiran-size {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 2px;
    }
    
    .btn-dl {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        text-decoration: none;
        flex-shrink: 0;
        transition: all 0.15s;
    }
    
    .btn-dl:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    /* ======================================== */
    /* META CARD */
    /* ======================================== */
    .meta-card {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.25rem;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.85rem;
        color: #64748b;
    }
    
    .meta-item + .meta-item {
        margin-top: 0.75rem;
    }
    
    .meta-item i {
        width: 28px;
        height: 28px;
        background: white;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: var(--primary);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        flex-shrink: 0;
    }
    
    .meta-item strong {
        color: var(--dark);
        font-weight: 600;
    }

    /* ======================================== */
    /* BUTTON STYLES */
    /* ======================================== */
    .btn-edit {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border: none;
        color: white;
        border-radius: 10px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
    }
    
    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.3);
        color: white;
    }
    
    .btn-hapus {
        background: white;
        border: 1.5px solid var(--danger);
        color: var(--danger);
        border-radius: 10px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .btn-hapus:hover {
        background: var(--danger);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(220, 53, 69, 0.2);
    }
    
    .btn-back {
        background: white;
        border: 1.5px solid var(--border);
        color: #64748b;
        border-radius: 10px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
        width: 100%;
    }
    
    .btn-back:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: #f8fafc;
        transform: translateY(-2px);
    }

    /* ======================================== */
    /* BADGE COUNT */
    /* ======================================== */
    .badge-count {
        background: #f1f5f9;
        color: #64748b;
        border-radius: 20px;
        padding: 0.3rem 0.8rem;
        font-size: 0.7rem;
        font-weight: 700;
    }

    /* ======================================== */
    /* EMPTY STATE */
    /* ======================================== */
    .empty-state {
        text-align: center;
        padding: 2.5rem;
    }
    
    .empty-state .empty-icon {
        width: 56px;
        height: 56px;
        background: #f1f5f9;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    
    .empty-state .empty-icon i {
        font-size: 22px;
        color: #94a3b8;
    }
    
    .empty-state .empty-text {
        font-size: 0.85rem;
        color: #94a3b8;
    }

    /* ======================================== */
    /* BREADCRUMB */
    /* ======================================== */
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }
    
    .breadcrumb-item a {
        color: #64748b;
        text-decoration: none;
        font-size: 0.8rem;
        transition: color 0.2s;
    }
    
    .breadcrumb-item a:hover {
        color: var(--primary);
    }
    
    .breadcrumb-item.active {
        color: var(--dark);
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        color: #cbd5e1;
    }

    /* ======================================== */
    /* RESPONSIVE */
    /* ======================================== */
    @media (max-width: 768px) {
        .info-row {
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .info-row .info-label {
            width: 100%;
        }
        
        .section-header .sh-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }
        
        .pct-label {
            font-size: 2rem;
        }
        
        .lampiran-name {
            max-width: 120px;
        }
    }
</style>
@endpush

@section('content')
<div class="nxl-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Detail Realisasi</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('realisasi.index') }}">Realisasi</a></li>
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
                    <a href="{{ route('realisasi.edit', $realisasi) }}" class="btn-edit">
                        <i class="feather-edit-2 me-1"></i> Edit
                    </a>
                    <button type="button" class="btn-hapus" onclick="confirmDelete('{{ route('realisasi.destroy', $realisasi) }}')">
                        <i class="feather-trash-2 me-1"></i> Hapus
                    </button>
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Main Content -->
    <div class="main-content">
        <div class="row g-4">

            <!-- Kolom Kiri (8 kolom) -->
            <div class="col-lg-8">

                <!-- Card Info Utama -->
                <div class="detail-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(52, 84, 209, 0.1);">
                                <i class="feather-file-text" style="color: #3454D1;"></i>
                            </div>
                            <h6 class="sh-title">{{ $realisasi->judul }}</h6>
                        </div>
                        @php
                            $badgeConf = [
                                'sesuai'   => ['class' => 'badge-sesuai',   'icon' => 'feather-check-circle', 'label' => 'Sesuai Target'],
                                'tidak'    => ['class' => 'badge-tidak',    'icon' => 'feather-x-circle',     'label' => 'Tidak Sesuai'],
                                'sebagian' => ['class' => 'badge-sebagian', 'icon' => 'feather-alert-circle', 'label' => 'Tercapai Sebagian'],
                            ];
                            $bc = $badgeConf[$realisasi->status_target] ?? ['class' => 'badge-secondary', 'icon' => 'feather-help-circle', 'label' => '-'];
                        @endphp
                        <span class="badge-status {{ $bc['class'] }}">
                            <i class="{{ $bc['icon'] }}"></i>
                            {{ $bc['label'] }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="feather-folder"></i>
                                Perencanaan
                            </div>
                            <div class="info-value fw-semibold">{{ $realisasi->perencanaan->judul ?? '-' }}</div>
                        </div>
                        
                        @if($realisasi->detailPerencanaan)
                        <div class="info-row">
                            <div class="info-label">
                                <i class="feather-corner-down-right"></i>
                                Detail Target
                            </div>
                            <div class="info-value">
                                <span style="background: #f1f5f9; border-radius: 6px; padding: 0.25rem 0.75rem; font-size: 0.85rem;">
                                    {{ $realisasi->detailPerencanaan->perencanaan ?? '-' }}
                                </span>
                            </div>
                        </div>
                        
                        @if($realisasi->detailPerencanaan->target)
                        <div class="info-row">
                            <div class="info-label">
                                <i class="feather-crosshair"></i>
                                Target
                            </div>
                            <div class="info-value">{{ $realisasi->detailPerencanaan->target }}</div>
                        </div>
                        @endif
                        @endif
                        
                        <div class="info-row">
                            <div class="info-label">
                                <i class="feather-calendar"></i>
                                Tanggal Realisasi
                            </div>
                            <div class="info-value">
                                <span class="fw-semibold">{{ $realisasi->tanggal_realisasi->format('d') }}</span>
                                {{ $realisasi->tanggal_realisasi->translatedFormat('F Y') }}
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">
                                <i class="feather-user"></i>
                                Dibuat oleh
                            </div>
                            <div class="info-value">{{ $realisasi->user->name ?? '-' }}</div>
                        </div>
                        
                        @if($realisasi->deskripsi)
                        <div class="info-row">
                            <div class="info-label">
                                <i class="feather-align-left"></i>
                                Deskripsi
                            </div>
                            <div class="info-value" style="line-height: 1.7; white-space: pre-wrap;">{{ $realisasi->deskripsi }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Card Pencapaian -->
                <div class="detail-card mb-4">
                    <div class="card-header">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(37, 176, 3, 0.1);">
                                <i class="feather-target" style="color: #25B003;"></i>
                            </div>
                            <h6 class="sh-title">Pencapaian Target</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Big Progress -->
                        <div class="progress-section mb-4">
                            <div class="d-flex justify-content-between align-items-end mb-3">
                                <div>
                                    <div class="small text-muted fw-semibold mb-1" style="letter-spacing: 0.5px; font-size: 0.7rem; text-transform: uppercase;">
                                        Persentase Pencapaian
                                    </div>
                                    @php
                                        $pctColor = [
                                            'sesuai'   => '#25B003',
                                            'tidak'    => '#dc3545',
                                            'sebagian' => '#ffc107',
                                        ][$realisasi->status_target] ?? '#3454D1';
                                    @endphp
                                    <div class="pct-label" style="color: {{ $pctColor }};">
                                        {{ number_format($realisasi->persentase, 0) }}<span style="font-size: 1rem; font-weight: 600;">%</span>
                                    </div>
                                </div>
                                <span class="badge-status {{ $bc['class'] }}" style="font-size: 0.7rem;">
                                    <i class="{{ $bc['icon'] }}" style="font-size: 0.7rem;"></i>
                                    {{ $bc['label'] }}
                                </span>
                            </div>
                            <div class="progress-big">
                                <div class="bar" style="width: {{ $realisasi->persentase }}%; background: linear-gradient(90deg, {{ $pctColor }}, {{ $pctColor }}cc);"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">0%</small>
                                <small class="text-muted">50%</small>
                                <small class="text-muted">100%</small>
                            </div>
                        </div>

                        @if($realisasi->keterangan_target)
                        <div class="note-box target-note mb-4">
                            <div class="note-header">
                                <i class="feather-info" style="color: #ffc107;"></i>
                                <span>Keterangan Target</span>
                            </div>
                            <div class="note-content">{{ $realisasi->keterangan_target }}</div>
                        </div>
                        @endif

                        @if($realisasi->catatan_tambahan)
                        <div class="note-box additional-note">
                            <div class="note-header">
                                <i class="feather-message-square" style="color: #17a2b8;"></i>
                                <span>Catatan Tambahan</span>
                            </div>
                            <div class="note-content">{{ $realisasi->catatan_tambahan }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan (4 kolom) -->
            <div class="col-lg-4">

                <!-- Card Lampiran -->
                <div class="detail-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(139, 92, 246, 0.1);">
                                <i class="feather-paperclip" style="color: #8b5cf6;"></i>
                            </div>
                            <h6 class="sh-title">Lampiran</h6>
                        </div>
                        <span class="badge-count">
                            {{ $realisasi->lampiran->count() }} file
                        </span>
                    </div>
                    <div class="card-body">
                        @forelse($realisasi->lampiran as $lmp)
                        @php
                            $ext = strtolower(pathinfo($lmp->nama_file, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            $iconClass = match(true) {
                                $ext === 'pdf'              => 'li-pdf',
                                in_array($ext, ['doc','docx']) => 'li-doc',
                                in_array($ext, ['xls','xlsx']) => 'li-xls',
                                default                     => 'li-other',
                            };
                            $iconName = match(true) {
                                $ext === 'pdf'              => 'feather-file-text',
                                in_array($ext, ['doc','docx']) => 'feather-file',
                                in_array($ext, ['xls','xlsx']) => 'feather-grid',
                                default                     => 'feather-paperclip',
                            };
                        @endphp
                        <div class="lampiran-item mb-2">
                            @if($isImage)
                                <img src="{{ Storage::url($lmp->path_file) }}"
                                     alt="{{ $lmp->nama_file }}"
                                     class="lampiran-thumb">
                            @else
                                <div class="lampiran-icon {{ $iconClass }}">
                                    <i class="{{ $iconName }}"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="lampiran-name">{{ $lmp->nama_file }}</div>
                                <div class="lampiran-size">
                                    {{ strtoupper($ext) }}
                                    @if(isset($lmp->ukuran_file))
                                        · {{ number_format($lmp->ukuran_file / 1024, 0) }} KB
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('realisasi.lampiran.download', $lmp) }}"
                               class="btn-dl" title="Download">
                                <i class="feather-download"></i>
                            </a>
                        </div>
                        @empty
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="feather-paperclip"></i>
                            </div>
                            <div class="empty-text">Tidak ada lampiran</div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Meta Info -->
                <div class="detail-card mb-4">
                    <div class="card-header">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(34, 197, 94, 0.1);">
                                <i class="feather-info" style="color: #22c55e;"></i>
                            </div>
                            <h6 class="sh-title">Informasi Waktu</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="meta-card">
                            <div class="meta-item">
                                <i class="feather-plus-circle"></i>
                                <span>Dibuat: <strong>{{ $realisasi->created_at->translatedFormat('d M Y, H:i') }}</strong></span>
                            </div>
                            @if($realisasi->updated_at->ne($realisasi->created_at))
                            <div class="meta-item">
                                <i class="feather-edit-3"></i>
                                <span>Diperbarui: <strong>{{ $realisasi->updated_at->translatedFormat('d M Y, H:i') }}</strong></span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tombol Kembali -->
                <a href="{{ route('realisasi.index') }}" class="btn-back">
                    <i class="feather-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
</div>

<!-- Form Delete (Hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmDelete(url) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Realisasi?',
            html: '<p class="mb-2">Semua lampiran juga akan ikut terhapus.</p><small class="text-muted">Tindakan ini tidak bisa dibatalkan.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="feather-trash-2 me-2"></i>Ya, Hapus!',
            cancelButtonText: '<i class="feather-x me-2"></i>Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = url;
                form.submit();
            }
        });
    } else {
        if (confirm('Hapus realisasi ini? Semua lampiran akan ikut terhapus.')) {
            const form = document.getElementById('deleteForm');
            form.action = url;
            form.submit();
        }
    }
}

// SweetAlert Notifications
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ session('error') }}',
        confirmButtonColor: '#3454D1'
    });
@endif
</script>
@endpush