@extends('layouts.app')

@section('title', 'Tambah Mutasi Kas')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Tambah Mutasi Kas</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mutasi-kas.index') }}">Mutasi Kas</a></li>
                <li class="breadcrumb-item active">Tambah</li>
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
                    <a href="{{ route('mutasi-kas.index') }}" class="btn btn-outline-secondary">
                        <i class="feather-list me-2"></i>
                        <span>Daftar Transaksi</span>
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
            <div class="col-lg-12">
                <!-- form card -->
                <div class="card table-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-plus-circle me-2 text-primary"></i>Form Tambah Transaksi Kas
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- info sistem -->
                        <div class="alert alert-info-custom mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="info-icon-custom bg-info-soft text-info">
                                    <i class="feather-clock"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-2">Informasi Sistem</h6>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Tanggal Hari Ini</small>
                                            <span class="fw-semibold">{{ date('d/m/Y') }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Waktu Server</small>
                                            <span class="fw-semibold">{{ now()->format('d/m/Y H:i:s') }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Transaksi Terakhir</small>
                                            @php
                                                $last = \App\Models\MutasiKas::orderBy('tanggal', 'desc')->first();
                                            @endphp
                                            @if($last)
                                                <span class="fw-semibold">{{ $last->tanggal->format('d/m/Y') }}</span>
                                                <small class="text-muted d-block">{{ Str::limit($last->uraian, 30) }}</small>
                                            @else
                                                <span class="fw-semibold">Belum ada transaksi</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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

                        <form action="{{ route('mutasi-kas.store') }}" method="POST" id="transaksiForm">
                            @csrf
                            
                            <!-- informasi transaksi -->
                            <h6 class="fw-semibold mb-3">
                                <i class="feather-dollar-sign me-2 text-primary"></i>Informasi Transaksi
                            </h6>
                            
                            <div class="row g-3 mb-4">
                                <!-- tanggal dengan custom date picker -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Tanggal Transaksi <span class="text-danger">*</span>
                                    </label>
                                    <div class="custom-date-input" id="tanggalWrapper">
                                        <input type="text" 
                                               id="tanggalDisplay"
                                               class="form-control date-display @error('tanggal') is-invalid @enderror" 
                                               placeholder="Pilih tanggal"
                                               value="{{ old('tanggal') ? \Carbon\Carbon::parse(old('tanggal'))->format('d/m/Y') : date('d/m/Y') }}"
                                               readonly>
                                        <input type="hidden" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}">
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
                                    @error('tanggal')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="feather-info me-1"></i>Tanggal transaksi bisa sebelum atau sesudah hari ini
                                    </div>
                                </div>

                                <!-- kode transaksi dengan tom select -->
                                <div class="col-md-6">
                                    <label for="kode_transaksi_id" class="form-label">
                                        Kode Transaksi <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select tom-select @error('kode_transaksi_id') is-invalid @enderror" 
                                            id="kode_transaksi_id" 
                                            name="kode_transaksi_id"
                                            required>
                                        <option value="">-- Pilih Kode Transaksi --</option>
                                        @foreach ($kodeTransaksi as $kode)
                                            <option value="{{ $kode->kode_transaksi_id }}"
                                                    data-kategori="{{ $kode->kategori->nama_kategori ?? '' }}"
                                                    data-kode="{{ $kode->kode }}"
                                                {{ old('kode_transaksi_id') == $kode->kode_transaksi_id ? 'selected' : '' }}>
                                                [{{ $kode->kode }}] {{ $kode->keterangan }}
                                                @if($kode->kategori)
                                                    ({{ $kode->kategori->nama_kategori }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kode_transaksi_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="feather-search me-1"></i>Ketik untuk mencari kode atau keterangan
                                    </div>
                                </div>

                                <!-- uraian -->
                                <div class="col-12">
                                    <label for="uraian" class="form-label">
                                        Uraian / Keterangan <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('uraian') is-invalid @enderror" 
                                              id="uraian" 
                                              name="uraian" 
                                              rows="4"
                                              placeholder="Deskripsi lengkap transaksi (contoh: Pembayaran SPP, Pembelian ATK, dll)"
                                              required>{{ old('uraian') }}</textarea>
                                    @error('uraian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- debit & kredit -->
                            <h6 class="fw-semibold mb-3">
                                <i class="feather-arrow-up-down me-2 text-primary"></i>Nominal Transaksi
                            </h6>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="debit" class="form-label text-success">
                                        <i class="feather-arrow-down-left me-1"></i>Debit (Pemasukan)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" 
                                               class="form-control currency-input @error('debit') is-invalid @enderror" 
                                               id="debit" 
                                               name="debit" 
                                               value="{{ old('debit') }}"
                                               placeholder="0">
                                        @error('debit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text text-success">
                                        <i class="feather-arrow-down-left me-1"></i>Isi jika transaksi pemasukan kas
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="kredit" class="form-label text-danger">
                                        <i class="feather-arrow-up-right me-1"></i>Kredit (Pengeluaran)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" 
                                               class="form-control currency-input @error('kredit') is-invalid @enderror" 
                                               id="kredit" 
                                               name="kredit" 
                                               value="{{ old('kredit') }}"
                                               placeholder="0">
                                        @error('kredit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text text-danger">
                                        <i class="feather-arrow-up-right me-1"></i>Isi jika transaksi pengeluaran kas
                                    </div>
                                </div>
                            </div>

                            <!-- catatan penting -->
                            <div class="alert alert-warning-custom mb-4">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="info-icon-custom bg-warning-soft text-warning">
                                        <i class="feather-alert-circle"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-2">Catatan Penting:</h6>
                                        <ul class="mb-0 ps-3">
                                            <li>Isi salah satu: <strong class="text-success">Debit (Pemasukan)</strong> atau <strong class="text-danger">Kredit (Pengeluaran)</strong></li>
                                            <li>Keduanya tidak boleh diisi bersamaan atau dikosongkan</li>
                                            <li>Tanggal transaksi bisa sebelum atau sesudah hari ini sesuai bukti transaksi</li>
                                            <li>Pastikan uraian diisi dengan jelas dan lengkap</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- form actions -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div class="pagination-info">
                                    <i class="feather-info me-1"></i>
                                    Pastikan data yang diisi sudah benar
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="reset" class="btn btn-outline-secondary" id="resetBtn">
                                        <i class="feather-refresh-cw me-2"></i>Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="feather-save me-2"></i>Simpan Transaksi
                                    </button>
                                </div>
                            </div>
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

.form-control.is-invalid, .form-select.is-invalid {
    border-color: var(--danger-color);
}

.form-control.is-invalid:focus, .form-select.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

.invalid-feedback {
    font-size: 0.7rem;
    color: var(--danger-color);
    margin-top: 0.25rem;
}

.form-text {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

textarea.form-control {
    resize: vertical;
}

/* Input Group */
.input-group-text {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-right: none;
    border-radius: 0.625rem 0 0 0.625rem;
    font-weight: 600;
    color: #64748b;
}

.input-group .form-control {
    border-left: none;
    border-radius: 0 0.625rem 0.625rem 0;
}

.input-group .form-control:focus {
    border-left: none;
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
    border-radius: 1rem;
    padding: 1rem 1.25rem;
}

/* ============================================
   INFO ICON CUSTOM
   ============================================ */
.info-icon-custom {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    flex-shrink: 0;
}

.info-icon-custom i {
    font-size: 1.25rem;
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
   TOM SELECT CUSTOMIZATION
   ============================================ */
.ts-wrapper.form-control {
    padding: 0;
    border: none;
}

.ts-wrapper .ts-control {
    border: 1px solid #e2e8f0;
    border-radius: 0.625rem;
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
    min-height: 42px;
    box-shadow: none;
}

.ts-wrapper.focus .ts-control {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(52, 84, 209, 0.1);
}

.ts-wrapper.has-items .ts-control {
    padding: 0.4rem 0.875rem;
}

.ts-dropdown {
    border: none;
    border-radius: 0.75rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    margin-top: 4px;
}

.ts-dropdown .option {
    padding: 0.625rem 0.875rem;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.875rem;
}

.ts-dropdown .option:last-child {
    border-bottom: none;
}

.ts-dropdown .option:hover {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
}

.ts-dropdown .option.active {
    background: #f1f5f9;
    color: #2c3e50;
}

/* ============================================
   SOFT BACKGROUNDS
   ============================================ */
.bg-primary-soft { background-color: rgba(52, 84, 209, 0.1); }
.bg-success-soft { background-color: rgba(37, 176, 3, 0.1); }
.bg-info-soft { background-color: rgba(23, 162, 184, 0.1); }
.bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
.bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }

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

    .form-control, .form-select {
        font-size: 0.8125rem;
    }

    .custom-date-picker {
        width: 280px;
    }

    .info-icon-custom {
        width: 36px;
        height: 36px;
    }

    .info-icon-custom i {
        font-size: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // ========================================
    // TOM SELECT - KODE TRANSAKSI
    // ========================================
    let tomSelectInstance = null;
    const kodeSelect = document.getElementById('kode_transaksi_id');
    if (kodeSelect) {
        tomSelectInstance = new TomSelect('#kode_transaksi_id', {
            create: false,
            sortField: { field: 'text', direction: 'asc' },
            searchField: ['text'],
            placeholder: 'Cari atau pilih kode transaksi...',
            highlight: true,
            maxOptions: null
        });
    }

    // ========================================
    // CUSTOM DATE PICKER
    // ========================================
    const tanggalDisplay = document.getElementById('tanggalDisplay');
    const tanggalHidden = document.getElementById('tanggal');
    const tanggalPicker = document.getElementById('tanggalPicker');
    const monthYear = document.getElementById('monthYear');
    const calendarDays = document.getElementById('calendarDays');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const clearDateBtn = document.getElementById('clearDate');
    const todayBtn = document.getElementById('todayDate');

    let currentDate = tanggalHidden.value ? new Date(tanggalHidden.value) : new Date();
    let selectedDate = tanggalHidden.value ? new Date(tanggalHidden.value) : new Date();

    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

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
    // CURRENCY FORMATTING
    // ========================================
    const debitInput = document.getElementById('debit');
    const kreditInput = document.getElementById('kredit');

    function formatCurrency(input) {
        let value = input.value.replace(/[^\d]/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
            input.value = value;
        }
    }

    function parseCurrency(value) {
        return parseInt(value.replace(/[^\d]/g, '')) || 0;
    }

    if (debitInput && kreditInput) {
        [debitInput, kreditInput].forEach(input => {
            input.addEventListener('blur', function() { formatCurrency(this); });
            input.addEventListener('focus', function() { this.value = this.value.replace(/[^\d]/g, ''); });
            if (input.value) formatCurrency(input);
        });

        debitInput.addEventListener('input', function() {
            if (parseCurrency(this.value) > 0) {
                kreditInput.value = '';
                kreditInput.disabled = true;
                kreditInput.classList.remove('is-invalid');
                this.classList.remove('is-invalid');
            } else {
                kreditInput.disabled = false;
            }
        });

        kreditInput.addEventListener('input', function() {
            if (parseCurrency(this.value) > 0) {
                debitInput.value = '';
                debitInput.disabled = true;
                debitInput.classList.remove('is-invalid');
                this.classList.remove('is-invalid');
            } else {
                debitInput.disabled = false;
            }
        });
    }

    // ========================================
    // FORM VALIDATION
    // ========================================
    const form = document.getElementById('transaksiForm');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');

    function validateForm() {
        let isValid = true;
        const errors = [];

        const debitValue = parseCurrency(debitInput.value);
        const kreditValue = parseCurrency(kreditInput.value);

        if (debitValue > 0 && kreditValue > 0) {
            errors.push('Debit dan Kredit tidak boleh diisi bersamaan');
            debitInput.classList.add('is-invalid');
            kreditInput.classList.add('is-invalid');
            isValid = false;
        } else if (debitValue === 0 && kreditValue === 0) {
            errors.push('Harap isi salah satu: Debit (pemasukan) atau Kredit (pengeluaran)');
            debitInput.classList.add('is-invalid');
            kreditInput.classList.add('is-invalid');
            isValid = false;
        } else {
            debitInput.classList.remove('is-invalid');
            kreditInput.classList.remove('is-invalid');
        }

        if (!tanggalHidden.value) {
            errors.push('Tanggal transaksi harus diisi');
            tanggalDisplay.classList.add('is-invalid');
            isValid = false;
        } else {
            tanggalDisplay.classList.remove('is-invalid');
        }

        if (kodeSelect && !kodeSelect.value) {
            errors.push('Kode transaksi harus dipilih');
            kodeSelect.classList.add('is-invalid');
            isValid = false;
        } else if (kodeSelect) {
            kodeSelect.classList.remove('is-invalid');
        }

        const uraianInput = document.getElementById('uraian');
        if (!uraianInput.value.trim()) {
            errors.push('Uraian transaksi harus diisi');
            uraianInput.classList.add('is-invalid');
            isValid = false;
        } else {
            uraianInput.classList.remove('is-invalid');
        }

        return { isValid, errors };
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            const validation = validateForm();

            if (!validation.isValid) {
                e.preventDefault();

                let errorHtml = '<strong>Perhatian!</strong> Terdapat kesalahan dalam pengisian form:';
                errorHtml += '<ul class="mb-0 mt-2 ps-3">';
                validation.errors.forEach(error => {
                    errorHtml += `<li>${error}</li>`;
                });
                errorHtml += '</ul>';

                const existingAlert = document.querySelector('.alert.alert-danger-custom');
                if (existingAlert && existingAlert !== document.querySelector('.alert.alert-danger-custom:first-child')) {
                    existingAlert.remove();
                }

                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger-custom mb-4';
                alertDiv.innerHTML = `
                    <div class="d-flex align-items-start gap-3">
                        <i class="feather-alert-circle fs-4 text-danger"></i>
                        <div class="flex-grow-1">
                            <div class="fw-semibold mb-2">Terjadi Kesalahan:</div>
                            ${errorHtml}
                        </div>
                    </div>
                `;

                form.parentNode.insertBefore(alertDiv, form.parentNode.firstChild);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                [debitInput, kreditInput].forEach(input => {
                    if (input && input.value) {
                        input.value = input.value.replace(/\./g, '');
                    }
                });
                submitBtn.innerHTML = '<i class="feather-loader me-2"></i>Menyimpan...';
                submitBtn.disabled = true;
            }
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
                confirmButtonText: '<i class="feather-refresh-cw me-2"></i>Ya, Reset',
                cancelButtonText: '<i class="feather-x me-2"></i>Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.reset();
                    
                    const today = new Date();
                    selectedDate = new Date(today);
                    tanggalDisplay.value = formatDate(today);
                    tanggalHidden.value = formatDateYMD(today);
                    renderCalendar();
                    
                    if (tomSelectInstance) tomSelectInstance.clear();
                    
                    debitInput.disabled = false;
                    kreditInput.disabled = false;
                    
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    
                    const existingAlert = document.querySelector('.alert.alert-danger-custom');
                    if (existingAlert) existingAlert.remove();
                    
                    Swal.fire({ icon: 'success', title: 'Reset Berhasil!', showConfirmButton: false, timer: 1500, toast: true, position: 'top-end' });
                }
            });
        });
    }

    renderCalendar();
});

function refreshPage() { location.reload(); }

@if(session('success'))
    Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
@endif

@if(session('error'))
    Swal.fire({ icon: 'error', title: 'Oops...', text: '{{ session('error') }}', confirmButtonText: 'OK' });
@endif
</script>
@endpush