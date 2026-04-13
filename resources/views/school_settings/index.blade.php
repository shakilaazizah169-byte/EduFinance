@extends('layouts.app')

@section('title', auth()->user()->role == 'super_admin' ? 'Setting' : 'Setting Instansi')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">
                    {{ auth()->user()->role == 'super_admin' ? 'Setting' : 'Setting Instansi' }}
                </h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">
                    {{ auth()->user()->role == 'super_admin' ? 'Setting' : 'Setting Instansi' }}
                </li>
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
            <div class="alert alert-success-custom mb-4">
                <div class="d-flex align-items-center gap-3">
                    <i class="feather-check-circle fs-4 text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

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

        <form action="{{ route('school.settings.update') }}" method="POST" enctype="multipart/form-data" id="settingForm">
            @csrf

            <div class="row g-4">
                <!-- KOLOM KIRI -->
                <div class="col-lg-8">
                    <!-- Identitas Instansi / Sistem -->
                    <div class="card table-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-building me-2 text-primary"></i>
                                {{ auth()->user()->role == 'super_admin' ? 'Identitas Pemilik Sistem' : 'Identitas Instansi' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">
                                        {{ auth()->user()->role == 'super_admin' ? 'Nama Pemilik / Perusahaan' : 'Nama Instansi' }}
                                    </label>
                                    <input type="text" name="nama_sekolah"
                                           class="form-control @error('nama_sekolah') is-invalid @enderror"
                                           value="{{ old('nama_sekolah', $setting->nama_sekolah) }}"
                                           placeholder="{{ auth()->user()->role == 'super_admin' ? 'Contoh: EduFinance / PT. Nama Perusahaan' : 'Contoh: SMA Negeri 1 Depok' }}">
                                    @error('nama_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea name="alamat" class="form-control" rows="2"
                                              placeholder="Jl. Raya No. 1, RT 01/RW 02, Kel. Nama Kelurahan, Kec. Nama Kecamatan">{{ old('alamat', $setting->alamat) }}</textarea>
                                    <div class="form-text">Contoh: Jl. Merdeka No. 5, RT 03/RW 01, Kel. Beji, Kec. Beji</div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Kota / Kabupaten</label>
                                    <input type="text" name="kota" class="form-control"
                                           value="{{ old('kota', $setting->kota) }}" placeholder="Depok">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="text" name="telepon" class="form-control"
                                           value="{{ old('telepon', $setting->telepon) }}" placeholder="(021) 1234567">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">
                                        {{ auth()->user()->role == 'super_admin' ? 'Kode Unik' : 'NPSN' }}
                                    </label>
                                    <input type="text" name="npsn" class="form-control"
                                           value="{{ old('npsn', $setting->npsn) }}" placeholder="{{ auth()->user()->role == 'super_admin' ? '-' : '20000000' }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email', $setting->email) }}" placeholder="info@example.com">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Website <span class="text-muted fw-normal">(opsional)</span></label>
                                    <input type="url" name="website" class="form-control"
                                           value="{{ old('website', $setting->website) }}" placeholder="https://example.com">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Pejabat -->
                    <div class="card table-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-users me-2 text-primary"></i>
                                {{ auth()->user()->role == 'super_admin' ? 'Data Sistem' : 'Data Pejabat Instansi' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ auth()->user()->role == 'super_admin' ? 'Nama Pemilik Sistem' : 'Nama Kepala Instansi' }}
                                    </label>
                                    <input type="text" name="nama_kepala_sekolah" class="form-control"
                                           value="{{ old('nama_kepala_sekolah', $setting->nama_kepala_sekolah) }}"
                                           placeholder="Drs. Nama Lengkap, M.Pd.">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ auth()->user()->role == 'super_admin' ? 'ID Pemilik Sistem' : 'NIP Kepala Instansi' }}
                                    </label>
                                    <input type="text" name="nip_kepala_sekolah" class="form-control"
                                           value="{{ old('nip_kepala_sekolah', $setting->nip_kepala_sekolah) }}"
                                           placeholder="196501011990031001">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama Bendahara / TU / Admin</label>
                                    <input type="text" name="nama_bendahara" class="form-control"
                                           value="{{ old('nama_bendahara', $setting->nama_bendahara) }}"
                                           placeholder="Nama Lengkap, S.E.">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NIP Bendahara / TU / Admin</label>
                                    <input type="text" name="nip_bendahara" class="form-control"
                                           value="{{ old('nip_bendahara', $setting->nip_bendahara) }}"
                                           placeholder="197001011995031001">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tanda Tangan -->
                    <div class="card table-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-edit-3 me-2 text-primary"></i>Tanda Tangan
                                <small class="text-muted fw-normal ms-2">(PNG transparan direkomendasikan)</small>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- TTD Kepala -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ auth()->user()->role == 'super_admin' ? 'TTD Pemilik Sistem' : 'TTD Kepala Instansi' }}
                                    </label>
                                    @if($setting->ttd_kepala)
                                        <div class="signature-preview mb-2">
                                            <div class="position-relative d-inline-block">
                                                <img src="{{ $setting->ttdKepalaUrl() }}" style="max-height:60px;" alt="TTD Kepala">
                                                <button type="button"
                                                        class="btn-delete-signature"
                                                        onclick="deleteFile('ttd_kepala')" title="Hapus">
                                                    <i class="feather-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                    <input type="file" name="ttd_kepala" class="form-control" accept="image/png,image/jpeg">
                                    <div class="form-text">Maks 1MB. Gunakan PNG background transparan.</div>
                                </div>
                                <!-- TTD Bendahara -->
                                <div class="col-md-6">
                                    <label class="form-label">TTD Bendahara / TU / Admin</label>
                                    @if($setting->ttd_bendahara)
                                        <div class="signature-preview mb-2">
                                            <div class="position-relative d-inline-block">
                                                <img src="{{ $setting->ttdBendaharaUrl() }}" style="max-height:60px;" alt="TTD Bendahara">
                                                <button type="button"
                                                        class="btn-delete-signature"
                                                        onclick="deleteFile('ttd_bendahara')" title="Hapus">
                                                    <i class="feather-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                    <input type="file" name="ttd_bendahara" class="form-control" accept="image/png,image/jpeg">
                                    <div class="form-text">Maks 1MB.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN -->
                <div class="col-lg-4">
                    <!-- Logo -->
                    <div class="card table-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-image me-2 text-primary"></i>Logo
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Logo Instansi -->
                            <div class="mb-4">
                                <label class="form-label">Logo Instansi</label>
                                <div class="logo-preview text-center p-3 bg-light rounded mb-2">
                                    @if($setting->logo_sekolah)
                                        <div class="position-relative d-inline-block">
                                            <img src="{{ $setting->logoSekolahUrl() }}"
                                                 id="preview-logo-sekolah"
                                                 style="max-height:90px;max-width:100%;" alt="Logo Instansi">
                                            <button type="button"
                                                    class="btn-delete-logo"
                                                    onclick="deleteFile('logo_sekolah')" title="Hapus logo">
                                                <i class="feather-x"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div id="preview-logo-sekolah" class="empty-logo">
                                            <i class="feather-image fs-2 d-block mb-1"></i>
                                            <small class="text-muted">Belum ada logo</small>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" name="logo_sekolah" class="form-control" accept="image/*"
                                       onchange="previewImage(this, 'preview-logo-sekolah')">
                                <div class="form-text">Maks 2MB · PNG/JPG · tampil di sidebar & PDF</div>
                            </div>

                            <!-- Logo Yayasan -->
                            <div>
                                <label class="form-label">Logo Yayasan <span class="text-muted fw-normal">(opsional)</span></label>
                                <div class="logo-preview text-center p-3 bg-light rounded mb-2">
                                    @if($setting->logo_yayasan)
                                        <div class="position-relative d-inline-block">
                                            <img src="{{ $setting->logoYayasanUrl() }}"
                                                 id="preview-logo-yayasan"
                                                 style="max-height:90px;max-width:100%;" alt="Logo Yayasan">
                                            <button type="button"
                                                    class="btn-delete-logo"
                                                    onclick="deleteFile('logo_yayasan')" title="Hapus logo">
                                                <i class="feather-x"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div id="preview-logo-yayasan" class="empty-logo">
                                            <i class="feather-image fs-2 d-block mb-1"></i>
                                            <small class="text-muted">Belum ada logo</small>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" name="logo_yayasan" class="form-control" accept="image/*"
                                       onchange="previewImage(this, 'preview-logo-yayasan')">
                                <div class="form-text">Muncul di pojok kanan atas PDF</div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="card info-card-custom mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3">
                                <div class="info-icon-custom bg-primary-soft text-primary">
                                    <i class="feather-info"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-semibold mb-2">Informasi Penting</h6>
                                    <div class="small text-muted">
                                        <div class="mb-2">
                                            <i class="feather-file-text text-danger me-1"></i>
                                            <strong>Laporan PDF</strong><br>
                                            Logo, kop surat, tanda tangan otomatis menggunakan data ini.
                                        </div>
                                        <div class="mb-2">
                                            <i class="feather-sidebar text-primary me-1"></i>
                                            <strong>Sidebar</strong><br>
                                            Logo sekolah tampil di sidebar navigasi.
                                        </div>
                                        <div>
                                            <i class="feather-user text-success me-1"></i>
                                            <strong>Foto Profile</strong><br>
                                            Ganti foto profil di menu <a href="{{ route('profile.edit') }}" class="text-primary">Edit Profile</a>.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Simpan -->
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                        <i class="feather-save me-2"></i>
                        {{ auth()->user()->role == 'super_admin' ? 'Simpan Setting' : 'Simpan Setting Instansi' }}
                    </button>
                </div>
            </div>
        </form>
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
.alert-success-custom {
    background: linear-gradient(135deg, rgba(37, 176, 3, 0.08) 0%, rgba(37, 176, 3, 0.05) 100%);
    border: 1px solid rgba(37, 176, 3, 0.15);
    border-radius: 1rem;
    padding: 1rem 1.25rem;
}

.alert-danger-custom {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.08) 0%, rgba(220, 53, 69, 0.05) 100%);
    border: 1px solid rgba(220, 53, 69, 0.15);
    border-radius: 1rem;
    padding: 1rem 1.25rem;
}

.alert-success-custom .btn-close,
.alert-danger-custom .btn-close {
    font-size: 0.75rem;
}

/* ============================================
   LOGO & SIGNATURE PREVIEW STYLES
   ============================================ */
.logo-preview {
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--bg-soft);
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
}

.empty-logo {
    text-align: center;
    color: #9ca3af;
    padding: 1rem;
}

.signature-preview {
    display: flex;
    align-items: center;
    justify-content: flex-start;
}

.btn-delete-logo,
.btn-delete-signature {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background-color: var(--danger-color);
    border: none;
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-delete-logo:hover,
.btn-delete-signature:hover {
    transform: scale(1.1);
    background-color: #c82333;
}

/* ============================================
   INFO CARD CUSTOM
   ============================================ */
.info-card-custom {
    background: linear-gradient(135deg, rgba(52, 84, 209, 0.03) 0%, rgba(30, 58, 138, 0.03) 100%);
    border: 1px solid rgba(52, 84, 209, 0.1);
}

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
   SOFT BACKGROUND COLORS
   ============================================ */
.bg-primary-soft { background-color: rgba(52, 84, 209, 0.1); }
.bg-success-soft { background-color: rgba(37, 176, 3, 0.1); }
.bg-info-soft { background-color: rgba(23, 162, 184, 0.1); }
.bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
.bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }

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

    .btn-primary {
        padding: 0.75rem;
    }
}

@media (max-width: 576px) {
    .logo-preview {
        min-height: 100px;
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
});

function refreshPage() {
    location.reload();
}

// Preview gambar sebelum upload
function previewImage(input, targetId) {
    if (!input.files || !input.files[0]) return;
    const target = document.getElementById(targetId);
    const reader = new FileReader();
    reader.onload = function(e) {
        if (target.tagName === 'IMG') {
            target.src = e.target.result;
        } else {
            target.innerHTML = `<img src="${e.target.result}" style="max-height:90px;max-width:100%;" alt="Preview">`;
        }
    };
    reader.readAsDataURL(input.files[0]);
}

// Hapus file via AJAX
function deleteFile(field) {
    Swal.fire({
        title: 'Hapus File?',
        text: 'Yakin ingin menghapus file ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="feather-trash-2 me-2"></i>Ya, Hapus!',
        cancelButtonText: '<i class="feather-x me-2"></i>Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("school.settings.delete-file") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ field: field })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'File berhasil dihapus',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Gagal menghapus file',
                        confirmButtonColor: '#3454D1'
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Silakan coba lagi',
                    confirmButtonColor: '#3454D1'
                });
            });
        }
    });
}

// SweetAlert Notifications
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