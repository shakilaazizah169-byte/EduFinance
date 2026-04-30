@extends('layouts.app')

@section('title', 'Bukti Mutasi')

@section('content')
<div class="nxl-content">
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Bukti Mutasi</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Bukti Mutasi</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('mutation-evidence.print') }}?{{ http_build_query(request()->only('start_date','end_date','evidence_type','mutation_id')) }}"
                    target="_blank" class="btn btn-light-brand btn-icon" data-bs-toggle="tooltip" title="Cetak Bukti">
                    <i class="feather-printer"></i>
                </a>
                <a href="{{ route('mutation-evidence.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-1"></i>Tambah Bukti
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">

        {{-- Alert --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="feather-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Filter Card --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="feather-filter me-2"></i>Filter &amp; Pencarian</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('mutation-evidence.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Cari</label>
                            <input type="text" name="search" class="form-control"
                                placeholder="Nomor bukti / judul..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Jenis Bukti</label>
                            <select name="evidence_type" class="form-select">
                                <option value="">Semua</option>
                                @foreach($typeLabels as $val => $label)
                                <option value="{{ $val }}" {{ request('evidence_type') == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-search me-1"></i>Filter
                            </button>
                            <a href="{{ route('mutation-evidence.index') }}" class="btn btn-light">
                                <i class="feather-x me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="feather-file-text me-2"></i>Daftar Bukti Mutasi
                </h5>
                <span class="badge bg-primary-subtle text-primary fs-12">
                    Total: <strong>Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No</th>
                                <th>Tanggal Bukti</th>
                                <th>Nomor Bukti</th>
                                <th>Jenis</th>
                                <th>Judul</th>
                                <th class="text-end">Nominal</th>
                                <th>Mutasi Terkait</th>
                                <th>File</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($evidences as $i => $ev)
                            <tr>
                                <td class="ps-3 text-muted">{{ $evidences->firstItem() + $i }}</td>
                                <td>
                                    {{ $ev->evidence_date
                                        ? \Carbon\Carbon::parse($ev->evidence_date)->format('d/m/Y')
                                        : '<span class="text-muted fst-italic">Belum diisi</span>' }}
                                </td>
                                <td>
                                    <code class="text-primary">{{ $ev->evidence_number }}</code>
                                </td>
                                <td>
                                    <span class="badge {{ $ev->type_badge }}">
                                        {{ $ev->type_label }}
                                    </span>
                                </td>
                                <td>{{ $ev->evidence_title }}</td>
                                <td class="text-end fw-semibold">
                                    Rp {{ number_format($ev->evidence_amount, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($ev->mutasiKas)
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($ev->mutasiKas->tanggal)->format('d/m/Y') }}
                                    </small><br>
                                    <span class="fw-medium">{{ $ev->mutasiKas->uraian }}</span>
                                    @else
                                    <span class="text-danger">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($ev->evidence_file)
                                    <a href="{{ Storage::url($ev->evidence_file) }}"
                                        target="_blank"
                                        class="btn btn-xs btn-icon btn-light-brand"
                                        data-bs-toggle="tooltip" title="Lihat File">
                                        <i class="feather-paperclip"></i>
                                    </a>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('mutation-evidence.show', $ev) }}"
                                            class="btn btn-xs btn-icon btn-light-brand"
                                            data-bs-toggle="tooltip" title="Detail">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('mutation-evidence.edit', $ev) }}"
                                            class="btn btn-xs btn-icon btn-light-brand"
                                            data-bs-toggle="tooltip" title="Edit">
                                            <i class="feather-edit"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-xs btn-icon btn-light-danger btn-delete"
                                            data-id="{{ $ev->id }}"
                                            data-title="{{ $ev->evidence_title }}"
                                            data-bs-toggle="tooltip" title="Hapus">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                        <form id="delete-form-{{ $ev->id }}"
                                            action="{{ route('mutation-evidence.destroy', $ev) }}"
                                            method="POST" class="d-none">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="feather-inbox fs-40 mb-2 d-block"></i>
                                    Belum ada bukti mutasi
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($evidences->hasPages())
                <div class="card-footer">
                    {{ $evidences->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>

    </div>{{-- end main-content --}}
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            Swal.fire({
                title: 'Hapus Bukti?',
                html: `Bukti <strong>${title}</strong> akan dihapus secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    });
</script>
@endpush