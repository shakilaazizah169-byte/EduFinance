@extends('layouts.app')

@section('title', 'Detail Invoice')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Detail Invoice</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">Invoice</a></li>
                <li class="breadcrumb-item active">Detail</li>
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
                    <a href="{{ route('invoice.print', $invoice->id) }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="Cetak PDF">
                        <i class="feather-printer"></i>
                    </a>
                    <a href="{{ route('invoice.edit', $invoice->id) }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="Edit Invoice">
                        <i class="feather-edit-2"></i>
                    </a>
                    <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary">
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
            {{-- KIRI (8 col) --}}
            <div class="col-lg-8">
                {{-- INFO INVOICE --}}
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-file-text me-2 text-primary"></i>Informasi Invoice
                        </h5>
                        <span class="badge badge-primary-light">
                            <i class="feather-calendar me-1"></i>
                            Dibuat: {{ $invoice->created_at ? $invoice->created_at->format('d M Y H:i') : '-' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">No. Invoice</label>
                                    <div class="info-value">
                                        <strong>{{ $invoice->invoice_number }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">Tanggal Invoice</label>
                                    <div class="info-value">
                                        {{ \Carbon\Carbon::parse($invoice->invoice_date)->translatedFormat('d F Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($setting && ($setting->kota || $setting->nama_kepala_sekolah))
                        <div class="alert alert-info-custom mt-3 mb-0">
                            <div class="d-flex align-items-start gap-2">
                                <i class="feather-info fs-5 text-primary mt-1"></i>
                                <div class="small">
                                    <strong>Informasi Setting:</strong><br>
                                    @if($setting->kota) Kota: <strong>{{ $setting->kota }}</strong><br>@endif
                                    @if($setting->nama_kepala_sekolah) Nama TTD: <strong>{{ $setting->nama_kepala_sekolah }}</strong>@endif
                                    @if($setting->nip_kepala_sekolah) &nbsp;| NIP: <strong>{{ $setting->nip_kepala_sekolah }}</strong>@endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- BILL TO --}}
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-user me-2 text-primary"></i>Tagihan Kepada (Bill To)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="info-group">
                                    <label class="info-label">Nama Perusahaan / Instansi</label>
                                    <div class="info-value fw-semibold">{{ $invoice->bill_to_nama }}</div>
                                </div>
                            </div>
                            @if($invoice->bill_to_alamat)
                            <div class="col-12">
                                <div class="info-group">
                                    <label class="info-label">Alamat</label>
                                    <div class="info-value">{!! nl2br(e($invoice->bill_to_alamat)) !!}</div>
                                </div>
                            </div>
                            @endif
                            @if($invoice->bill_to_telepon || $invoice->bill_to_email)
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">Telepon</label>
                                    <div class="info-value">{{ $invoice->bill_to_telepon ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">Email</label>
                                    <div class="info-value">{{ $invoice->bill_to_email ?: '-' }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- TABEL ITEM --}}
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-list me-2 text-primary"></i>Detail Item
                        </h5>
                        <span class="badge badge-info-light">
                            <i class="feather-package me-1"></i>
                            {{ $invoice->items->count() }} Item
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 45%">DESKRIPSI</th>
                                        <th style="width: 15%" class="text-center">QTY</th>
                                        <th style="width: 20%" class="text-end">UNIT COST (Rp)</th>
                                        <th style="width: 20%" class="text-end">AMOUNT (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->items as $item)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $item->description }}</span>
                                        </td>
                                        <td class="text-center">{{ number_format($item->qty, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-primary">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">SUBTOTAL</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @if($invoice->tax_rate > 0 || $invoice->sales_tax > 0)
                                    <tr>
                                        <td colspan="3" class="text-end">Tax Rate ({{ $invoice->tax_rate }}%)</td>
                                        <td class="text-end">Rp {{ number_format($invoice->sales_tax, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if($invoice->other > 0)
                                    <tr>
                                        <td colspan="3" class="text-end">Other</td>
                                        <td class="text-end">Rp {{ number_format($invoice->other, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="bg-primary-soft">
                                        <td colspan="3" class="text-end fw-bold fs-5">TOTAL</td>
                                        <td class="text-end fw-bold fs-5 text-primary">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- CATATAN --}}
                @if($invoice->catatan_bank || $invoice->pesan_penutup || $invoice->catatan_tambahan)
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-message-square me-2 text-primary"></i>Catatan & Informasi
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($invoice->catatan_bank)
                        <div class="mb-3">
                            <label class="info-label">Informasi Rekening / Pembayaran</label>
                            <div class="info-value bg-light p-3 rounded-3">{!! nl2br(e($invoice->catatan_bank)) !!}</div>
                        </div>
                        @endif
                        @if($invoice->pesan_penutup)
                        <div class="mb-3">
                            <label class="info-label">Pesan Penutup</label>
                            <div class="info-value fst-italic">"{{ $invoice->pesan_penutup }}"</div>
                        </div>
                        @endif
                        @if($invoice->catatan_tambahan)
                        <div>
                            <label class="info-label">Catatan Tambahan</label>
                            <div class="info-value">{!! nl2br(e($invoice->catatan_tambahan)) !!}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- KANAN (4 col) --}}
            <div class="col-lg-4">
                {{-- Ringkasan Harga Card --}}
                <div class="card table-card mb-4 sticky-top" style="top: 80px;">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-calculator me-2 text-primary"></i>Ringkasan Harga
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="summary-item">
                            <span class="summary-label">Subtotal</span>
                            <span class="summary-value">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($invoice->tax_rate > 0)
                        <div class="summary-item">
                            <span class="summary-label">Tax Rate ({{ $invoice->tax_rate }}%)</span>
                            <span class="summary-value">Rp {{ number_format($invoice->sales_tax, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($invoice->other > 0)
                        <div class="summary-item">
                            <span class="summary-label">Other</span>
                            <span class="summary-value">Rp {{ number_format($invoice->other, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <hr>
                        <div class="summary-item total">
                            <span class="summary-label fw-bold">TOTAL</span>
                            <span class="summary-value fw-bold text-primary fs-4">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-3">
                            <label class="info-label">Terbilang</label>
                            <div class="terbilang-box">
                                <i class="feather-book-open me-1"></i>
                                @php
                                    $terbilangText = $invoice->terbilang;
                                    if (empty($terbilangText) && $invoice->total > 0) {
                                        if (!function_exists('terbilangHelper')) {
                                            function terbilangHelper($n) {
                                                $kata = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
                                                if ($n < 12) return $kata[$n];
                                                if ($n < 20) return $kata[$n - 10] . ' belas';
                                                if ($n < 100) return $kata[intval($n/10)] . ' puluh' . ($n%10 ? ' '.$kata[$n%10] : '');
                                                if ($n < 200) return 'seratus' . ($n%100 ? ' '.terbilangHelper($n%100) : '');
                                                if ($n < 1000) return $kata[intval($n/100)] . ' ratus' . ($n%100 ? ' '.terbilangHelper($n%100) : '');
                                                if ($n < 2000) return 'seribu' . ($n%1000 ? ' '.terbilangHelper($n%1000) : '');
                                                if ($n < 1000000) return terbilangHelper(intval($n/1000)) . ' ribu' . ($n%1000 ? ' '.terbilangHelper($n%1000) : '');
                                                if ($n < 1000000000) return terbilangHelper(intval($n/1000000)) . ' juta' . ($n%1000000 ? ' '.terbilangHelper($n%1000000) : '');
                                                return terbilangHelper(intval($n/1000000000)) . ' miliar' . ($n%1000000000 ? ' '.terbilangHelper($n%1000000000) : '');
                                            }
                                        }
                                        $tb = terbilangHelper((int) round($invoice->total));
                                        $terbilangText = '"' . ucfirst($tb) . ' Rupiah"';
                                    }
                                @endphp
                                {{ $terbilangText ?: '—' }}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-2">
                            <a href="{{ route('invoice.print', $invoice->id) }}" class="btn btn-primary">
                                <i class="feather-printer me-2"></i>Cetak PDF
                            </a>
                            <a href="{{ route('invoice.edit', $invoice->id) }}" class="btn btn-outline-primary">
                                <i class="feather-edit-2 me-2"></i>Edit Invoice
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Informasi Tambahan --}}
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-info me-2 text-primary"></i>Informasi Lainnya
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item mb-3">
                            <i class="feather-calendar text-primary me-2"></i>
                            <div>
                                <div class="info-label-small">Dibuat pada</div>
                                <div class="info-value-small">{{ $invoice->created_at ? $invoice->created_at->translatedFormat('d F Y H:i:s') : '-' }}</div>
                            </div>
                        </div>
                        <div class="info-item mb-3">
                            <i class="feather-edit-2 text-primary me-2"></i>
                            <div>
                                <div class="info-label-small">Terakhir diupdate</div>
                                <div class="info-value-small">{{ $invoice->updated_at ? $invoice->updated_at->translatedFormat('d F Y H:i:s') : '-' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="feather-hash text-primary me-2"></i>
                            <div>
                                <div class="info-label-small">Jumlah Item</div>
                                <div class="info-value-small">{{ $invoice->items->count() }} item</div>
                            </div>
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

.card-footer {
    background: transparent;
    border-top: 1px solid var(--border-color);
    padding: 1rem 1.5rem;
}

/* ============================================
   INFO GROUP STYLES
   ============================================ */
.info-group {
    margin-bottom: 0.5rem;
}

.info-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    display: block;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 0.875rem;
    color: #2c3e50;
    word-break: break-word;
}

.info-label-small {
    font-size: 0.65rem;
    font-weight: 500;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.info-value-small {
    font-size: 0.8125rem;
    color: #2c3e50;
    font-weight: 500;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.info-item i {
    margin-top: 0.125rem;
}

/* ============================================
   SUMMARY ITEM STYLES
   ============================================ */
.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
}

.summary-label {
    font-size: 0.8125rem;
    color: #6c757d;
}

.summary-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: #2c3e50;
}

.summary-item.total {
    padding-top: 0.5rem;
}

.summary-item.total .summary-value {
    font-size: 1.25rem;
}

.terbilang-box {
    background-color: var(--bg-soft);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    font-size: 0.8125rem;
    font-style: italic;
    color: #2c3e50;
    border-left: 3px solid var(--primary-color);
}

/* ============================================
   TABLE STYLES
   ============================================ */
.table-card {
    overflow: hidden;
}

.table {
    margin-bottom: 0;
}

.table > thead > tr > th {
    background-color: var(--bg-soft);
    font-weight: 600;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 0.875rem 1rem;
    border-bottom: 1px solid var(--border-color);
}

.table > tbody > tr > td {
    padding: 0.875rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--border-color);
}

.table > tfoot > tr > td {
    padding: 0.75rem 1rem;
    font-weight: 600;
    border-top: 1px solid var(--border-color);
}

.table > tbody > tr:hover {
    background-color: rgba(52, 84, 209, 0.02);
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

.badge-info-light {
    background-color: rgba(23, 162, 184, 0.08);
    color: var(--info-color);
}

.badge-warning-light {
    background-color: rgba(255, 193, 7, 0.08);
    color: #d39e00;
}

/* ============================================
   BUTTONS
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

.btn-outline-primary {
    border-radius: 0.625rem;
    padding: 0.625rem 1.25rem;
    font-weight: 500;
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
.alert-info-custom {
    background: linear-gradient(135deg, rgba(52, 84, 209, 0.05) 0%, rgba(30, 58, 138, 0.05) 100%);
    border: 1px solid rgba(52, 84, 209, 0.15);
    border-radius: 1rem;
    padding: 1rem 1.25rem;
}

/* ============================================
   BACKGROUND SOFT
   ============================================ */
.bg-primary-soft {
    background-color: rgba(52, 84, 209, 0.05);
}

.bg-light {
    background-color: var(--bg-soft) !important;
}

/* ============================================
   STICKY & UTILITY
   ============================================ */
.sticky-top {
    position: sticky;
    top: 80px;
    z-index: 1020;
}

hr {
    border: none;
    border-top: 1px solid var(--border-color);
    margin: 1rem 0;
}

.rounded-3 {
    border-radius: 0.75rem !important;
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

    .table > thead > tr > th,
    .table > tbody > tr > td {
        padding: 0.625rem 0.75rem;
        font-size: 0.8125rem;
    }

    .summary-item.total .summary-value {
        font-size: 1rem;
    }

    .terbilang-box {
        font-size: 0.75rem;
    }

    .info-value {
        font-size: 0.8125rem;
    }

    .btn-icon {
        width: 34px;
        height: 34px;
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