@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Tambah Kategori</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}">Kategori</a></li>
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
                    <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary">
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
                            <i class="feather-folder-plus me-2 text-primary"></i>Form Tambah Kategori
                        </h5>
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

                        <form action="{{ route('kategori.store') }}" method="POST" id="kategoriForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="nama_kategori" class="form-label">
                                    Nama Kategori <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nama_kategori') is-invalid @enderror" 
                                       id="nama_kategori" 
                                       name="nama_kategori" 
                                       value="{{ old('nama_kategori') }}"
                                       placeholder="Masukkan nama kategori"
                                       required
                                       autofocus>
                                @error('nama_kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="feather-info me-1"></i>
                                    Contoh: Penerimaan Pendapatan SPP, Pengeluaran Biaya Operasional, dll.
                                </div>
                            </div>

                            <div class="alert alert-info-custom mb-4">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="feather-info fs-5 text-primary mt-1"></i>
                                    <div>
                                        <strong>Tips Penamaan Kategori:</strong>
                                        <ul class="mb-0 mt-2 ps-3">
                                            <li>Gunakan nama yang jelas dan deskriptif</li>
                                            <li>Awali dengan "Penerimaan" atau "Pengeluaran" untuk memudahkan identifikasi</li>
                                            <li>Hindari singkatan yang tidak umum</li>
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
                                    <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary">
                                        <i class="feather-x me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i>Simpan Kategori
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
                            <i class="feather-info me-2 text-primary"></i>Informasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="info-card-custom mb-4">
                            <div class="d-flex align-items-start gap-2">
                                <i class="feather-folder text-primary mt-1"></i>
                                <div>
                                    <strong>Tentang Kategori</strong>
                                    <p class="small text-muted mb-0 mt-1">
                                        Kategori digunakan untuk mengelompokkan kode transaksi berdasarkan jenisnya.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-semibold mb-2 small text-uppercase text-muted">
                                <i class="feather-trending-up me-1"></i>Contoh Kategori Penerimaan:
                            </h6>
                            <ul class="small text-muted mb-0 ps-3">
                                <li>Penerimaan Pendapatan SPP</li>
                                <li>Penerimaan Pendapatan Uang Gedung</li>
                                <li>Penerimaan Pendapatan Uang Kegiatan</li>
                                <li>Penerimaan Pendapatan Lainnya</li>
                            </ul>
                        </div>

                        <div>
                            <h6 class="fw-semibold mb-2 small text-uppercase text-muted">
                                <i class="feather-trending-down me-1"></i>Contoh Kategori Pengeluaran:
                            </h6>
                            <ul class="small text-muted mb-0 ps-3">
                                <li>Pengeluaran Biaya Operasional</li>
                                <li>Pengeluaran Biaya Gaji</li>
                                <li>Pengeluaran Biaya Pemeliharaan</li>
                                <li>Pengeluaran Biaya Lainnya</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card table-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-bar-chart-2 me-2 text-primary"></i>Statistik
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted small">Total Kategori</span>
                            <span class="badge badge-primary-light">{{ \App\Models\Kategori::count() }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small">Kategori Aktif</span>
                            <span class="badge badge-success-light">{{ \App\Models\Kategori::count() }}</span>
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

.form-control.is-invalid {
    border-color: var(--danger-color);
}

.form-control.is-invalid:focus {
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

/* ============================================
   INFO CARD CUSTOM
   ============================================ */
.info-card-custom {
    background-color: var(--bg-soft);
    border-radius: 0.75rem;
    padding: 1rem;
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

    .form-control {
        font-size: 0.8125rem;
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

    // Auto focus pada input pertama
    const firstInput = document.getElementById('nama_kategori');
    if (firstInput) {
        firstInput.focus();
    }

    // Form validation
    const form = document.getElementById('kategoriForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const namaKategori = document.getElementById('nama_kategori').value.trim();
            
            if (namaKategori.length < 3) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Nama kategori minimal 3 karakter',
                    confirmButtonColor: '#3454D1'
                });
                return false;
            }
        });
    }
});

function refreshPage() {
    location.reload();
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