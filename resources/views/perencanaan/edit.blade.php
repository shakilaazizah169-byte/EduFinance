@extends('layouts.app')

@section('title', 'Edit Perencanaan')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Edit Perencanaan</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('perencanaan.index') }}">Perencanaan</a></li>
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
                    <span class="badge badge-info-light">
                        <i class="feather-clock me-1"></i>Terakhir diupdate: {{ $perencanaan->updated_at->format('d/m/Y H:i') }}
                    </span>
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
        <div class="row g-4">
            <div class="col-lg-12">
                <!-- form card -->
                <div class="card table-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-edit-2 me-2 text-primary"></i>Form Edit Perencanaan
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

                        <form action="{{ route('perencanaan.update', $perencanaan) }}" method="POST" id="perencanaanForm">
                            @csrf
                            @method('PUT')

                            <!-- informasi perencanaan -->
                            <h6 class="fw-semibold mb-3">
                                <i class="feather-calendar me-2 text-primary"></i>Informasi Perencanaan
                            </h6>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="judul" class="form-label">
                                        Judul Perencanaan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('judul') is-invalid @enderror" 
                                           id="judul" 
                                           name="judul" 
                                           value="{{ old('judul', $perencanaan->judul) }}"
                                           placeholder="Contoh: Rencana Kegiatan Bulan Januari"
                                           required>
                                    @error('judul')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label for="bulan" class="form-label">
                                        Bulan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('bulan') is-invalid @enderror" 
                                            id="bulan" 
                                            name="bulan"
                                            required>
                                        <option value="">-- Pilih Bulan --</option>
                                        @foreach($months as $key => $month)
                                            <option value="{{ $key }}" {{ old('bulan', $perencanaan->bulan) == $key ? 'selected' : '' }}>
                                                {{ $month }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bulan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label for="tahun" class="form-label">
                                        Tahun <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('tahun') is-invalid @enderror" 
                                            id="tahun" 
                                            name="tahun"
                                            required>
                                        <option value="">-- Pilih Tahun --</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ old('tahun', $perencanaan->tahun) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tahun')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- detail perencanaan -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-semibold mb-0">
                                    <i class="feather-list me-2 text-primary"></i>Detail Perencanaan
                                </h6>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge badge-primary-light">{{ $perencanaan->details->count() }} Rencana</span>
                                    <button type="button" class="btn btn-sm btn-primary" id="tambahDetail">
                                        <i class="feather-plus me-1"></i>Tambah Detail
                                    </button>
                                </div>
                            </div>

                            <div id="detailContainer" class="detail-container mb-4">
                                @foreach($perencanaan->details as $index => $detail)
                                <div class="detail-item card border mb-3" id="detail-{{ $index }}">
                                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">Rencana #{{ $index + 1 }}</span>
                                        <button type="button" class="btn btn-sm {{ $index > 0 ? 'btn-outline-danger btn-hapus' : 'btn-outline-secondary' }}" 
                                                data-id="{{ $index }}" data-detail-id="{{ $detail->id }}" 
                                                {{ $index == 0 ? 'disabled' : '' }}>
                                            <i class="feather-{{ $index > 0 ? 'trash-2' : 'minus-circle' }} me-1"></i> 
                                            {{ $index > 0 ? 'Hapus' : 'Minimal 1' }}
                                        </button>
                                    </div>
                                    <div class="card-body py-3">
                                        <input type="hidden" name="detail_ids[]" value="{{ $detail->id }}">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Perencanaan <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="perencanaan[]" 
                                                       value="{{ old('perencanaan.' . $index, $detail->perencanaan) }}"
                                                       placeholder="Contoh: Rapat Koordinasi" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Target <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="target[]" 
                                                       value="{{ old('target.' . $index, $detail->target) }}"
                                                       placeholder="Contoh: 20 Orang / 100%" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Deskripsi</label>
                                                <textarea class="form-control" name="deskripsi[]" rows="3"
                                                          placeholder="Deskripsi lengkap perencanaan...">{{ old('deskripsi.' . $index, $detail->deskripsi) }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Pelaksanaan</label>
                                                <textarea class="form-control" name="pelaksanaan[]" rows="3"
                                                          placeholder="Rencana pelaksanaan kegiatan...">{{ old('pelaksanaan.' . $index, $detail->pelaksanaan) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="alert alert-info-custom mb-4">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="feather-info fs-4 text-primary mt-1"></i>
                                    <div>
                                        <strong>Informasi Edit:</strong>
                                        <ul class="mb-0 mt-1 ps-3">
                                            <li>Klik <strong>"Tambah Detail"</strong> untuk menambahkan rencana baru</li>
                                            <li>Hapus detail yang tidak diperlukan menggunakan tombol hapus</li>
                                            <li>Perubahan akan disimpan saat klik <strong>Simpan Perubahan</strong></li>
                                            <li>Kolom dengan tanda <span class="text-danger">*</span> wajib diisi</li>
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
                                    <button type="button" class="btn btn-outline-warning" id="resetBtn">
                                        <i class="feather-refresh-cw me-2"></i>Reset Perubahan
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
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

.btn-outline-warning {
    border-radius: 0.625rem;
    padding: 0.625rem 1rem;
    border-color: #e2e8f0;
    color: #ffc107;
    transition: all 0.2s ease;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: transparent;
    color: #2c3e50;
    transform: translateY(-1px);
}

.btn-outline-danger {
    border-radius: 0.625rem;
    padding: 0.5rem 0.875rem;
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

.btn-sm {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
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

.badge-info-light {
    background-color: rgba(23, 162, 184, 0.08);
    color: var(--info-color);
}

/* ============================================
   DETAIL CONTAINER
   ============================================ */
.detail-container .detail-item {
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    overflow: hidden;
}

.detail-container .detail-item:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.detail-container .card-header {
    background-color: var(--bg-soft);
    border-bottom: 1px solid var(--border-color);
    padding: 0.75rem 1.25rem;
}

.detail-container .card-header .fw-semibold {
    font-size: 0.85rem;
    color: #2c3e50;
}

.detail-container .card-body {
    background-color: #ffffff;
}

/* Animation for new detail */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.detail-item.new-added {
    animation: slideIn 0.3s ease forwards;
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

    .form-control, .form-select {
        font-size: 0.8125rem;
    }

    .detail-container .card-body .row > div {
        margin-bottom: 0.75rem;
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

    let detailCounter = {{ $perencanaan->details->count() }};
    let deletedDetailIds = [];

    function addDetail() {
        const container = document.getElementById('detailContainer');
        const newIndex = detailCounter;
        
        const detailHtml = `
            <div class="detail-item card border mb-3 new-added" id="detail-${newIndex}">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Rencana #${newIndex + 1}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-hapus" data-id="${newIndex}" data-detail-id="">
                        <i class="feather-trash-2 me-1"></i> Hapus
                    </button>
                </div>
                <div class="card-body py-3">
                    <input type="hidden" name="detail_ids[]" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Perencanaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="perencanaan[]" 
                                   placeholder="Contoh: Rapat Koordinasi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Target <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="target[]" 
                                   placeholder="Contoh: 20 Orang / 100%" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi[]" rows="3"
                                      placeholder="Deskripsi lengkap perencanaan..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pelaksanaan</label>
                            <textarea class="form-control" name="pelaksanaan[]" rows="3"
                                      placeholder="Rencana pelaksanaan kegiatan..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', detailHtml);
        detailCounter++;
        updateNumbering();
        
        setTimeout(() => {
            document.querySelectorAll('.new-added').forEach(el => el.classList.remove('new-added'));
        }, 300);
    }

    function deleteDetail(id, detailId) {
        const element = document.getElementById(`detail-${id}`);
        const totalDetails = document.querySelectorAll('.detail-item').length;
        
        if (totalDetails <= 1) {
            Swal.fire({ icon: 'warning', title: 'Tidak Dapat Menghapus', text: 'Minimal harus ada 1 detail perencanaan!', confirmButtonColor: '#3454D1' });
            return;
        }
        
        if (detailId) deletedDetailIds.push(detailId);
        
        element.style.transition = 'all 0.3s ease';
        element.style.opacity = '0';
        element.style.transform = 'translateY(-20px)';
        
        setTimeout(() => {
            element.remove();
            updateNumbering();
            
            const deletedContainer = document.getElementById('deletedDetailsContainer') || (() => {
                const container = document.createElement('div');
                container.id = 'deletedDetailsContainer';
                container.style.display = 'none';
                document.getElementById('perencanaanForm').appendChild(container);
                return container;
            })();
            
            deletedDetailIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_details[]';
                input.value = id;
                deletedContainer.appendChild(input);
            });
        }, 300);
    }

    function updateNumbering() {
        const items = document.querySelectorAll('.detail-item');
        items.forEach((item, index) => {
            item.id = `detail-${index}`;
            const titleSpan = item.querySelector('.card-header .fw-semibold');
            if (titleSpan) titleSpan.textContent = `Rencana #${index + 1}`;
            
            const deleteBtn = item.querySelector('.btn-hapus');
            const detailId = deleteBtn?.getAttribute('data-detail-id');
            if (deleteBtn) {
                deleteBtn.setAttribute('data-id', index);
                if (index === 0) {
                    deleteBtn.disabled = true;
                    deleteBtn.classList.remove('btn-outline-danger');
                    deleteBtn.classList.add('btn-outline-secondary');
                    deleteBtn.innerHTML = '<i class="feather-minus-circle me-1"></i> Minimal 1';
                } else {
                    deleteBtn.disabled = false;
                    deleteBtn.classList.remove('btn-outline-secondary');
                    deleteBtn.classList.add('btn-outline-danger');
                    deleteBtn.innerHTML = '<i class="feather-trash-2 me-1"></i> Hapus';
                }
            }
        });
        detailCounter = items.length;
        
        const badge = document.querySelector('.badge.badge-primary-light');
        if (badge) badge.textContent = detailCounter + ' Rencana';
    }

    document.getElementById('tambahDetail')?.addEventListener('click', addDetail);
    
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.btn-hapus:not(:disabled)');
        if (deleteBtn) {
            const id = deleteBtn.getAttribute('data-id');
            const detailId = deleteBtn.getAttribute('data-detail-id');
            Swal.fire({
                title: 'Hapus Rencana?',
                text: 'Apakah Anda yakin ingin menghapus rencana ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="feather-trash-2 me-2"></i>Ya, Hapus!',
                cancelButtonText: '<i class="feather-x me-2"></i>Batal',
                reverseButtons: true
            }).then((result) => { if (result.isConfirmed) deleteDetail(id, detailId); });
        }
    });

    const form = document.getElementById('perencanaanForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const perencanaanInputs = document.querySelectorAll('input[name="perencanaan[]"]');
            const targetInputs = document.querySelectorAll('input[name="target[]"]');
            let isValid = true;
            let firstInvalid = null;

            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            perencanaanInputs.forEach(input => {
                if (!input.value.trim()) { input.classList.add('is-invalid'); isValid = false; if (!firstInvalid) firstInvalid = input; }
            });
            targetInputs.forEach(input => {
                if (!input.value.trim()) { input.classList.add('is-invalid'); isValid = false; if (!firstInvalid) firstInvalid = input; }
            });

            if (!isValid) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Semua kolom Perencanaan dan Target wajib diisi!', confirmButtonColor: '#3454D1' });
                if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                const submitBtn = document.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="feather-loader me-2"></i>Menyimpan...';
                submitBtn.disabled = true;
            }
        });
    }

    window.resetForm = function() {
        Swal.fire({
            title: 'Reset Form?',
            text: 'Semua perubahan yang belum disimpan akan hilang. Lanjutkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="feather-refresh-cw me-2"></i>Ya, Reset',
            cancelButtonText: '<i class="feather-x me-2"></i>Batal'
        }).then((result) => { if (result.isConfirmed) location.reload(); });
    };

    document.getElementById('resetBtn')?.addEventListener('click', function(e) { e.preventDefault(); window.resetForm(); });
});

function refreshPage() { location.reload(); }

@if($errors->any())
    Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', html: '<ul class="text-start mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>', confirmButtonText: 'OK' });
@endif
</script>
@endpush