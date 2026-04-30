@extends('layouts.app')

@section('title', 'Detail Bukti Mutasi')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Detail Bukti Mutasi</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mutation-evidence.index') }}">Bukti Mutasi</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="d-flex gap-2">
                <a href="{{ route('mutation-evidence.edit', $mutationEvidence) }}" class="btn btn-light-brand">
                    <i class="feather-edit me-1"></i>Edit
                </a>
                <a href="{{ route('mutation-evidence.print') }}?mutation_id={{ $mutationEvidence->mutation_id }}"
                    target="_blank" class="btn btn-primary">
                    <i class="feather-printer me-1"></i>Cetak
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">
                            <i class="feather-file-text me-2"></i>{{ $mutationEvidence->evidence_title }}
                        </h5>
                        <span class="badge {{ $mutationEvidence->type_badge }} fs-12">
                            {{ $mutationEvidence->type_label }}
                        </span>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Nomor Bukti</dt>
                            <dd class="col-sm-8"><code class="text-primary fs-14">{{ $mutationEvidence->evidence_number }}</code></dd>

                            <dt class="col-sm-4">Tanggal Bukti</dt>
                            <dd class="col-sm-8">
                                {{ $mutationEvidence->evidence_date
                                    ? $mutationEvidence->evidence_date->translatedFormat('d F Y')
                                    : '<span class="text-muted fst-italic">Belum diisi</span>' }}
                            </dd>

                            <dt class="col-sm-4">Jenis Bukti</dt>
                            <dd class="col-sm-8">
                                <span class="badge {{ $mutationEvidence->type_badge }}">
                                    {{ $mutationEvidence->type_label }}
                                </span>
                            </dd>

                            <dt class="col-sm-4">Nominal</dt>
                            <dd class="col-sm-8 fw-bold fs-16 text-primary">
                                Rp {{ number_format($mutationEvidence->evidence_amount, 0, ',', '.') }}
                            </dd>

                            <dt class="col-sm-4">Catatan</dt>
                            <dd class="col-sm-8">{{ $mutationEvidence->notes ?: '—' }}</dd>

                            <dt class="col-sm-4">Dibuat Pada</dt>
                            <dd class="col-sm-8">{{ $mutationEvidence->created_at->translatedFormat('d F Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Mutasi Info --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="feather-link me-2"></i>Mutasi Terkait</h6>
                    </div>
                    <div class="card-body p-3">
                        @if($mutationEvidence->mutasiKas)
                        @php $m = $mutationEvidence->mutasiKas; @endphp
                        <dl class="row mb-0 small">
                            <dt class="col-5">Tanggal</dt>
                            <dd class="col-7">{{ \Carbon\Carbon::parse($m->tanggal)->format('d/m/Y') }}</dd>
                            <dt class="col-5">Kode</dt>
                            <dd class="col-7">{{ $m->kodeTransaksi->kode ?? '—' }}</dd>
                            <dt class="col-5">Uraian</dt>
                            <dd class="col-7">{{ $m->uraian }}</dd>
                            <dt class="col-5">Debit</dt>
                            <dd class="col-7 text-success fw-semibold">Rp {{ number_format($m->debit, 0, ',', '.') }}</dd>
                            <dt class="col-5">Kredit</dt>
                            <dd class="col-7 text-danger fw-semibold">Rp {{ number_format($m->kredit, 0, ',', '.') }}</dd>
                            <dt class="col-5">Saldo</dt>
                            <dd class="col-7 fw-bold">Rp {{ number_format($m->saldo, 0, ',', '.') }}</dd>
                        </dl>
                        @else
                        <p class="text-muted mb-0">Mutasi tidak ditemukan</p>
                        @endif
                    </div>
                </div>

                {{-- File --}}
                @if($mutationEvidence->evidence_file)
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="feather-paperclip me-2"></i>Lampiran</h6>
                    </div>
                    <div class="card-body p-3 text-center">
                        @php $ext = pathinfo($mutationEvidence->evidence_file, PATHINFO_EXTENSION); @endphp
                        @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                        <img src="{{ Storage::url($mutationEvidence->evidence_file) }}"
                            class="img-fluid rounded mb-2" style="max-height:200px">
                        @else
                        <i class="feather-file-text fs-48 text-danger mb-2 d-block"></i>
                        @endif
                        <a href="{{ Storage::url($mutationEvidence->evidence_file) }}"
                            target="_blank" class="btn btn-sm btn-light-brand">
                            <i class="feather-download me-1"></i>Unduh / Lihat
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection