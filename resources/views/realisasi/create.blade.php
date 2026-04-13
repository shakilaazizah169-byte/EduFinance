@extends('layouts.app')

@section('title', 'Tambah Realisasi')

@section('content')
<div class="nxl-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Tambah Realisasi</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('realisasi.index') }}">Realisasi</a></li>
                <li class="breadcrumb-item">Tambah</li>
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
                    <a href="{{ route('realisasi.index') }}" class="btn btn-icon btn-light-brand">
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
    <!-- End Page Header -->

    <!-- Main Content -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Form Tambah Realisasi</h5>
                    </div>

                    <div class="card-body">
                        @if($perencanaanList->isEmpty())
                        <div class="alert alert-warning d-flex align-items-center p-3 mb-4" role="alert">
                            <div class="avatar-text bg-warning text-white me-3">
                                <i class="feather-alert-circle"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-1">Belum Ada Perencanaan</h6>
                                <p class="mb-0">Silakan buat perencanaan terlebih dahulu sebelum menambah realisasi.</p>
                                <a href="{{ route('perencanaan.create') }}" class="btn btn-sm btn-warning mt-2">
                                    <i class="feather-plus me-1"></i>Buat Perencanaan
                                </a>
                            </div>
                        </div>
                        @endif

                        <form action="{{ route('realisasi.store') }}" method="POST" enctype="multipart/form-data" id="formRealisasi">
                            @csrf
                            
                            <div class="row">
                                <!-- Kolom Kiri (8 kolom) -->
                                <div class="col-lg-8">
                                    
                                    <!-- Hubungkan ke Perencanaan -->
                                    <div class="mb-4">
                                        <h6 class="fw-semibold mb-3">
                                            <i class="feather-link text-primary me-2"></i>Hubungkan ke Perencanaan
                                        </h6>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">
                                                    Perencanaan <span class="text-danger">*</span>
                                                </label>
                                                <select name="perencanaan_id" id="perencanaanSelect"
                                                        class="form-control @error('perencanaan_id') is-invalid @enderror"
                                                        required>
                                                    <option value="">-- Pilih Perencanaan --</option>
                                                    @foreach($perencanaanList as $p)
                                                        <option value="{{ $p->perencanaan_id }}"
                                                                {{ old('perencanaan_id') == $p->perencanaan_id ? 'selected' : '' }}>
                                                            {{ $p->judul }}
                                                            ({{ \Carbon\Carbon::create()->month($p->bulan)->translatedFormat('F') }}
                                                            {{ $p->tahun }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('perencanaan_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Detail Perencanaan</label>
                                                <select name="detail_perencanaan_id" id="detailSelect"
                                                    class="form-control @error('detail_perencanaan_id') is-invalid @enderror"                                                        disabled>
                                                    <option value="">-- Pilih Detail (opsional) --</option>
                                                </select>
                                                @error('detail_perencanaan_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted" id="detailHint">Pilih perencanaan terlebih dahulu</small>
                                            </div>
                                        </div>

                                        <!-- Info Perencanaan Terpilih -->
                                        <div id="perencanaanInfo" class="alert alert-info d-flex align-items-center p-3 mt-3 d-none">
                                            <div class="avatar-text bg-info text-white me-3">
                                                <i class="feather-calendar"></i>
                                            </div>
                                            <div id="perencanaanInfoContent" class="flex-grow-1"></div>
                                        </div>
                                    </div>

                                    <!-- Detail Perencanaan Preview -->
                                    <div id="detailPreviewWrap" class="mb-4 d-none">
                                        <h6 class="fw-semibold mb-3">
                                            <i class="feather-list text-primary me-2"></i>Detail Perencanaan yang Dipilih
                                        </h6>
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="d-flex align-items-start gap-3">
                                                            <div class="avatar-text bg-soft-primary text-primary">
                                                                <i class="feather-target"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted d-block small">Kegiatan</span>
                                                                <span class="fw-semibold" id="prev_perencanaan">–</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="d-flex align-items-start gap-3">
                                                            <div class="avatar-text bg-soft-success text-success">
                                                                <i class="feather-check-circle"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted d-block small">Target</span>
                                                                <span class="fw-semibold" id="prev_target">–</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="d-flex align-items-start gap-3">
                                                            <div class="avatar-text bg-soft-info text-info">
                                                                <i class="feather-file-text"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted d-block small">Deskripsi</span>
                                                                <span class="fw-semibold" id="prev_deskripsi">–</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="d-flex align-items-start gap-3">
                                                            <div class="avatar-text bg-soft-warning text-warning">
                                                                <i class="feather-calendar"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted d-block small">Pelaksanaan</span>
                                                                <span class="fw-semibold" id="prev_pelaksanaan">–</span>
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
                                                <label class="form-label fw-semibold">
                                                    Judul Realisasi <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="judul"
                                                       class="form-control @error('judul') is-invalid @enderror"
                                                       value="{{ old('judul') }}"
                                                       placeholder="Contoh: Realisasi Pembelian ATK Bulan Januari"
                                                       required>
                                                @error('judul') 
                                                    <div class="invalid-feedback">{{ $message }}</div> 
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-semibold">
                                                    Tanggal Realisasi <span class="text-danger">*</span>
                                                </label>
                                                <div class="custom-date-input">
                                                    <input type="text" 
                                                           id="tanggalRealisasiDisplay"
                                                           class="form-control date-display" 
                                                           placeholder="Pilih tanggal"
                                                           value="{{ old('tanggal_realisasi') ? \Carbon\Carbon::parse(old('tanggal_realisasi'))->format('d/m/Y') : date('d/m/Y') }}"
                                                           readonly>
                                                    <input type="hidden" name="tanggal_realisasi" id="tanggalRealisasi" value="{{ old('tanggal_realisasi', date('Y-m-d')) }}">
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
                                                <label class="form-label fw-semibold">
                                                    Deskripsi <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="deskripsi"
                                                          class="form-control @error('deskripsi') is-invalid @enderror"
                                                          rows="4"
                                                          placeholder="Jelaskan apa yang sudah direalisasikan..."
                                                          required>{{ old('deskripsi') }}</textarea>
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
                                                <label class="form-label fw-semibold">
                                                    Status Target <span class="text-danger">*</span>
                                                </label>
                                                <select name="status_target" id="statusTarget"
                                                        class="form-control @error('status_target') is-invalid @enderror"
                                                        required>
                                                    <option value="">-- Pilih Status --</option>
                                                    <option value="sesuai" {{ old('status_target') == 'sesuai' ? 'selected' : '' }}>✓ Sesuai Target</option>
                                                    <option value="tidak" {{ old('status_target') == 'tidak' ? 'selected' : '' }}>✗ Tidak Sesuai</option>
                                                    <option value="sebagian" {{ old('status_target') == 'sebagian' ? 'selected' : '' }}>◑ Tercapai Sebagian</option>
                                                </select>
                                                @error('status_target') 
                                                    <div class="invalid-feedback">{{ $message }}</div> 
                                                @enderror
                                            </div>

                                            <div class="col-md-8 mb-3">
                                                <label class="form-label fw-semibold">
                                                    Persentase
                                                    <span class="badge bg-primary ms-2" id="persentaseLabel">
                                                        {{ old('persentase', 0) }}%
                                                    </span>
                                                </label>
                                                <div class="d-flex align-items-center gap-3">
                                                    <input type="range" id="persentaseRange"
                                                           class="form-range flex-grow-1"
                                                           min="0" max="100" step="1"
                                                           value="{{ old('persentase', 0) }}">
                                                    <input type="number" id="persentaseInput"
                                                           class="form-control" style="width:80px"
                                                           min="0" max="100"
                                                           value="{{ old('persentase', 0) }}">
                                                </div>
                                                <input type="hidden" name="persentase" id="persentaseHidden"
                                                       value="{{ old('persentase', 0) }}">
                                                <small class="text-muted">
                                                    <i class="feather-info me-1"></i>Otomatis terisi saat pilih status, atau geser manual
                                                </small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Keterangan Target</label>
                                                <textarea name="keterangan_target" class="form-control" rows="3"
                                                          placeholder="Keterangan pencapaian target...">{{ old('keterangan_target') }}</textarea>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Catatan Tambahan</label>
                                                <textarea name="catatan_tambahan" class="form-control" rows="3"
                                                          placeholder="Catatan lain yang perlu disampaikan...">{{ old('catatan_tambahan') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan (4 kolom) - Lampiran -->
                                <div class="col-lg-4">
                                    <div class="card stretch stretch-full">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="feather-paperclip me-2"></i>Lampiran
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="upload-area" id="dropZone">
                                                <div class="text-center py-4">
                                                    <i class="feather-upload-cloud display-4 text-muted mb-3"></i>
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

                                            <!-- Info Tips -->
                                            <div class="alert alert-info mt-3 py-2 small">
                                                <i class="feather-info me-1"></i>
                                                Format yang didukung: PDF, JPG, PNG, DOC, XLS
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between border-top pt-4 mt-4">
                                <a href="{{ route('realisasi.index') }}" class="btn btn-light">
                                    <i class="feather-arrow-left me-2"></i>Kembali ke Daftar
                                </a>
                                <div class="d-flex gap-2">
                                    <button type="reset" class="btn btn-outline-secondary" id="resetBtn">
                                        <i class="feather-refresh-ccw me-2"></i>Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="feather-save me-2"></i>Simpan Realisasi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
</div>
@endsection

@push('styles')
<style>
/* Card Styles */
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    border-radius: 12px;
    margin-bottom: 1rem;
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

/* Form Elements */
.form-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.4rem;
}

.form-control {
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #3454D1;
    box-shadow: 0 0 0 0.2rem rgba(52, 84, 209, 0.1);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

/* Avatar Text */
.avatar-text {
    width: 42px;
    height: 42px;
    border-radius: 10px;
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
    background-color: rgba(23, 162, 184, 0.1);
    color: #17a2b8;
}

.avatar-text.bg-soft-warning {
    background-color: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

/* Custom Date Picker */
.custom-date-input {
    position: relative;
    width: 100%;
}

.custom-date-input .date-display {
    background-color: white;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.6rem 1rem;
    padding-right: 40px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.custom-date-input .date-display:hover {
    border-color: #3454D1;
    background-color: #f8fafc;
}

.custom-date-input .date-display:focus {
    outline: none;
    border-color: #3454D1;
    box-shadow: 0 0 0 0.2rem rgba(52, 84, 209, 0.1);
}

.custom-date-input .calendar-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    pointer-events: none;
    transition: color 0.2s ease;
}

.custom-date-input:hover .calendar-icon {
    color: #3454D1;
}

/* Date Picker Dropdown */
.custom-date-picker {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    width: 320px;
    background: white;
    border-radius: 16px;
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
    padding: 0 0.5rem;
}

.month-nav {
    width: 32px;
    height: 32px;
    border: none;
    background: #f8f9fa;
    border-radius: 8px;
    color: #495057;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.month-nav:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: scale(0.95);
}

.month-year {
    font-weight: 600;
    font-size: 1rem;
    color: #2d3748;
}

.date-picker-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    margin-bottom: 0.5rem;
}

.date-picker-weekdays span {
    font-size: 0.8rem;
    font-weight: 600;
    color: #718096;
    padding: 0.5rem 0;
}

.date-picker-weekdays span:first-child {
    color: #e53e3e;
}

.date-picker-weekdays span:last-child {
    color: #3182ce;
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
    font-size: 0.9rem;
    color: #2d3748;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
}

.date-picker-days button:hover:not(.empty) {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: scale(0.95);
}

.date-picker-days button.today {
    background-color: #e9ecef;
    font-weight: 700;
    color: #3454D1;
    border: 2px solid #3454D1;
}

.date-picker-days button.selected {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    transform: scale(0.95);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
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
    border-top: 1px solid #e9ecef;
}

.btn-clear, .btn-today {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 8px;
    font-size: 0.85rem;
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
    color: #dc3545;
}

.btn-today {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-today:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Alert Styles */
.alert-info {
    background-color: rgba(23, 162, 184, 0.05);
    border: 1px solid rgba(23, 162, 184, 0.1);
    color: #055160;
    border-radius: 12px;
}

.alert-warning {
    background-color: rgba(255, 193, 7, 0.05);
    border: 1px solid rgba(255, 193, 7, 0.1);
    color: #856404;
    border-radius: 12px;
}

/* Upload Area */
.upload-area {
    border: 2px dashed #e2e8f0;
    border-radius: 12px;
    background-color: #f8fafc;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-area:hover {
    border-color: #3454D1;
    background-color: rgba(52, 84, 209, 0.02);
}

.upload-area.dragover {
    border-color: #3454D1;
    background-color: rgba(52, 84, 209, 0.05);
}

/* File Preview */
.file-preview {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    background-color: white;
    transition: all 0.2s ease;
}

.file-preview:hover {
    border-color: #3454D1;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.file-preview .file-thumb {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    object-fit: cover;
}

.file-preview .btn-outline-danger {
    padding: 0.25rem 0.5rem;
    border-color: #dee2e6;
}

.file-preview .btn-outline-danger:hover {
    background-color: #dc3545;
    color: white;
    border-color: #dc3545;
}

/* Range Input */
.form-range {
    height: 1.5rem;
    padding: 0;
}

.form-range::-webkit-slider-thumb {
    background-color: #3454D1;
}

.form-range::-webkit-slider-thumb:active {
    background-color: #2a43b0;
}

/* Badge */
.badge.bg-primary {
    background-color: #3454D1 !important;
    padding: 0.5rem 1rem;
    font-weight: 500;
}

/* Border Top */
.border-top {
    border-top: 1px solid #e9ecef !important;
}

/* Responsive */
@media (max-width: 768px) {
    .card-header, .card-body {
        padding: 1rem;
    }
    
    .avatar-text {
        width: 36px;
        height: 36px;
        font-size: 14px;
    }
    
    .custom-date-picker {
        width: 280px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ─── Elemen DOM ───────────────────────────────────────────────────────────
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

    const bulanNames = ['','Januari','Februari','Maret','April','Mei','Juni',
                        'Juli','Agustus','September','Oktober','November','Desember'];

    // ─── 1. Pilih Perencanaan → Load detail via AJAX ──────────────────────────
    if (perencanaanSelect) {
        perencanaanSelect.addEventListener('change', function() {
            const id = this.value;

            // Reset
            detailSelect.disabled = true;
            detailSelect.innerHTML = '<option value="">Memuat...</option>';
            perencanaanInfo.classList.add('d-none');
            detailPreviewWrap.classList.add('d-none');

            if (!id) {
                detailSelect.innerHTML = '<option value="">-- Pilih Detail (opsional) --</option>';
                detailHint.textContent = 'Pilih perencanaan terlebih dahulu';
                return;
            }

            fetch(`{{ route('ajax.detail-perencanaan') }}?perencanaan_id=${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                const details = data.details || [];

                detailSelect.innerHTML = '<option value="">-- Pilih Detail (opsional) --</option>';

                if (details.length > 0) {
                    details.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d.detail_id;
                        opt.textContent = d.nomor + '. ' + d.perencanaan;

                        // Simpan semua data langsung di attribute option
                        opt.dataset.perencanaan = d.perencanaan || '';
                        opt.dataset.target = d.target || '';
                        opt.dataset.deskripsi = d.deskripsi || '';
                        opt.dataset.pelaksanaan = d.pelaksanaan || '';

                        detailSelect.appendChild(opt);
                    });
                    detailHint.textContent = details.length + ' detail tersedia';
                } else {
                    const opt = document.createElement('option');
                    opt.disabled = true;
                    opt.textContent = 'Tidak ada detail';
                    detailSelect.appendChild(opt);
                    detailHint.textContent = 'Tidak ada detail untuk perencanaan ini';
                }

                detailSelect.disabled = false;

                // Info header
                const p = data.perencanaan;
                infoContent.innerHTML = `<strong>${p.judul}</strong> &mdash; ${bulanNames[p.bulan] || p.bulan} ${p.tahun}`;
                perencanaanInfo.classList.remove('d-none');
            })
            .catch(err => {
                console.error('AJAX error:', err);
                detailSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                detailHint.textContent = 'Terjadi kesalahan, coba refresh halaman';
                showToast('Gagal memuat data detail', 'error');
            });
        });
    }

    // ─── 2. Pilih Detail → Tampilkan preview ────────────────────────────────
    if (detailSelect) {
        detailSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];

            if (!this.value || !selected) {
                detailPreviewWrap.classList.add('d-none');
                return;
            }

            // Ambil data dari attribute option
            const perencanaan = selected.dataset.perencanaan;
            const target = selected.dataset.target;
            const deskripsi = selected.dataset.deskripsi;
            const pelaksanaan = selected.dataset.pelaksanaan;

            // Isi preview
            prevPerencanaan.textContent = perencanaan || '–';
            prevTarget.textContent = target || '–';
            prevDeskripsi.textContent = deskripsi || '–';
            prevPelaksanaan.textContent = pelaksanaan || '–';

            detailPreviewWrap.classList.remove('d-none');
        });
    }

    // ─── 3. Custom Date Picker ──────────────────────────────────────────────
    const tanggalDisplay = document.getElementById('tanggalRealisasiDisplay');
    const tanggalHidden = document.getElementById('tanggalRealisasi');
    const tanggalPicker = document.getElementById('tanggalPicker');
    const monthYear = document.getElementById('monthYear');
    const calendarDays = document.getElementById('calendarDays');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const clearDateBtn = document.getElementById('clearDate');
    const todayBtn = document.getElementById('todayDate');

    let currentDate = new Date();
    let selectedDate = tanggalHidden.value ? new Date(tanggalHidden.value) : new Date();

    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

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

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        monthYear.textContent = `${monthNames[month]} ${year}`;
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevMonthDays = new Date(year, month, 0).getDate();
        
        let html = '';
        
        // Previous month days
        for (let i = firstDay; i > 0; i--) {
            const day = prevMonthDays - i + 1;
            html += `<button type="button" class="prev-month">${day}</button>`;
        }
        
        // Current month days
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const isToday = isSameDay(date, new Date());
            const isSelected = selectedDate && isSameDay(date, selectedDate);
            
            let classes = '';
            if (isToday) classes += ' today';
            if (isSelected) classes += ' selected';
            
            html += `<button type="button" class="${classes}" data-day="${day}">${day}</button>`;
        }
        
        // Next month days
        const totalDays = firstDay + daysInMonth;
        const remainingCells = 42 - totalDays;
        for (let day = 1; day <= remainingCells; day++) {
            html += `<button type="button" class="next-month">${day}</button>`;
        }
        
        calendarDays.innerHTML = html;
        
        // Add click handlers
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

    function isSameDay(date1, date2) {
        return date1.getDate() === date2.getDate() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getFullYear() === date2.getFullYear();
    }

    // Toggle date picker
    tanggalDisplay.addEventListener('click', function(e) {
        e.stopPropagation();
        tanggalPicker.classList.toggle('show');
        renderCalendar();
    });

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-date-input')) {
            tanggalPicker.classList.remove('show');
        }
    });

    // Month navigation
    prevMonthBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    // Clear date
    clearDateBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        selectedDate = null;
        tanggalDisplay.value = '';
        tanggalHidden.value = '';
        renderCalendar();
        tanggalPicker.classList.remove('show');
    });

    // Today
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

    // ─── 4. Status Target & Persentase (Dua Arah) ──────────────────────────────
    const statusTarget = document.getElementById('statusTarget');
    const persentaseRange = document.getElementById('persentaseRange');
    const persentaseInput = document.getElementById('persentaseInput');
    const persentaseLabel = document.getElementById('persentaseLabel');
    const persentaseHidden = document.getElementById('persentaseHidden');

    // Fungsi untuk update semua elemen persentase
    function updatePersentase(val) {
        val = parseInt(val);
        if (isNaN(val)) val = 0;
        val = Math.min(100, Math.max(0, val));
        
        persentaseRange.value = val;
        persentaseInput.value = val;
        persentaseHidden.value = val;
        persentaseLabel.textContent = val + '%';
        
        // Update status target berdasarkan persentase (hanya jika tidak sedang dalam mode manual)
        if (!window.isManuallyChangingStatus) {
            updateStatusFromPersentase(val);
        }
    }

    // Fungsi untuk update status berdasarkan persentase
    function updateStatusFromPersentase(val) {
        let newStatus = '';
        if (val == 100) {
            newStatus = 'sesuai';
        } else if (val == 0) {
            newStatus = 'tidak';
        } else if (val > 0 && val < 100) {
            newStatus = 'sebagian';
        }
        
        if (newStatus && statusTarget.value !== newStatus) {
            statusTarget.value = newStatus;
        }
    }

    // Fungsi untuk update persentase berdasarkan status
    function updatePersentaseFromStatus(status) {
        const map = { 
            sesuai: 100, 
            tidak: 0, 
            sebagian: 50 
        };
        
        if (status in map) {
            window.isManuallyChangingStatus = true;
            updatePersentase(map[status]);
            setTimeout(() => {
                window.isManuallyChangingStatus = false;
            }, 100);
        }
    }

    // Flag untuk mencegah infinite loop
    window.isManuallyChangingStatus = false;

    // Event listener untuk perubahan status
    if (statusTarget) {
        statusTarget.addEventListener('change', function() {
            updatePersentaseFromStatus(this.value);
        });
    }

    // Event listener untuk range slider
    if (persentaseRange) {
        persentaseRange.addEventListener('input', function() {
            updatePersentase(this.value);
        });
    }

    // Event listener untuk input number
    if (persentaseInput) {
        persentaseInput.addEventListener('input', function() {
            updatePersentase(this.value);
        });
    }

    // Inisialisasi awal - sync berdasarkan value yang sudah ada
    if (statusTarget.value) {
        updatePersentaseFromStatus(statusTarget.value);
    } else if (persentaseHidden.value) {
        updatePersentase(persentaseHidden.value);
    }

    // ─── 5. Multiple File Upload + Preview ───────────────────────────────────
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
                    <div class="fw-semibold">${file.name}</div>
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
                    <div class="fw-semibold">${file.name}</div>
                    <small class="text-muted">${size}</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                    <i class="feather-trash-2"></i>
                </button>
            `;
        }
        
        previewContainer.appendChild(div);
    }

    window.removeFile = function(index) {
        selectedFiles[index] = null;
        document.getElementById(`fp-${index}`)?.remove();
        syncFileInput();
    };

    function syncFileInput() {
        const dt = new DataTransfer();
        selectedFiles.filter(Boolean).forEach(f => dt.items.add(f));
        lampiranInput.files = dt.files;
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
            lampiranInput.click();
        });
    }

    // ─── 6. Form Submit ─────────────────────────────────────────────────────
    const form = document.getElementById('formRealisasi');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');

    if (form) {
        form.addEventListener('submit', function(e) {
            // Validasi perencanaan
            if (!perencanaanSelect.value) {
                e.preventDefault();
                showToast('Silakan pilih perencanaan terlebih dahulu', 'error');
                perencanaanSelect.focus();
                return;
            }

            // Validasi judul
            const judulInput = document.querySelector('input[name="judul"]');
            if (!judulInput.value.trim()) {
                e.preventDefault();
                showToast('Judul realisasi harus diisi', 'error');
                judulInput.focus();
                return;
            }

            // Validasi tanggal
            if (!tanggalHidden.value) {
                e.preventDefault();
                showToast('Tanggal realisasi harus diisi', 'error');
                tanggalDisplay.focus();
                return;
            }

            // Validasi deskripsi
            const deskripsiInput = document.querySelector('textarea[name="deskripsi"]');
            if (!deskripsiInput.value.trim()) {
                e.preventDefault();
                showToast('Deskripsi harus diisi', 'error');
                deskripsiInput.focus();
                return;
            }

            // Validasi status target
            if (!statusTarget.value) {
                e.preventDefault();
                showToast('Status target harus dipilih', 'error');
                statusTarget.focus();
                return;
            }

            // Show loading
            submitBtn.innerHTML = '<i class="feather-loader me-2"></i>Menyimpan...';
            submitBtn.disabled = true;
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Reset Form?',
                text: 'Semua data yang sudah diisi akan hilang. Lanjutkan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="feather-refresh-ccw me-2"></i>Ya, Reset',
                cancelButtonText: '<i class="feather-x me-2"></i>Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.reset();
                    
                    // Reset preview
                    detailPreviewWrap.classList.add('d-none');
                    perencanaanInfo.classList.add('d-none');
                    previewContainer.innerHTML = '';
                    selectedFiles = [];
                    
                    // Reset tanggal
                    const today = new Date();
                    selectedDate = today;
                    tanggalDisplay.value = formatDate(today);
                    tanggalHidden.value = formatDateYMD(today);
                    
                    // Reset persentase
                    updatePersentase(0);
                    
                    showToast('Form berhasil direset', 'success');
                }
            });
        });
    }

    // ─── 7. Toast Helper ───────────────────────────────────────────────────
    function showToast(message, type = 'info') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        Toast.fire({
            icon: type,
            title: message
        });
    }

    // Initialize
    renderCalendar();
});

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