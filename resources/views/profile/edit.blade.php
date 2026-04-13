@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Edit Profile</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('profile.details') }}">Profile Details</a></li>
                <li class="breadcrumb-item active">Edit Profile</li>
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
                    <a href="{{ route('profile.details') }}" class="btn btn-outline-secondary">
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- form card -->
                <div class="card table-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-edit-2 me-2"></i>Edit Profile
                        </h5>
                    </div>
                    
                    <div class="card-body p-4">
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
                        
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
                            @csrf
                            
                            <!-- Avatar Upload Section -->
                            <div class="text-center mb-4">
                                <div class="avatar-upload">
                                    <div class="avatar-preview mb-3">
                                        <div id="imagePreview" 
                                             style="background-image: url('{{ $user->getAvatarUrl() }}');
                                                    width: 120px; 
                                                    height: 120px; 
                                                    border-radius: 50%; 
                                                    background-size: cover; 
                                                    background-position: center;
                                                    border: 3px solid var(--primary-color);
                                                    margin: 0 auto;
                                                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                                                    cursor: pointer;">
                                        </div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input type="file" name="avatar" id="avatarUpload" accept=".png,.jpg,.jpeg" class="form-control" style="display: none;">
                                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('avatarUpload').click();">
                                            <i class="feather-camera me-2"></i> Ganti Foto
                                        </button>
                                        <div class="text-muted mt-2 small">
                                            <i class="feather-info me-1"></i>Format: JPG, PNG. Maks: 2MB
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Basic Information -->
                            <h6 class="fw-semibold mb-3">
                                <i class="feather-user me-2 text-primary"></i>Informasi Dasar
                            </h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Change Password -->
                            <h6 class="fw-semibold mb-3">
                                <i class="feather-lock me-2 text-primary"></i>Ubah Password
                            </h6>
                            <div class="alert alert-info-custom mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="feather-info"></i>
                                    <span>Kosongkan jika tidak ingin mengubah password</span>
                                </div>
                            </div>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="current_password" class="form-label">Password Saat Ini</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="new_password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                           id="new_password" name="new_password">
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control" 
                                           id="new_password_confirmation" name="new_password_confirmation">
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Form Actions -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div class="pagination-info">
                                    <i class="feather-info me-1"></i>
                                    Pastikan data yang diisi sudah benar
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('profile.details') }}" class="btn btn-outline-secondary">
                                        <i class="feather-x me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i>Simpan Perubahan
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
    padding: 0.625rem 1.25rem;
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
    padding: 0.5rem 1rem;
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

.alert-info-custom {
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.08) 0%, rgba(23, 162, 184, 0.05) 100%);
    border: 1px solid rgba(23, 162, 184, 0.15);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
}

.alert-success-custom .btn-close,
.alert-danger-custom .btn-close {
    font-size: 0.75rem;
}

/* ============================================
   AVATAR UPLOAD STYLES
   ============================================ */
.avatar-upload {
    position: relative;
}

.avatar-edit {
    text-align: center;
}

.avatar-preview {
    transition: all 0.3s ease;
}

.avatar-preview:hover {
    opacity: 0.9;
}

#imagePreview {
    transition: all 0.3s ease;
}

#imagePreview:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
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
   PAGINATION INFO
   ============================================ */
.pagination-info {
    font-size: 0.75rem;
    color: #6c757d;
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

    .btn-primary, .btn-outline-secondary {
        padding: 0.5rem 1rem;
    }
}

@media (max-width: 576px) {
    .d-flex.flex-wrap.justify-content-between {
        flex-direction: column;
        align-items: stretch !important;
    }

    .d-flex.flex-wrap.justify-content-between .pagination-info {
        text-align: center;
        margin-bottom: 1rem;
    }

    .d-flex.flex-wrap.justify-content-between .d-flex {
        justify-content: center;
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

// Preview avatar sebelum upload
document.getElementById('avatarUpload').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').style.backgroundImage = 'url(' + e.target.result + ')';
        }
        reader.readAsDataURL(this.files[0]);
    }
});

// Validasi file size
document.getElementById('avatarUpload').addEventListener('change', function(e) {
    const file = this.files[0];
    if (file) {
        const fileSize = file.size / 1024 / 1024; // dalam MB
        if (fileSize > 2) {
            Swal.fire({
                icon: 'warning',
                title: 'Ukuran File Terlalu Besar',
                text: 'Ukuran file maksimal 2MB!',
                confirmButtonColor: '#3454D1'
            });
            this.value = ''; // Reset input
            // Kembalikan ke avatar sebelumnya
            document.getElementById('imagePreview').style.backgroundImage = 'url({{ $user->getAvatarUrl() }})';
        }
    }
});

function refreshPage() {
    location.reload();
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