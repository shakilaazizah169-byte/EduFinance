@extends('layouts.app')

@section('title', 'Edit Kode Mutasi')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10 mb-0">Edit Kode Mutasi</h5>
            </div>
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kode-transaksi.index') }}">Kode Mutasi</a></li>
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
            <div class="col-lg-8">
                <!-- form card -->
                <div class="card table-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-edit-2 me-2 text-primary"></i>Form Edit Kode Mutasi
                        </h5>
                        <span class="badge badge-secondary-light">ID: {{ $kodeTransaksi->kode_transaksi_id }}</span>
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

                        <form action="{{ route('kode-transaksi.update', $kodeTransaksi->kode_transaksi_id) }}" method="POST" id="kodeTransaksiForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <label for="kode" class="form-label">
                                    Kode Mutasi <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('kode') is-invalid @enderror" 
                                       id="kode" 
                                       name="kode" 
                                       value="{{ old('kode', $kodeTransaksi->kode) }}"
                                       placeholder="Masukkan kode transaksi"
                                       required
                                       autofocus>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="feather-info me-1"></i>
                                    Kode harus unik dan mudah diingat (maksimal 10 karakter)
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="keterangan" class="form-label">
                                    Keterangan <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                          id="keterangan" 
                                          name="keterangan" 
                                          rows="3"
                                          placeholder="Deskripsi atau keterangan dari kode transaksi"
                                          required>{{ old('keterangan', $kodeTransaksi->keterangan) }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="feather-info me-1"></i>
                                    Jelaskan dengan detail fungsi atau penggunaan kode transaksi ini
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="kategori_id" class="form-label">
                                    Kategori <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('kategori_id') is-invalid @enderror" 
                                        id="kategori_id" 
                                        name="kategori_id"
                                        required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($kategori as $kat)
                                        <option value="{{ $kat->kategori_id }}" 
                                                {{ old('kategori_id', $kodeTransaksi->kategori_id) == $kat->kategori_id ? 'selected' : '' }}>
                                            {{ $kat->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="feather-info me-1"></i>
                                    Pilih kategori yang sesuai dengan jenis transaksi
                                </div>
                            </div>

                            <div class="alert alert-info-custom mb-4">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="feather-info fs-5 text-primary mt-1"></i>
                                    <div>
                                        <strong>Informasi Kode Mutasi:</strong>
                                        <ul class="mb-0 mt-2 ps-3">
                                            <li>Dibuat pada: <strong>{{ $kodeTransaksi->created_at->format('d F Y, H:i') }}</strong></li>
                                            <li>Terakhir diubah: <strong>{{ $kodeTransaksi->updated_at->format('d F Y, H:i') }}</strong></li>
                                            <li>Total transaksi: <strong>{{ $kodeTransaksi->mutasiKas->count() }} transaksi</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div class="pagination-info">
                                    <i class="feather-info me-1"></i>
                                    Pastikan data yang diisi sudah benar
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('kode-transaksi.index') }}" class="btn btn-outline-secondary">
                                        <i class="feather-x me-2"></i>Batal
                                    </a>
                                    <a href="{{ route('kode-transaksi.create') }}" class="btn btn-outline-primary">
                                        <i class="feather-plus me-2"></i>Tambah Baru
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i>Update Kode
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- info sidebar -->
            <div class="col-lg-4">
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-info me-2 text-primary"></i>Detail Kode Mutasi
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
                            <div class="info-value-detail fw-semibold fs-5">{{ $kodeTransaksi->kode }}</div>
                        </div>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Kategori</div>
                            <div class="info-value-detail">
                                <span class="badge badge-info-light">{{ $kodeTransaksi->kategori->nama_kategori }}</span>
                            </div>
                        </div>
                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Total Transaksi</div>
                            <div class="info-value-detail">
                                <span class="badge badge-success-light">{{ $kodeTransaksi->mutasiKas->count() }} transaksi</span>
                            </div>
                        </div>

                        <hr>

                        <div class="info-item-detail mb-3">
                            <div class="info-label-detail">Dibuat Pada</div>
                            <div class="info-value-detail small">{{ $kodeTransaksi->created_at->format('d F Y, H:i') }}</div>
                        </div>
                        <div class="info-item-detail">
                            <div class="info-label-detail">Terakhir Diubah</div>
                            <div class="info-value-detail small">{{ $kodeTransaksi->updated_at->format('d F Y, H:i') }}</div>
                        </div>
                    </div>
                </div>

                @if($kodeTransaksi->mutasiKas->count() > 0)
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-dollar-sign me-2 text-primary"></i>Ringkasan Transaksi
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

                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-clock me-2 text-primary"></i>Transaksi Terakhir
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group-custom">
                            @foreach($kodeTransaksi->mutasiKas->take(5) as $mutasi)
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
                                onclick="deleteKodeTransaksi({{ $kodeTransaksi->kode_transaksi_id }}, '{{ addslashes($kodeTransaksi->kode) }}')">
                            <i class="feather-trash-2 me-2"></i>Hapus Kode Mutasi
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
   SOFT BACKGROUNDS
   ============================================ */
.bg-danger-soft {
    background-color: rgba(220, 53, 69, 0.08);
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

    const firstInput = document.getElementById('kode');
    if (firstInput) {
        firstInput.focus();
        firstInput.select();
    }

    const form = document.getElementById('kodeTransaksiForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const kode = document.getElementById('kode').value.trim();
            const keterangan = document.getElementById('keterangan').value.trim();
            const kategori = document.getElementById('kategori_id').value;
            
            if (kode.length < 1) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Kode transaksi harus diisi',
                    confirmButtonColor: '#3454D1'
                });
                return false;
            }

            if (kode.length > 10) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Kode transaksi maksimal 10 karakter',
                    confirmButtonColor: '#3454D1'
                });
                return false;
            }

            if (keterangan.length < 5) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Keterangan minimal 5 karakter',
                    confirmButtonColor: '#3454D1'
                });
                return false;
            }

            if (!kategori) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Kategori harus dipilih',
                    confirmButtonColor: '#3454D1'
                });
                return false;
            }
        });
    }

    const kodeInput = document.getElementById('kode');
    if (kodeInput) {
        kodeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
});

function refreshPage() {
    location.reload();
}

function deleteKodeTransaksi(id, kode) {
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