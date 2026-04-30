@extends('layouts.app')

@section('title', 'Edit Bukti Mutasi')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Edit Bukti Mutasi</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mutation-evidence.index') }}">Bukti Mutasi</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <form action="{{ route('mutation-evidence.update', $mutationEvidence) }}"
            method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="feather-edit me-2"></i>Edit Informasi Bukti</h5>
                        </div>
                        <div class="card-body">

                            {{-- Mutasi --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mutasi Terkait <span class="text-danger">*</span></label>
                                <select name="mutation_id" class="form-select @error('mutation_id') is-invalid @enderror" required>
                                    @foreach($mutasiList as $m)
                                    <option value="{{ $m->mutasi_id }}"
                                        {{ old('mutation_id', $mutationEvidence->mutation_id) == $m->mutasi_id ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse($m->tanggal)->format('d/m/Y') }}
                                        — {{ $m->uraian }}
                                        ({{ $m->kodeTransaksi->kode ?? '-' }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('mutation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Bukti</label>
                                    <input type="date" name="evidence_date"
                                        class="form-control @error('evidence_date') is-invalid @enderror"
                                        value="{{ old('evidence_date', $mutationEvidence->evidence_date?->format('Y-m-d')) }}">
                                    @error('evidence_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jenis Bukti <span class="text-danger">*</span></label>
                                    <select name="evidence_type" class="form-select @error('evidence_type') is-invalid @enderror" required>
                                        @foreach($typeLabels as $val => $label)
                                        <option value="{{ $val }}"
                                            {{ old('evidence_type', $mutationEvidence->evidence_type) == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('evidence_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-semibold">Nomor Bukti <span class="text-danger">*</span></label>
                                <input type="text" name="evidence_number"
                                    class="form-control @error('evidence_number') is-invalid @enderror"
                                    value="{{ old('evidence_number', $mutationEvidence->evidence_number) }}" required>
                                @error('evidence_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-semibold">Judul / Deskripsi <span class="text-danger">*</span></label>
                                <input type="text" name="evidence_title"
                                    class="form-control @error('evidence_title') is-invalid @enderror"
                                    value="{{ old('evidence_title', $mutationEvidence->evidence_title) }}" required>
                                @error('evidence_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-semibold">Nominal (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="evidence_amount" id="editAmount"
                                        class="form-control @error('evidence_amount') is-invalid @enderror"
                                        value="{{ old('evidence_amount', number_format($mutationEvidence->evidence_amount, 0, ',', '.')) }}">
                                </div>
                                @error('evidence_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $mutationEvidence->notes) }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="feather-upload-cloud me-2"></i>File Bukti</h5>
                        </div>
                        <div class="card-body">
                            @if($mutationEvidence->evidence_file)
                            <div class="mb-3 p-2 border rounded bg-light">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="feather-file-text text-primary fs-20"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium small text-truncate">
                                            {{ basename($mutationEvidence->evidence_file) }}
                                        </div>
                                        <a href="{{ Storage::url($mutationEvidence->evidence_file) }}"
                                            target="_blank" class="text-primary small">
                                            <i class="feather-external-link me-1"></i>Lihat File
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <label class="form-label">Ganti File (opsional)</label>
                            <input type="file" name="evidence_file"
                                class="form-control @error('evidence_file') is-invalid @enderror"
                                accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text">JPG, PNG, atau PDF (maks 5 MB)</div>
                            @error('evidence_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body d-flex gap-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-1"></i>Simpan Perubahan
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

@push('scripts')
<script>
    document.getElementById('editAmount').addEventListener('input', function() {
        let raw = this.value.replace(/\D/g, '');
        this.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
    });
</script>
@endpush