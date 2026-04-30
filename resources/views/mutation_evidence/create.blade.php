@extends('layouts.app')

@section('title', 'Tambah Bukti Mutasi')

@section('content')
<div class="nxl-content">

    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Tambah Bukti Mutasi</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mutation-evidence.index') }}">Bukti Mutasi</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="feather-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('mutation-evidence.store') }}" method="POST"
            enctype="multipart/form-data" id="evidenceForm">
            @csrf

            <div class="row g-4">
                {{-- ── Kolom Kiri ──────────────────────────────── --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="feather-file-text me-2"></i>Informasi Bukti</h5>
                        </div>
                        <div class="card-body">

                            {{-- Mutasi Terkait --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Mutasi Terkait <span class="text-danger">*</span>
                                </label>
                                <select name="mutation_id" id="mutationSelect" class="form-select @error('mutation_id') is-invalid @enderror" required>
                                    <option value="">— Pilih Mutasi —</option>
                                    @foreach($mutasiList as $m)
                                    <option value="{{ $m->mutasi_id }}"
                                        data-amount="{{ $m->debit > 0 ? $m->debit : $m->kredit }}"
                                        {{ (old('mutation_id', $selectedMutasi?->mutasi_id) == $m->mutasi_id) ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse($m->tanggal)->format('d/m/Y') }}
                                        — {{ $m->uraian }}
                                        ({{ $m->kodeTransaksi->kode ?? '-' }})
                                        — Rp {{ number_format($m->debit > 0 ? $m->debit : $m->kredit, 0, ',', '.') }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('mutation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3">
                                {{-- Tanggal Bukti --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Bukti</label>
                                    <input type="date" name="evidence_date" id="evidenceDate"
                                        class="form-control @error('evidence_date') is-invalid @enderror"
                                        value="{{ old('evidence_date', now()->format('Y-m-d')) }}">
                                    <div class="form-text">Bisa diisi belakangan</div>
                                    @error('evidence_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Jenis Bukti --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Jenis Bukti <span class="text-danger">*</span>
                                    </label>
                                    <select name="evidence_type" class="form-select @error('evidence_type') is-invalid @enderror" required>
                                        <option value="">— Pilih Jenis —</option>
                                        @foreach($typeLabels as $val => $label)
                                        <option value="{{ $val }}" {{ old('evidence_type') == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('evidence_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Nomor Bukti --}}
                            <div class="mt-3">
                                <label class="form-label fw-semibold">
                                    Nomor Bukti <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" name="evidence_number" id="evidenceNumber"
                                        class="form-control @error('evidence_number') is-invalid @enderror"
                                        value="{{ old('evidence_number', $autoNumber) }}"
                                        placeholder="BKT/YYYYMMDD/0001"
                                        required>
                                    <button type="button" class="btn btn-light-brand" id="regenerateBtn"
                                        data-bs-toggle="tooltip" title="Generate ulang nomor">
                                        <i class="feather-refresh-cw"></i>
                                    </button>
                                </div>
                                <div class="form-text">Format: BKT/YYYYMMDD/XXXX — bisa diedit manual</div>
                                @error('evidence_number')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Judul Bukti --}}
                            <div class="mt-3">
                                <label class="form-label fw-semibold">
                                    Judul / Deskripsi Bukti <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="evidence_title"
                                    class="form-control @error('evidence_title') is-invalid @enderror"
                                    value="{{ old('evidence_title') }}"
                                    placeholder="cth: Struk pembelian ATK, Kwitansi bayar listrik..."
                                    required>
                                @error('evidence_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nominal --}}
                            <div class="mt-3">
                                <label class="form-label fw-semibold">Nominal Bukti (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="evidence_amount" id="evidenceAmount"
                                        class="form-control @error('evidence_amount') is-invalid @enderror"
                                        value="{{ old('evidence_amount') }}"
                                        placeholder="0"
                                        inputmode="numeric">
                                </div>
                                @error('evidence_amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Catatan --}}
                            <div class="mt-3">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="notes" class="form-control" rows="3"
                                    placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                            </div>

                        </div>{{-- card-body --}}
                    </div>{{-- card --}}
                </div>{{-- col-lg-8 --}}

                {{-- ── Kolom Kanan ─────────────────────────────── --}}
                <div class="col-lg-4">

                    {{-- Upload File --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="feather-upload-cloud me-2"></i>File Bukti</h5>
                        </div>
                        <div class="card-body">
                            <div class="upload-area" id="uploadArea">
                                <i class="feather-image fs-40 text-muted mb-2"></i>
                                <p class="mb-1 fw-medium">Seret &amp; lepas file di sini</p>
                                <p class="text-muted small mb-3">JPG, PNG, atau PDF (maks 5 MB)</p>
                                <label for="evidenceFile" class="btn btn-light-brand btn-sm">
                                    <i class="feather-folder me-1"></i>Pilih File
                                </label>
                                <input type="file" id="evidenceFile" name="evidence_file"
                                    accept=".jpg,.jpeg,.png,.pdf" class="d-none">
                            </div>
                            <div id="filePreview" class="mt-3 d-none">
                                <div class="d-flex align-items-center gap-2 p-2 border rounded">
                                    <i class="feather-file text-primary fs-20"></i>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="fw-medium text-truncate" id="fileName"></div>
                                        <small class="text-muted" id="fileSize"></small>
                                    </div>
                                    <button type="button" class="btn btn-xs btn-icon btn-light-danger"
                                        id="removeFile">
                                        <i class="feather-x"></i>
                                    </button>
                                </div>
                                <div id="imgPreviewWrapper" class="mt-2 text-center d-none">
                                    <img id="imgPreview" class="img-fluid rounded" style="max-height:200px">
                                </div>
                            </div>
                            @error('evidence_file')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Info Mutasi --}}
                    <div class="card" id="mutasiInfoCard" style="{{ $selectedMutasi ? '' : 'display:none' }}">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="feather-info me-1"></i>Info Mutasi Terpilih</h6>
                        </div>
                        <div class="card-body p-3">
                            @if($selectedMutasi)
                            <dl class="row mb-0 small">
                                <dt class="col-5">Tanggal</dt>
                                <dd class="col-7">{{ \Carbon\Carbon::parse($selectedMutasi->tanggal)->format('d/m/Y') }}</dd>
                                <dt class="col-5">Uraian</dt>
                                <dd class="col-7">{{ $selectedMutasi->uraian }}</dd>
                                <dt class="col-5">Kode</dt>
                                <dd class="col-7">{{ $selectedMutasi->kodeTransaksi->kode ?? '-' }}</dd>
                                <dt class="col-5">Debit</dt>
                                <dd class="col-7 text-success">Rp {{ number_format($selectedMutasi->debit, 0, ',', '.') }}</dd>
                                <dt class="col-5">Kredit</dt>
                                <dd class="col-7 text-danger">Rp {{ number_format($selectedMutasi->kredit, 0, ',', '.') }}</dd>
                            </dl>
                            @endif
                        </div>
                    </div>

                </div>{{-- col-lg-4 --}}
            </div>{{-- row --}}

            {{-- Action Buttons --}}
            <div class="card mt-4">
                <div class="card-body d-flex align-items-center gap-3">
                    <button type="submit" name="action" value="save" class="btn btn-primary">
                        <i class="feather-save me-1"></i>Simpan Bukti
                    </button>
                    <button type="submit" name="action" value="save_add" class="btn btn-light-brand">
                        <i class="feather-plus me-1"></i>Simpan &amp; Tambah Lagi
                    </button>
                    <a href="{{ route('mutation-evidence.index') }}" class="btn btn-light">
                        <i class="feather-arrow-left me-1"></i>Batal
                    </a>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 10px;
        padding: 30px 20px;
        text-align: center;
        transition: border-color .2s, background .2s;
        cursor: pointer;
    }

    .upload-area.dragover {
        border-color: #3454d1;
        background: #f0f4ff;
    }
</style>
@endpush

@push('scripts')
<script>
    // ── Auto-format nominal ────────────────────────────────────
    const amountInput = document.getElementById('evidenceAmount');
    amountInput.addEventListener('input', function() {
        let raw = this.value.replace(/\D/g, '');
        this.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
    });

    // ── Regenerate nomor bukti via AJAX ───────────────────────
    document.getElementById('regenerateBtn').addEventListener('click', function() {
        const date = document.getElementById('evidenceDate').value || '';
        fetch(`{{ route('mutation-evidence.generate-number') }}?date=${date}`)
            .then(r => r.json())
            .then(d => {
                document.getElementById('evidenceNumber').value = d.number;
            });
    });

    // Update nomor saat tanggal berubah
    document.getElementById('evidenceDate').addEventListener('change', function() {
        fetch(`{{ route('mutation-evidence.generate-number') }}?date=${this.value}`)
            .then(r => r.json())
            .then(d => {
                document.getElementById('evidenceNumber').value = d.number;
            });
    });

    // ── File upload preview ────────────────────────────────────
    const fileInput = document.getElementById('evidenceFile');
    const filePreview = document.getElementById('filePreview');
    const fileNameEl = document.getElementById('fileName');
    const fileSizeEl = document.getElementById('fileSize');
    const imgPreview = document.getElementById('imgPreview');
    const imgWrapper = document.getElementById('imgPreviewWrapper');
    const uploadArea = document.getElementById('uploadArea');

    function handleFile(file) {
        if (!file) return;
        fileNameEl.textContent = file.name;
        fileSizeEl.textContent = (file.size / 1024).toFixed(1) + ' KB';
        filePreview.classList.remove('d-none');

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                imgPreview.src = e.target.result;
                imgWrapper.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            imgWrapper.classList.add('d-none');
        }
    }

    fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

    document.getElementById('removeFile').addEventListener('click', () => {
        fileInput.value = '';
        filePreview.classList.add('d-none');
        imgWrapper.classList.add('d-none');
    });

    // Drag & drop
    uploadArea.addEventListener('dragover', e => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
    uploadArea.addEventListener('drop', e => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            handleFile(file);
        }
    });
    uploadArea.addEventListener('click', () => fileInput.click());
</script>
@endpush