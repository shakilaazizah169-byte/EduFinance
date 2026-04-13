@extends('layouts.app')

@section('title', 'Buat Invoice')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Buat Invoice</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">Invoice</a></li>
                <li class="breadcrumb-item active">Buat Invoice</li>
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

        @php
            $kotaSetting    = $setting->getRawOriginal('kota') ?? '';
            $namaKepala     = $setting->getRawOriginal('nama_kepala_sekolah') ?? '';
            $nipKepala      = $setting->getRawOriginal('nip_kepala_sekolah') ?? '';
        @endphp

        <form action="{{ route('invoice.store') }}" method="POST" id="invoiceForm">
            @csrf
            <div class="row g-4">
                {{-- KIRI (8 col) --}}
                <div class="col-lg-8">
                    {{-- INFO INVOICE --}}
                    <div class="card table-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-file-text me-2 text-primary"></i>Informasi Invoice
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        No. Invoice <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="invoice_number"
                                           class="form-control @error('invoice_number') is-invalid @enderror"
                                           value="{{ old('invoice_number', 'INV-' . date('Ymd') . '-' . str_pad(rand(1,999),3,'0',STR_PAD_LEFT)) }}"
                                           placeholder="INV-20240101-001" required>
                                    @error('invoice_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Tanggal Invoice <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="invoice_date"
                                           class="form-control @error('invoice_date') is-invalid @enderror"
                                           value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                    @error('invoice_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Info Setting --}}
                            <div class="alert alert-info-custom mt-3 mb-0">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="feather-info fs-5 text-primary mt-1"></i>
                                    <div class="small">
                                        <strong>Data berikut otomatis dari Setting:</strong><br>
                                        Kota: <strong>{{ $kotaSetting ?: '-' }}</strong> &bull;
                                        Nama TTD: <strong>{{ $namaKepala ?: '-' }}</strong>
                                        @if($nipKepala) &bull; NIP: <strong>{{ $nipKepala }}</strong> @endif
                                        <br>
                                        <a href="{{ route('school.settings') }}" target="_blank" class="text-primary">
                                            <i class="feather-settings me-1"></i>Ubah di Setting
                                        </a>
                                    </div>
                                </div>
                            </div>
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
                                    <label class="form-label">
                                        Nama Perusahaan / Instansi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="bill_to_nama"
                                           class="form-control @error('bill_to_nama') is-invalid @enderror"
                                           value="{{ old('bill_to_nama') }}"
                                           placeholder="PT. Nama Perusahaan / Instansi" required>
                                    @error('bill_to_nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="bill_to_alamat" class="form-control" rows="3"
                                              placeholder="Jl. Nama Jalan No. XX&#10;RT/RW, Kelurahan, Kecamatan&#10;Kota, Provinsi, Kode Pos">{{ old('bill_to_alamat') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" name="bill_to_telepon" class="form-control"
                                           value="{{ old('bill_to_telepon') }}" placeholder="0812xxxxxxxx">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="bill_to_email" class="form-control"
                                           value="{{ old('bill_to_email') }}" placeholder="email@perusahaan.com">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TABEL ITEM --}}
                    <div class="card table-card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="feather-list me-2 text-primary"></i>Detail Item
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="tambahBaris()">
                                <i class="feather-plus me-1"></i>Tambah Baris
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="tabelItem">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:42%">Deskripsi</th>
                                            <th style="width:12%" class="text-center">Qty</th>
                                            <th style="width:20%" class="text-end">Unit Cost (Rp)</th>
                                            <th style="width:18%" class="text-end">Amount (Rp)</th>
                                            <th style="width:8%" class="text-center">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemBody">
                                        <tr class="item-row">
                                            <td><input type="text" name="items[0][description]" class="form-control form-control-sm border-0" placeholder="Nama produk / jasa..." required></td>
                                            <td><input type="number" name="items[0][qty]" class="form-control form-control-sm border-0 text-center qty-input" min="1" value="1" required></td>
                                            <td><input type="number" name="items[0][unit_cost]" class="form-control form-control-sm border-0 text-end unit-cost-input" min="0" value="0" step="1000" required></td>
                                            <td><input type="text" name="items[0][amount]" class="form-control form-control-sm border-0 text-end amount-display" readonly value="0"></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusBaris(this)"><i class="feather-trash-2"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- CATATAN --}}
                    <div class="card table-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-message-square me-2 text-primary"></i>Catatan & Informasi Pembayaran
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Informasi Rekening / Pembayaran</label>
                                    <textarea name="catatan_bank" class="form-control" rows="2"
                                              placeholder="Make all checks payable to BRI Syariah&#10;No. Rekening: 4204204201 An. PT Hijau Solusi Utama">{{ old('catatan_bank') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Pesan Penutup</label>
                                    <input type="text" name="pesan_penutup" class="form-control"
                                           value="{{ old('pesan_penutup', 'THANK YOU FOR YOUR BUSINESS!') }}"
                                           placeholder="THANK YOU FOR YOUR BUSINESS!">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Catatan Tambahan <span class="text-muted fw-normal">(opsional)</span></label>
                                    <textarea name="catatan_tambahan" class="form-control" rows="2"
                                              placeholder="Catatan lain jika diperlukan...">{{ old('catatan_tambahan') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KANAN (4 col) --}}
                <div class="col-lg-4">
                    <div class="card table-card mb-4 sticky-top" style="top: 80px;">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="feather-calculator me-2 text-primary"></i>Ringkasan Harga
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Subtotal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="subtotalDisplay" class="form-control text-end" readonly value="0">
                                    <input type="hidden" name="subtotal" id="subtotalInput" value="0">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tax Rate (%)</label>
                                <div class="input-group">
                                    <input type="number" name="tax_rate" id="taxRate"
                                           class="form-control text-end" min="0" max="100" step="0.1" value="{{ old('tax_rate', 0) }}">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sales Tax</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="salesTaxDisplay" class="form-control text-end" readonly value="0">
                                    <input type="hidden" name="sales_tax" id="salesTaxInput" value="0">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Other</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="other" id="otherInput"
                                           class="form-control text-end" min="0" value="{{ old('other', 0) }}" step="1000">
                                </div>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label fw-bold">TOTAL</label>
                                <div class="input-group">
                                    <span class="input-group-text fw-bold">Rp</span>
                                    <input type="text" id="totalDisplay" class="form-control text-end fw-bold" readonly value="0">
                                    <input type="hidden" name="total" id="totalInput" value="0">
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Terbilang</label>
                                <textarea id="terbilangDisplay" name="terbilang" class="form-control"
                                          rows="2" readonly style="font-style:italic; font-size:.85rem;"></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-grid gap-2">
                                <button type="submit" name="action" value="save" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>Simpan Invoice
                                </button>
                                <button type="submit" name="action" value="save_print" class="btn btn-success">
                                    <i class="feather-printer me-2"></i>Simpan & Cetak PDF
                                </button>
                                <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary">
                                    <i class="feather-x me-2"></i>Batal
                                </a>
                            </div>
                        </div>
                    </div>
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
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(52, 84, 209, 0.1);
    outline: none;
}

.form-control-sm {
    border-radius: 0.5rem;
    padding: 0.375rem 0.625rem;
}

.input-group-text {
    background-color: var(--bg-soft);
    border: 1px solid #e2e8f0;
    border-radius: 0.625rem;
    font-size: 0.875rem;
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

.btn-success {
    background: linear-gradient(135deg, var(--success-color) 0%, #1a7a02 100%);
    border: none;
    border-radius: 0.625rem;
    padding: 0.625rem 1.25rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 176, 3, 0.25);
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
    border-radius: 0.5rem;
    border-color: #e2e8f0;
    color: var(--primary-color);
    transition: all 0.2s ease;
}

.btn-outline-primary:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    transform: translateY(-1px);
}

.btn-outline-danger {
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.btn-outline-danger:hover {
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
   TABLE STYLES
   ============================================ */
.table {
    margin-bottom: 0;
}

.table-bordered {
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    overflow: hidden;
}

.table-light th {
    background-color: var(--bg-soft);
    font-weight: 600;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 0.75rem;
    border-bottom: 1px solid var(--border-color);
}

.table td {
    padding: 0.5rem;
    vertical-align: middle;
    border-color: var(--border-color);
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

/* ============================================
   RESPONSIVE
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

    .table td {
        padding: 0.375rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let rowIndex = 1;

function fmt(n) {
    return new Intl.NumberFormat('id-ID').format(Math.round(n));
}

const satuan  = ['','satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan',
                 'sepuluh','sebelas','dua belas','tiga belas','empat belas','lima belas',
                 'enam belas','tujuh belas','delapan belas','sembilan belas'];
const puluhan = ['','','dua puluh','tiga puluh','empat puluh','lima puluh',
                 'enam puluh','tujuh puluh','delapan puluh','sembilan puluh'];

function terbilangRatusan(n) {
    if (n < 20)  return satuan[n];
    if (n < 100) return puluhan[Math.floor(n/10)] + (n%10 ? ' ' + satuan[n%10] : '');
    if (n < 200) return 'seratus' + (n%100 ? ' ' + terbilangRatusan(n%100) : '');
    return satuan[Math.floor(n/100)] + ' ratus' + (n%100 ? ' ' + terbilangRatusan(n%100) : '');
}

function terbilang(n) {
    n = Math.round(n);
    if (n === 0) return 'nol';
    let r = '';
    if (n >= 1000000000) { r += terbilangRatusan(Math.floor(n/1000000000)) + ' milyar '; n %= 1000000000; }
    if (n >= 1000000)    { r += terbilangRatusan(Math.floor(n/1000000))    + ' juta ';   n %= 1000000; }
    if (n >= 1000)       { r += (Math.floor(n/1000)===1?'seribu ':terbilangRatusan(Math.floor(n/1000))+' ribu '); n %= 1000; }
    if (n > 0)             r += terbilangRatusan(n);
    return r.trim();
}

function hitungSemua() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty      = parseFloat(row.querySelector('.qty-input').value)       || 0;
        const unitCost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
        const amount   = qty * unitCost;
        subtotal      += amount;
        row.querySelector('.amount-display').value = fmt(amount);
    });
    const taxRate  = parseFloat(document.getElementById('taxRate').value)   || 0;
    const other    = parseFloat(document.getElementById('otherInput').value) || 0;
    const salesTax = subtotal * taxRate / 100;
    const total    = subtotal + salesTax + other;

    document.getElementById('subtotalDisplay').value = fmt(subtotal);
    document.getElementById('subtotalInput').value   = Math.round(subtotal);
    document.getElementById('salesTaxDisplay').value = fmt(salesTax);
    document.getElementById('salesTaxInput').value   = Math.round(salesTax);
    document.getElementById('totalDisplay').value    = fmt(total);
    document.getElementById('totalInput').value      = Math.round(total);

    const tb = terbilang(total);
    document.getElementById('terbilangDisplay').value =
        '"' + tb.charAt(0).toUpperCase() + tb.slice(1) + ' Rupiah"';
}

function tambahBaris() {
    const tbody = document.getElementById('itemBody');
    const tr    = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td><input type="text" name="items[${rowIndex}][description]" class="form-control form-control-sm border-0" placeholder="Nama produk / jasa..." required></td>
        <td><input type="number" name="items[${rowIndex}][qty]" class="form-control form-control-sm border-0 text-center qty-input" min="1" value="1" required></td>
        <td><input type="number" name="items[${rowIndex}][unit_cost]" class="form-control form-control-sm border-0 text-end unit-cost-input" min="0" value="0" step="1000" required></td>
        <td><input type="text" name="items[${rowIndex}][amount]" class="form-control form-control-sm border-0 text-end amount-display" readonly value="0"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusBaris(this)"><i class="feather-trash-2"></i></button></td>
    `;
    tbody.appendChild(tr);
    rowIndex++;
    attachListeners(tr);
}

function hapusBaris(btn) {
    if (document.querySelectorAll('.item-row').length <= 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Minimal harus ada 1 baris item.',
            confirmButtonColor: '#3454D1'
        });
        return;
    }
    btn.closest('tr').remove();
    hitungSemua();
}

function attachListeners(row) {
    row.querySelector('.qty-input').addEventListener('input', hitungSemua);
    row.querySelector('.unit-cost-input').addEventListener('input', hitungSemua);
}

function refreshPage() {
    location.reload();
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    document.querySelectorAll('.item-row').forEach(attachListeners);
    document.getElementById('taxRate').addEventListener('input', hitungSemua);
    document.getElementById('otherInput').addEventListener('input', hitungSemua);
    hitungSemua();
});
</script>
@endpush