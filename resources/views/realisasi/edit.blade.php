@extends('layouts.app')

@section('title', 'Edit Realisasi')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10 mb-0">Edit Realisasi</h5>
            </div>
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('realisasi.index') }}">Realisasi</a></li>
                <li class="breadcrumb-item active">Edit</li>
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
                    <a href="{{ route('realisasi.index') }}" class="btn btn-outline-secondary">
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
            <!-- kolom kiri - form utama (8 col) -->
            <div class="col-lg-8">
                <div class="card table-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-edit-2 me-2 text-primary"></i>Form Edit Realisasi
                        </h5>
                        <span class="badge badge-secondary-light">ID: {{ $realisasi->id }}</span>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger-custom mb-4">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="feather-alert-circle fs-4 text-danger"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold mb-2">Terjadi Kesalahan:</div>
                                        <ul class="mb-0 ps-3">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('realisasi.update', $realisasi->id) }}" method="POST" enctype="multipart/form-data" id="realisasiForm">
                            @csrf
                            @method('PUT')
                            
                            <!-- Hubungkan ke Perencanaan -->
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">
                                    <i class="feather-link text-primary me-2"></i>Hubungkan ke Perencanaan
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="perencanaan_id" class="form-label">
                                            Perencanaan <span class="text-danger">*</span>
                                        </label>
                                        <select name="perencanaan_id" id="perencanaanSelect"
                                                class="form-select @error('perencanaan_id') is-invalid @enderror"
                                                required>
                                            <option value="">-- Pilih Perencanaan --</option>
                                            @foreach($perencanaanList as $p)
                                                <option value="{{ $p->perencanaan_id }}"
                                                        {{ old('perencanaan_id', $realisasi->perencanaan_id) == $p->perencanaan_id ? 'selected' : '' }}>
                                                    {{ $p->judul }} ({{ \Carbon\Carbon::create()->month($p->bulan)->translatedFormat('F') }} {{ $p->tahun }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('perencanaan_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="detail_perencanaan_id" class="form-label">
                                            Detail Perencanaan <span class="text-muted">(opsional)</span>
                                        </label>
                                        <select name="detail_perencanaan_id" id="detailSelect"
                                                class="form-select @error('detail_perencanaan_id') is-invalid @enderror">
                                            <option value="">-- Pilih Detail (opsional) --</option>
                                        </select>
                                        @error('detail_perencanaan_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text" id="detailHint">
                                            <i class="feather-info me-1"></i>Pilih perencanaan terlebih dahulu untuk melihat detail
                                        </div>
                                    </div>
                                </div>

                                <!-- Info Perencanaan Terpilih -->
                                <div id="perencanaanInfo" class="alert alert-info-custom mt-3 @if(!$realisasi->perencanaan_id) d-none @endif">
                                    <div class="d-flex align-items-start gap-3">
                                        <i class="feather-calendar fs-5 text-primary mt-1"></i>
                                        <div id="perencanaanInfoContent" class="flex-grow-1">
                                            @if($realisasi->perencanaan)
                                                <strong>{{ $realisasi->perencanaan->judul }}</strong> &mdash; 
                                                {{ \Carbon\Carbon::create()->month($realisasi->perencanaan->bulan)->translatedFormat('F') }} {{ $realisasi->perencanaan->tahun }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Preview -->
                                <div id="detailPreviewWrap" class="mt-3 @if(!$realisasi->detail_perencanaan_id) d-none @endif">
                                    <div class="card bg-light border-0 rounded-3">
                                        <div class="card-body p-3">
                                            <h6 class="fw-semibold mb-3 small">
                                                <i class="feather-list text-primary me-2"></i>Detail Perencanaan yang Dipilih
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <div class="d-flex align-items-start gap-2">
                                                        <i class="feather-target text-primary mt-1" style="width: 18px;"></i>
                                                        <div>
                                                            <span class="text-muted d-block small">Kegiatan</span>
                                                            <span class="fw-semibold small" id="prev_perencanaan">
                                                                {{ $realisasi->detailPerencanaan->perencanaan ?? '-' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div class="d-flex align-items-start gap-2">
                                                        <i class="feather-check-circle text-success mt-1" style="width: 18px;"></i>
                                                        <div>
                                                            <span class="text-muted d-block small">Target</span>
                                                            <span class="fw-semibold small" id="prev_target">
                                                                {{ $realisasi->detailPerencanaan->target ?? '-' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div class="d-flex align-items-start gap-2">
                                                        <i class="feather-file-text text-info mt-1" style="width: 18px;"></i>
                                                        <div>
                                                            <span class="text-muted d-block small">Deskripsi</span>
                                                            <span class="small" id="prev_deskripsi">
                                                                {{ $realisasi->detailPerencanaan->deskripsi ?? '-' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div class="d-flex align-items-start gap-2">
                                                        <i class="feather-calendar text-warning mt-1" style="width: 18px;"></i>
                                                        <div>
                                                            <span class="text-muted d-block small">Pelaksanaan</span>
                                                            <span class="small" id="prev_pelaksanaan">
                                                                {{ $realisasi->detailPerencanaan->pelaksanaan ?? '-' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Realisasi -->
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">
                                    <i class="feather-info text-primary me-2"></i>Informasi Realisasi
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="judul" class="form-label">
                                            Judul Realisasi <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="judul" id="judul"
                                               class="form-control @error('judul') is-invalid @enderror"
                                               value="{{ old('judul', $realisasi->judul) }}"
                                               placeholder="Contoh: Realisasi Pembelian ATK Bulan Januari"
                                               required>
                                        @error('judul') 
                                            <div class="invalid-feedback">{{ $message }}</div> 
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="tanggal_realisasi" class="form-label">
                                            Tanggal Realisasi <span class="text-danger">*</span>
                                        </label>
                                        <div class="custom-date-input" id="tanggalWrapper">
                                            <input type="text" 
                                                   id="tanggalDisplay"
                                                   class="form-control date-display" 
                                                   placeholder="Pilih tanggal"
                                                   value="{{ old('tanggal_realisasi', $realisasi->tanggal_realisasi->format('d/m/Y')) }}"
                                                   readonly>
                                            <input type="hidden" name="tanggal_realisasi" id="tanggalHidden" 
                                                   value="{{ old('tanggal_realisasi', $realisasi->tanggal_realisasi->format('Y-m-d')) }}">
                                            <i class="feather-calendar calendar-icon"></i>
                                            
                                            <!-- Custom Date Picker -->
                                            <div class="custom-date-picker" id="tanggalPicker">
                                                <div class="date-picker-header">
                                                    <button type="button" class="month-nav" id="prevMonth">
                                                        <i class="feather-chevron-left"></i>
                                                    </button>
                                                    <span class="month-year" id="monthYear">February 2026</span>
                                                    <button type="button" class="month-nav" id="nextMonth">
                                                        <i class="feather-chevron-right"></i>
                                                    </button>
                                                </div>
                                                <div class="date-picker-weekdays">
                                                    <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                                                </div>
                                                <div class="date-picker-days" id="calendarDays"></div>
                                                <div class="date-picker-footer">
                                                    <button type="button" class="btn-clear" id="clearDate">Clear</button>
                                                    <button type="button" class="btn-today" id="todayDate">Today</button>
                                                </div>
                                            </div>
                                        </div>
                                        @error('tanggal_realisasi')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="deskripsi" class="form-label">
                                            Deskripsi <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="deskripsi" id="deskripsi"
                                                  class="form-control @error('deskripsi') is-invalid @enderror"
                                                  rows="4"
                                                  placeholder="Jelaskan apa yang sudah direalisasikan..."
                                                  required>{{ old('deskripsi', $realisasi->deskripsi) }}</textarea>
                                        @error('deskripsi') 
                                            <div class="invalid-feedback">{{ $message }}</div> 
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Pencapaian Target -->
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">
                                    <i class="feather-bullseye text-primary me-2"></i>Pencapaian Target
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="status_target" class="form-label">
                                            Status Target <span class="text-danger">*</span>
                                        </label>
                                        <select name="status_target" id="statusTarget"
                                                class="form-select @error('status_target') is-invalid @enderror"
                                                required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="sesuai" {{ old('status_target', $realisasi->status_target) == 'sesuai' ? 'selected' : '' }}>✓ Sesuai Target</option>
                                            <option value="tidak" {{ old('status_target', $realisasi->status_target) == 'tidak' ? 'selected' : '' }}>✗ Tidak Sesuai</option>
                                            <option value="sebagian" {{ old('status_target', $realisasi->status_target) == 'sebagian' ? 'selected' : '' }}>◑ Tercapai Sebagian</option>
                                        </select>
                                        @error('status_target') 
                                            <div class="invalid-feedback">{{ $message }}</div> 
                                        @enderror
                                    </div>

                                    <div class="col-md-8 mb-3">
                                        <label class="form-label">
                                            Persentase
                                            <span class="badge badge-primary-light ms-2" id="persentaseLabel">
                                                {{ old('persentase', $realisasi->persentase) }}%
                                            </span>
                                        </label>
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="range" id="persentaseRange"
                                                   class="form-range flex-grow-1"
                                                   min="0" max="100" step="1"
                                                   value="{{ old('persentase', $realisasi->persentase) }}">
                                            <input type="number" id="persentaseInput"
                                                   class="form-control" style="width:80px"
                                                   min="0" max="100"
                                                   value="{{ old('persentase', $realisasi->persentase) }}">
                                        </div>
                                        <input type="hidden" name="persentase" id="persentaseHidden"
                                               value="{{ old('persentase', $realisasi->persentase) }}">
                                        <div class="form-text">
                                            <i class="feather-info me-1"></i>Otomatis terisi saat pilih status, atau geser manual
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="keterangan_target" class="form-label">Keterangan Target</label>
                                        <textarea name="keterangan_target" id="keterangan_target" 
                                                  class="form-control" rows="3"
                                                  placeholder="Keterangan pencapaian target...">{{ old('keterangan_target', $realisasi->keterangan_target) }}</textarea>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="catatan_tambahan" class="form-label">Catatan Tambahan</label>
                                        <textarea name="catatan_tambahan" id="catatan_tambahan" 
                                                  class="form-control" rows="3"
                                                  placeholder="Catatan lain yang perlu disampaikan...">{{ old('catatan_tambahan', $realisasi->catatan_tambahan) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                <div class="pagination-info text-center text-md-start">
                                    <i class="feather-info me-1"></i>
                                    Pastikan data yang diisi sudah benar
                                </div>
                                <div class="d-flex flex-column flex-sm-row gap-2 w-100" style="max-width: 100%;">
                                    <a href="{{ route('realisasi.create') }}" class="btn btn-outline-primary flex-grow-1">
                                        <i class="feather-plus me-2"></i>Tambah Baru
                                    </a>
                                    <a href="{{ route('realisasi.index') }}" class="btn btn-outline-secondary flex-grow-1">
                                        <i class="feather-x me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="feather-save me-2"></i>Update Realisasi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- kolom kanan - sidebar (4 col) -->
            <div class="col-lg-4">
                <!-- card detail ringkas -->
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-info me-2 text-primary"></i>Detail Realisasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">ID Realisasi</div>
                            <div class="info-value-detail">
                                <span class="badge badge-primary-light">{{ $realisasi->id }}</span>
                            </div>
                        </div>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Judul Realisasi</div>
                            <div class="info-value-detail fw-semibold">{{ $realisasi->judul }}</div>
                        </div>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Tanggal Realisasi</div>
                            <div class="info-value-detail">{{ $realisasi->tanggal_realisasi->format('d F Y') }}</div>
                        </div>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Status Target</div>
                            <div class="info-value-detail">
                                @php
                                    $statusClass = '';
                                    $statusIcon = '';
                                    if($realisasi->status_target == 'sesuai') {
                                        $statusClass = 'badge-success-light';
                                        $statusIcon = 'feather-check-circle';
                                    } elseif($realisasi->status_target == 'sebagian') {
                                        $statusClass = 'badge-warning-light';
                                        $statusIcon = 'feather-alert-circle';
                                    } else {
                                        $statusClass = 'badge-danger-light';
                                        $statusIcon = 'feather-x-circle';
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    <i class="{{ $statusIcon }} me-1"></i>
                                    {{ ucfirst($realisasi->status_target) }}
                                </span>
                            </div>
                        </div>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Persentase</div>
                            <div class="info-value-detail">
                                <span class="badge badge-primary-light">{{ $realisasi->persentase }}%</span>
                            </div>
                        </div>
                        <hr>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Dibuat Pada</div>
                            <div class="info-value-detail small">{{ $realisasi->created_at->format('d F Y, H:i') }}</div>
                        </div>
                        <div class="info-item-detail">
                            <div class="info-label-detail">Terakhir Diubah</div>
                            <div class="info-value-detail small">{{ $realisasi->updated_at->format('d F Y, H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- card lampiran existing -->
                @if($realisasi->lampiran->count())
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-paperclip me-2 text-primary"></i>Lampiran Saat Ini
                        </h5>
                        <span class="badge badge-info-light">{{ $realisasi->lampiran->count() }} file</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group-custom">
                            @foreach($realisasi->lampiran as $lmp)
                            <div class="list-item">
                                <div class="d-flex align-items-center gap-3">
                                    @if($lmp->isImage())
                                        <img src="{{ Storage::url($lmp->path_file) }}"
                                             class="file-thumb rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="avatar-initial bg-primary-soft text-primary" style="width: 40px; height: 40px;">
                                            <i class="feather-file-text"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold small">{{ $lmp->nama_file }}</div>
                                        <small class="text-muted">{{ $lmp->ukuran_format }}</small>
                                    </div>
                                    <div class="form-check d-flex align-items-center justify-content-center m-0 p-0 gap-2">
                                        <input class="form-check-input border-danger m-0 p-0" type="checkbox"
                                               name="hapus_lampiran[]" value="{{ $lmp->id }}"
                                               id="hapus_{{ $lmp->id }}" style="cursor: pointer;">
                                        <label class="form-check-label text-danger m-0 p-0 d-flex align-items-center" for="hapus_{{ $lmp->id }}" style="cursor: pointer;">
                                            <i class="feather-trash-2 fs-5"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">
                                <i class="feather-info me-1"></i>Centang <i class="feather-trash-2 text-danger"></i> untuk menghapus file
                            </small>
                        </div>
                    </div>
                </div>
                @endif

                <!-- card upload lampiran baru -->
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-upload-cloud me-2 text-primary"></i>Tambah Lampiran Baru
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="upload-area" id="dropZone">
                            <div class="text-center py-4">
                                <i class="feather-upload-cloud display-4 text-muted mb-3 d-block" style="font-size: 2.5rem;"></i>
                                <h6 class="fw-semibold mb-1">Klik atau drag file</h6>
                                <p class="text-muted small mb-0">PDF, JPG, PNG, DOC, XLS</p>
                                <p class="text-muted small mb-0">Maks 10MB per file</p>
                            </div>
                            <input type="file" name="lampiran[]" id="lampiranInput"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                                   multiple class="d-none">
                        </div>
                        <div id="previewContainer" class="mt-3"></div>
                        @error('lampiran.*')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- danger zone -->
                <div class="card border-danger">
                    <div class="card-header bg-danger-soft border-danger">
                        <h5 class="mb-0 text-danger">
                            <i class="feather-alert-triangle me-2"></i>Zona Berbahaya
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            Menghapus realisasi akan mempengaruhi semua data yang terkait.
                        </p>
                        @if($realisasi->lampiran->count() > 0)
                            <div class="alert alert-warning-custom small mb-3">
                                <i class="feather-alert-circle me-1"></i>
                                Realisasi ini memiliki <strong>{{ $realisasi->lampiran->count() }} lampiran</strong> terkait
                            </div>
                        @endif
                        <button type="button" 
                                class="btn btn-outline-danger w-100" 
                                onclick="deleteRealisasi({{ $realisasi->id }}, '{{ addslashes($realisasi->judul) }}')">
                            <i class="feather-trash-2 me-2"></i>Hapus Realisasi
                        </button>
                        <form id="delete-form-{{ $realisasi->id }}" 
                              action="{{ route('realisasi.destroy', $realisasi->id) }}" 
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

.card-footer {
    background: transparent;
    border-top: 1px solid var(--border-color);
    padding: 0.75rem 1.5rem;
}

.border-danger {
    border: 1px solid rgba(220, 53, 69, 0.2) !important;
}

/* ============================================
   FORM STYLES
   ============================================ */
.form-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #6c757d;
    margin-bottom: 0.5rem;
    display: block;
}

.form-control, .form-select {
    border-radius: 0.625rem;
    border: 1px solid #e2e8f0;
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background-color: #ffffff;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(52, 84, 209, 0.1);
    outline: none;
}

.form-control:hover, .form-select:hover {
    border-color: #cbd5e0;
}

textarea.form-control {
    resize: vertical;
}

/* Form Range */
.form-range {
    height: 1.5rem;
    padding: 0;
}

.form-range::-webkit-slider-thumb {
    background-color: var(--primary-color);
}

.form-range::-webkit-slider-thumb:active {
    background-color: var(--primary-dark);
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
.alert-danger-custom {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.08) 0%, rgba(220, 53, 69, 0.05) 100%);
    border: 1px solid rgba(220, 53, 69, 0.15);
    border-radius: 1rem;
    padding: 1rem 1.25rem;
}

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
   UPLOAD AREA
   ============================================ */
.upload-area {
    border: 2px dashed #e2e8f0;
    border-radius: 0.75rem;
    background-color: #f8fafc;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-area:hover {
    border-color: var(--primary-color);
    background-color: rgba(52, 84, 209, 0.02);
}

.upload-area.dragover {
    border-color: var(--primary-color);
    background-color: rgba(52, 84, 209, 0.05);
}

.file-thumb {
    width: 40px;
    height: 40px;
    border-radius: 0.5rem;
    object-fit: cover;
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

.badge-warning-light {
    background-color: rgba(255, 193, 7, 0.08);
    color: #d39e00;
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
.bg-danger-soft { background-color: rgba(220, 53, 69, 0.08); }
.bg-info-soft { background-color: rgba(23, 162, 184, 0.1); }

/* Avatar Initial */
.avatar-initial {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 1rem;
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
   CARD BG LIGHT
   ============================================ */
.card.bg-light {
    background-color: #f8fafc !important;
}

.rounded-3 {
    border-radius: 0.75rem !important;
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

    .form-control, .form-select {
        font-size: 0.8125rem;
    }

    .info-item-detail {
        flex-direction: column;
        align-items: flex-start;
    }

    .custom-date-picker {
        width: 280px;
        left: auto;
        right: 0;
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

    // ========================================
    // CUSTOM DATE PICKER
    // ========================================
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    
    const tanggalDisplay = document.getElementById('tanggalDisplay');
    const tanggalHidden = document.getElementById('tanggalHidden');
    const tanggalPicker = document.getElementById('tanggalPicker');
    const monthYear = document.getElementById('monthYear');
    const calendarDays = document.getElementById('calendarDays');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const clearDateBtn = document.getElementById('clearDate');
    const todayBtn = document.getElementById('todayDate');

    let currentDate = tanggalHidden.value ? new Date(tanggalHidden.value) : new Date();
    let selectedDate = tanggalHidden.value ? new Date(tanggalHidden.value) : new Date();

    function formatDate(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    function formatDateYMD(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${year}-${month}-${day}`;
    }

    function isSameDay(date1, date2) {
        return date1.getDate() === date2.getDate() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getFullYear() === date2.getFullYear();
    }

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        monthYear.textContent = `${monthNames[month]} ${year}`;
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevMonthDays = new Date(year, month, 0).getDate();
        
        let html = '';
        
        for (let i = firstDay; i > 0; i--) {
            const day = prevMonthDays - i + 1;
            html += `<button type="button" class="prev-month">${day}</button>`;
        }
        
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const isToday = isSameDay(date, new Date());
            const isSelected = selectedDate && isSameDay(date, selectedDate);
            
            let classes = '';
            if (isToday) classes += ' today';
            if (isSelected) classes += ' selected';
            
            html += `<button type="button" class="${classes}" data-day="${day}">${day}</button>`;
        }
        
        const totalDays = firstDay + daysInMonth;
        const remainingCells = 42 - totalDays;
        for (let day = 1; day <= remainingCells; day++) {
            html += `<button type="button" class="next-month">${day}</button>`;
        }
        
        calendarDays.innerHTML = html;
        
        calendarDays.querySelectorAll('button:not(.prev-month):not(.next-month)').forEach(btn => {
            btn.addEventListener('click', function() {
                const day = parseInt(this.dataset.day);
                const newDate = new Date(year, month, day);
                selectedDate = newDate;
                tanggalDisplay.value = formatDate(newDate);
                tanggalHidden.value = formatDateYMD(newDate);
                renderCalendar();
                tanggalPicker.classList.remove('show');
            });
        });
    }

    if (tanggalDisplay) {
        tanggalDisplay.addEventListener('click', function(e) {
            e.stopPropagation();
            tanggalPicker.classList.toggle('show');
            renderCalendar();
        });
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-date-input')) {
            if (tanggalPicker) tanggalPicker.classList.remove('show');
        }
    });

    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });
    }

    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });
    }

    if (clearDateBtn) {
        clearDateBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            selectedDate = null;
            tanggalDisplay.value = '';
            tanggalHidden.value = '';
            renderCalendar();
            tanggalPicker.classList.remove('show');
        });
    }

    if (todayBtn) {
        todayBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const today = new Date();
            currentDate = new Date(today);
            selectedDate = new Date(today);
            tanggalDisplay.value = formatDate(today);
            tanggalHidden.value = formatDateYMD(today);
            renderCalendar();
            tanggalPicker.classList.remove('show');
        });
    }

    // ========================================
    // PERENCANAAN & DETAIL
    // ========================================
    const perencanaanSelect = document.getElementById('perencanaanSelect');
    const detailSelect = document.getElementById('detailSelect');
    const detailHint = document.getElementById('detailHint');
    const perencanaanInfo = document.getElementById('perencanaanInfo');
    const infoContent = document.getElementById('perencanaanInfoContent');
    const detailPreviewWrap = document.getElementById('detailPreviewWrap');
    const prevPerencanaan = document.getElementById('prev_perencanaan');
    const prevTarget = document.getElementById('prev_target');
    const prevDeskripsi = document.getElementById('prev_deskripsi');
    const prevPelaksanaan = document.getElementById('prev_pelaksanaan');

    const currentDetailId = {{ $realisasi->detail_perencanaan_id ?? 'null' }};
    const currentPerencanaanId = {{ $realisasi->perencanaan_id ?? 'null' }};

    const bulanNames = ['','Januari','Februari','Maret','April','Mei','Juni',
                        'Juli','Agustus','September','Oktober','November','Desember'];

    function loadDetails(perencanaanId, selectedDetailId = null) {
        if (!perencanaanId) return;

        detailSelect.disabled = true;
        detailHint.innerHTML = '<i class="feather-loader me-1"></i>Memuat detail...';

        fetch(`{{ route('ajax.detail-perencanaan') }}?perencanaan_id=${perencanaanId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const details = data.details || [];
            const perencanaan = data.perencanaan;

            if (perencanaan && infoContent) {
                infoContent.innerHTML = `<strong>${perencanaan.judul}</strong> &mdash; ${bulanNames[perencanaan.bulan] || perencanaan.bulan} ${perencanaan.tahun}`;
                perencanaanInfo.classList.remove('d-none');
            }

            detailSelect.innerHTML = '<option value="">-- Pilih Detail (opsional) --</option>';

            if (details.length > 0) {
                details.forEach(d => {
                    const option = document.createElement('option');
                    option.value = d.id;
                    option.textContent = d.nomor + '. ' + d.perencanaan;
                    option.dataset.perencanaan = d.perencanaan || '';
                    option.dataset.target = d.target || '';
                    option.dataset.deskripsi = d.deskripsi || '';
                    option.dataset.pelaksanaan = d.pelaksanaan || '';
                    if (selectedDetailId == d.id) option.selected = true;
                    detailSelect.appendChild(option);
                });
                detailHint.innerHTML = '<i class="feather-info me-1"></i>' + details.length + ' detail tersedia';
            } else {
                detailSelect.innerHTML = '<option value="">Tidak ada detail</option>';
                detailHint.innerHTML = '<i class="feather-info me-1"></i>Tidak ada detail untuk perencanaan ini';
            }

            detailSelect.disabled = false;

            if (selectedDetailId) {
                const selectedOption = detailSelect.querySelector(`option[value="${selectedDetailId}"]`);
                if (selectedOption) {
                    updateDetailPreview(selectedOption);
                }
            }
        })
        .catch(err => {
            console.error('AJAX error:', err);
            detailSelect.innerHTML = '<option value="">Gagal memuat data</option>';
            detailHint.innerHTML = '<i class="feather-alert-circle me-1"></i>Terjadi kesalahan, coba refresh halaman';
            detailSelect.disabled = false;
        });
    }

    function updateDetailPreview(selectedOption) {
        if (!detailPreviewWrap) return;
        
        const perencanaan = selectedOption.dataset.perencanaan || '-';
        const target = selectedOption.dataset.target || '-';
        const deskripsi = selectedOption.dataset.deskripsi || '-';
        const pelaksanaan = selectedOption.dataset.pelaksanaan || '-';

        if (prevPerencanaan) prevPerencanaan.textContent = perencanaan;
        if (prevTarget) prevTarget.textContent = target;
        if (prevDeskripsi) prevDeskripsi.textContent = deskripsi;
        if (prevPelaksanaan) prevPelaksanaan.textContent = pelaksanaan;

        detailPreviewWrap.classList.remove('d-none');
    }

    if (perencanaanSelect && currentPerencanaanId) {
        loadDetails(currentPerencanaanId, currentDetailId);
    }

    if (perencanaanSelect) {
        perencanaanSelect.addEventListener('change', function() {
            const id = this.value;

            if (detailSelect) {
                detailSelect.innerHTML = '<option value="">-- Pilih Detail (opsional) --</option>';
                detailSelect.disabled = true;
            }
            if (detailHint) detailHint.innerHTML = '<i class="feather-info me-1"></i>Pilih perencanaan terlebih dahulu';
            if (perencanaanInfo) perencanaanInfo.classList.add('d-none');
            if (detailPreviewWrap) detailPreviewWrap.classList.add('d-none');

            if (!id) return;

            loadDetails(id);
        });
    }

    if (detailSelect) {
        detailSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (!this.value || !selected) {
                if (detailPreviewWrap) detailPreviewWrap.classList.add('d-none');
                return;
            }
            updateDetailPreview(selected);
        });
    }

    // ========================================
    // STATUS & PERSENTASE
    // ========================================
    const statusTarget = document.getElementById('statusTarget');
    const persentaseRange = document.getElementById('persentaseRange');
    const persentaseInput = document.getElementById('persentaseInput');
    const persentaseLabel = document.getElementById('persentaseLabel');
    const persentaseHidden = document.getElementById('persentaseHidden');

    let isUpdatingFromStatus = false;
    let isUpdatingFromPersentase = false;

    function updatePersentase(val, source = 'manual') {
        val = parseInt(val);
        if (isNaN(val)) val = 0;
        val = Math.min(100, Math.max(0, val));
        
        if (persentaseRange) persentaseRange.value = val;
        if (persentaseInput) persentaseInput.value = val;
        if (persentaseHidden) persentaseHidden.value = val;
        if (persentaseLabel) persentaseLabel.textContent = val + '%';
        
        if (source !== 'status' && statusTarget) {
            updateStatusFromPersentase(val);
        }
    }

    function updateStatusFromPersentase(val) {
        if (isUpdatingFromStatus) return;
        isUpdatingFromPersentase = true;
        
        let newStatus = '';
        if (val === 100) newStatus = 'sesuai';
        else if (val === 0) newStatus = 'tidak';
        else if (val > 0 && val < 100) newStatus = 'sebagian';
        
        if (newStatus && statusTarget.value !== newStatus) {
            statusTarget.value = newStatus;
        }
        
        setTimeout(() => { isUpdatingFromPersentase = false; }, 50);
    }

    function updatePersentaseFromStatus(status) {
        if (isUpdatingFromPersentase) return;
        isUpdatingFromStatus = true;
        
        const map = { sesuai: 100, tidak: 0, sebagian: 50 };
        
        if (status in map) {
            const val = map[status];
            if (persentaseRange) persentaseRange.value = val;
            if (persentaseInput) persentaseInput.value = val;
            if (persentaseHidden) persentaseHidden.value = val;
            if (persentaseLabel) persentaseLabel.textContent = val + '%';
        }
        
        setTimeout(() => { isUpdatingFromStatus = false; }, 50);
    }

    if (statusTarget) {
        statusTarget.addEventListener('change', function() {
            updatePersentaseFromStatus(this.value);
        });
    }

    if (persentaseRange) {
        persentaseRange.addEventListener('input', function() {
            updatePersentase(this.value, 'slider');
        });
    }

    if (persentaseInput) {
        persentaseInput.addEventListener('input', function() {
            let val = parseInt(this.value) || 0;
            if (val > 100) val = 100;
            if (val < 0) val = 0;
            this.value = val;
            updatePersentase(val, 'input');
        });
    }

    // ========================================
    // FILE UPLOAD
    // ========================================
    const lampiranInput = document.getElementById('lampiranInput');
    const previewContainer = document.getElementById('previewContainer');
    const dropZone = document.getElementById('dropZone');
    let selectedFiles = [];

    const fileIcons = {
        pdf: 'feather-file-text text-danger',
        doc: 'feather-file-text text-primary', 
        docx: 'feather-file-text text-primary',
        xls: 'feather-file-text text-success', 
        xlsx: 'feather-file-text text-success',
        jpg: 'feather-image text-info',
        jpeg: 'feather-image text-info',
        png: 'feather-image text-info'
    };

    if (lampiranInput) {
        lampiranInput.addEventListener('change', function() {
            Array.from(this.files).forEach(addFile);
            syncFileInput();
        });
    }

    function addFile(file) {
        const index = selectedFiles.length;
        selectedFiles.push(file);
        const ext = file.name.split('.').pop().toLowerCase();
        const isImg = ['jpg','jpeg','png'].includes(ext);
        const size = file.size < 1048576
            ? (file.size/1024).toFixed(0) + ' KB'
            : (file.size/1048576).toFixed(1) + ' MB';

        const div = document.createElement('div');
        div.className = 'file-preview d-flex align-items-center gap-3';
        div.id = `fp-${index}`;
        
        const iconClass = fileIcons[ext] || 'feather-file text-secondary';
        
        if (isImg) {
            div.innerHTML = `
                <img src="" class="file-thumb rounded">
                <div class="flex-grow-1">
                    <div class="fw-semibold small">${file.name}</div>
                    <small class="text-muted">${size}</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                    <i class="feather-trash-2"></i>
                </button>
            `;
            const reader = new FileReader();
            reader.onload = e => div.querySelector('.file-thumb').src = e.target.result;
            reader.readAsDataURL(file);
        } else {
            div.innerHTML = `
                <i class="${iconClass} fs-3"></i>
                <div class="flex-grow-1">
                    <div class="fw-semibold small">${file.name}</div>
                    <small class="text-muted">${size}</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                    <i class="feather-trash-2"></i>
                </button>
            `;
        }
        
        if (previewContainer) previewContainer.appendChild(div);
    }

    window.removeFile = function(index) {
        selectedFiles[index] = null;
        const element = document.getElementById(`fp-${index}`);
        if (element) element.remove();
        syncFileInput();
    };

    function syncFileInput() {
        const dt = new DataTransfer();
        selectedFiles.filter(Boolean).forEach(f => dt.items.add(f));
        if (lampiranInput) lampiranInput.files = dt.files;
    }

    if (dropZone) {
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            Array.from(e.dataTransfer.files).forEach(addFile);
            syncFileInput();
        });

        dropZone.addEventListener('click', () => {
            if (lampiranInput) lampiranInput.click();
        });
    }

    // ========================================
    // FORM SUBMIT
    // ========================================
    const form = document.getElementById('realisasiForm');
    const submitBtn = form ? form.querySelector('button[type="submit"]') : null;

    if (form) {
        form.addEventListener('submit', function(e) {
            if (!perencanaanSelect.value) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Peringatan', text: 'Silakan pilih perencanaan', confirmButtonColor: '#3454D1' });
                perencanaanSelect.focus();
                return;
            }

            const judulInput = document.getElementById('judul');
            if (!judulInput.value.trim()) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Peringatan', text: 'Judul realisasi harus diisi', confirmButtonColor: '#3454D1' });
                judulInput.focus();
                return;
            }

            if (!tanggalHidden.value) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Peringatan', text: 'Tanggal realisasi harus diisi', confirmButtonColor: '#3454D1' });
                tanggalDisplay.focus();
                return;
            }

            const deskripsiInput = document.getElementById('deskripsi');
            if (!deskripsiInput.value.trim()) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Peringatan', text: 'Deskripsi harus diisi', confirmButtonColor: '#3454D1' });
                deskripsiInput.focus();
                return;
            }

            if (!statusTarget.value) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Peringatan', text: 'Status target harus dipilih', confirmButtonColor: '#3454D1' });
                statusTarget.focus();
                return;
            }

            if (submitBtn) {
                submitBtn.innerHTML = '<i class="feather-loader me-2"></i>Menyimpan...';
                submitBtn.disabled = true;
            }
        });
    }

    renderCalendar();
});

function refreshPage() {
    location.reload();
}

function deleteRealisasi(id, judul) {
    Swal.fire({
        title: 'Hapus Realisasi?',
        html: `
            <div class="text-start">
                <p>Apakah Anda yakin ingin menghapus realisasi ini?</p>
                <div class="alert alert-light border p-3 rounded-3">
                    <div class="mb-0">
                        <strong class="text-primary">Judul:</strong>
                        <span>${judul}</span>
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
        confirmButtonColor: '#3454D1'
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