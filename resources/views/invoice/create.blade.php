@extends('layouts.app')

@section('title', 'Buat Invoice')

@section('content')
<div class="nxl-content">
    <!-- page header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10 mb-0">Buat Invoice</h5>
            </div>
            <ul class="breadcrumb mb-0">
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
        <div class="row g-4">
            <!-- kolom kiri - form utama (8 col) -->
            <div class="col-lg-8">
                <div class="card table-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-plus-circle me-2 text-primary"></i>Form Buat Invoice
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

                        @php
                            $kotaSetting    = $setting->getRawOriginal('kota') ?? '';
                            $namaKepala     = $setting->getRawOriginal('nama_kepala_sekolah') ?? '';
                            $nipKepala      = $setting->getRawOriginal('nip_kepala_sekolah') ?? '';
                        @endphp

                        <form action="{{ route('invoice.store') }}" method="POST" id="invoiceForm">
                            @csrf
                            
                            <!-- INFO INVOICE -->
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">
                                    <i class="feather-file-text text-primary me-2"></i>Informasi Invoice
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="invoice_number" class="form-label">
                                            No. Invoice <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="invoice_number"
                                               class="form-control @error('invoice_number') is-invalid @enderror"
                                               value="{{ old('invoice_number', 'INV-' . date('Ymd') . '-' . str_pad(rand(1,999),3,'0',STR_PAD_LEFT)) }}"
                                               placeholder="INV-20240101-001" required>
                                        @error('invoice_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        <div class="form-text">
                                            <i class="feather-info me-1"></i>
                                            Nomor invoice harus unik dan mudah diidentifikasi
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="invoice_date" class="form-label">
                                            Tanggal Invoice <span class="text-danger">*</span>
                                        </label>
                                        <div class="custom-date-input" id="tanggalWrapper">
                                            <input type="text" 
                                                   id="tanggalDisplay"
                                                   class="form-control date-display" 
                                                   placeholder="Pilih tanggal"
                                                   value="{{ old('invoice_date') ? \Carbon\Carbon::parse(old('invoice_date'))->format('d/m/Y') : date('d/m/Y') }}"
                                                   readonly>
                                            <input type="hidden" name="invoice_date" id="tanggalHidden" 
                                                   value="{{ old('invoice_date', date('Y-m-d')) }}">
                                            <i class="feather-calendar calendar-icon"></i>
                                            
                                            <!-- Custom Date Picker -->
                                            <div class="custom-date-picker" id="tanggalPicker">
                                                <div class="date-picker-header">
                                                    <button type="button" class="month-nav" id="prevMonth">
                                                        <i class="feather-chevron-left"></i>
                                                    </button>
                                                    <span class="month-year" id="monthYear">February 2026</span>
                                                    <button type="button" class="month-nav" id="nextMonth">
                                                        <i class="feather-chevron-right"></i>
                                                    </button>
                                                </div>
                                                <div class="date-picker-weekdays">
                                                    <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                                                </div>
                                                <div class="date-picker-days" id="calendarDays"></div>
                                                <div class="date-picker-footer">
                                                    <button type="button" class="btn-clear" id="clearDate">Clear</button>
                                                    <button type="button" class="btn-today" id="todayDate">Today</button>
                                                </div>
                                            </div>
                                        </div>
                                        @error('invoice_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <!-- Info Setting -->
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

                            <!-- BILL TO -->
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">
                                    <i class="feather-user text-primary me-2"></i>Tagihan Kepada (Bill To)
                                </h6>
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="bill_to_nama" class="form-label">
                                            Nama Perusahaan / Instansi <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="bill_to_nama"
                                               class="form-control @error('bill_to_nama') is-invalid @enderror"
                                               value="{{ old('bill_to_nama') }}"
                                               placeholder="PT. Nama Perusahaan / Instansi" required>
                                        @error('bill_to_nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="bill_to_alamat" class="form-label">Alamat</label>
                                        <textarea name="bill_to_alamat" class="form-control" rows="3"
                                                  placeholder="Jl. Nama Jalan No. XX&#10;RT/RW, Kelurahan, Kecamatan&#10;Kota, Provinsi, Kode Pos">{{ old('bill_to_alamat') }}</textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="bill_to_telepon" class="form-label">Telepon</label>
                                        <input type="text" name="bill_to_telepon" class="form-control"
                                               value="{{ old('bill_to_telepon') }}" placeholder="0812xxxxxxxx">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="bill_to_email" class="form-label">Email</label>
                                        <input type="email" name="bill_to_email" class="form-control"
                                               value="{{ old('bill_to_email') }}" placeholder="email@perusahaan.com">
                                    </div>
                                </div>
                            </div>

                            <!-- TABEL ITEM -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-semibold mb-0">
                                        <i class="feather-list text-primary me-2"></i>Detail Item
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="tambahBaris()">
                                        <i class="feather-plus me-1"></i>Tambah Baris
                                    </button>
                                </div>
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

                            <!-- CATATAN -->
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">
                                    <i class="feather-message-square text-primary me-2"></i>Catatan & Informasi Pembayaran
                                </h6>
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="catatan_bank" class="form-label">Informasi Rekening / Pembayaran</label>
                                        <textarea name="catatan_bank" class="form-control" rows="2"
                                                  placeholder="Make all checks payable to BRI Syariah&#10;No. Rekening: 4204204201 An. PT Hijau Solusi Utama">{{ old('catatan_bank') }}</textarea>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="pesan_penutup" class="form-label">Pesan Penutup</label>
                                        <input type="text" name="pesan_penutup" class="form-control"
                                               value="{{ old('pesan_penutup', 'THANK YOU FOR YOUR BUSINESS!') }}"
                                               placeholder="THANK YOU FOR YOUR BUSINESS!">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="catatan_tambahan" class="form-label">Catatan Tambahan <span class="text-muted fw-normal">(opsional)</span></label>
                                        <textarea name="catatan_tambahan" class="form-control" rows="2"
                                                  placeholder="Catatan lain jika diperlukan...">{{ old('catatan_tambahan') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info-custom mb-4">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="feather-lightbulb fs-5 text-primary mt-1"></i>
                                    <div>
                                        <strong>Tips Membuat Invoice:</strong>
                                        <ul class="mb-0 mt-2 ps-3">
                                            <li>Pastikan nomor invoice unik dan berurutan</li>
                                            <li>Isi deskripsi item dengan jelas</li>
                                            <li>Periksa kembali perhitungan sebelum menyimpan</li>
                                            <li>Gunakan "Simpan & Cetak PDF" untuk langsung mencetak</li>
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
                                    <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary">
                                        <i class="feather-x me-2"></i>Batal
                                    </a>
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="feather-refresh-cw me-2"></i>Reset
                                    </button>
                                    <button type="submit" name="action" value="save" class="btn btn-primary">
                                        <i class="feather-save me-2"></i>Simpan Invoice
                                    </button>
                                    <button type="submit" name="action" value="save_print" class="btn btn-success">
                                        <i class="feather-printer me-2"></i>Simpan & Cetak
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- kolom kanan - sidebar (4 col) -->
            <div class="col-lg-4">
                <!-- card ringkasan harga -->
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
                </div>

                <!-- card informasi -->
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-info me-2 text-primary"></i>Informasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="info-card-custom mb-4">
                            <div class="d-flex align-items-start gap-2">
                                <i class="feather-file-text text-primary mt-1"></i>
                                <div>
                                    <strong>Tentang Invoice</strong>
                                    <p class="small text-muted mb-0 mt-1">
                                        Invoice adalah dokumen tagihan yang berisi rincian transaksi penjualan barang atau jasa.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-semibold mb-2 small text-uppercase text-muted">
                                <i class="feather-check-circle me-1"></i>Komponen Invoice:
                            </h6>
                            <ul class="small text-muted mb-0 ps-3">
                                <li><strong>Subtotal</strong> - Total harga semua item</li>
                                <li><strong>Tax Rate</strong> - Persentase pajak yang dikenakan</li>
                                <li><strong>Sales Tax</strong> - Nilai pajak yang dihitung</li>
                                <li><strong>Other</strong> - Biaya tambahan lainnya</li>
                                <li><strong>Total</strong> - Jumlah akhir yang harus dibayar</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- card statistik -->
                <div class="card table-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather-bar-chart-2 me-2 text-primary"></i>Statistik
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted small">Total Invoice</span>
                            <span class="badge badge-primary-light">{{ \App\Models\Invoice::where('user_id', auth()->id())->count() }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small">Invoice Bulan Ini</span>
                            <span class="badge badge-success-light">
                                {{ \App\Models\Invoice::where('user_id', auth()->id())->whereMonth('invoice_date', now()->month)->count() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- pro tips card -->
                <div class="card border-primary">
                    <div class="card-header bg-primary-soft border-primary">
                        <h5 class="mb-0 text-primary">
                            <i class="feather-lightbulb me-2"></i>Pro Tips
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0 ps-3">
                            <li class="mb-2">Gunakan format nomor invoice yang konsisten</li>
                            <li class="mb-2">Cek kembali perhitungan sebelum menyimpan</li>
                            <li class="mb-2">Tambahkan catatan pembayaran untuk memudahkan klien</li>
                            <li>Simpan & Cetak PDF untuk arsip digital</li>
                        </ul>
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

.border-primary {
    border: 1px solid rgba(52, 84, 209, 0.2) !important;
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
   CUSTOM DATE PICKER
   ============================================ */
.custom-date-input {
    position: relative;
    width: 100%;
}

.custom-date-input .date-display {
    background-color: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.625rem;
    padding: 0.625rem 2.5rem 0.625rem 0.875rem;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.custom-date-input .date-display:hover {
    border-color: var(--primary-color);
    background-color: #f8fafc;
}

.custom-date-input .calendar-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
}

.custom-date-input:hover .calendar-icon {
    color: var(--primary-color);
}

.custom-date-picker {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    width: 320px;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    padding: 1rem;
    display: none;
    animation: slideDown 0.2s ease;
}

.custom-date-picker.show {
    display: block;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.date-picker-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.month-nav {
    width: 32px;
    height: 32px;
    border: none;
    background: #f8f9fa;
    border-radius: 0.5rem;
    color: #495057;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.month-nav:hover {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
}

.month-year {
    font-weight: 600;
    font-size: 0.875rem;
    color: #2c3e50;
}

.date-picker-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    margin-bottom: 0.5rem;
}

.date-picker-weekdays span {
    font-size: 0.7rem;
    font-weight: 600;
    color: #9ca3af;
    padding: 0.5rem 0;
}

.date-picker-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}

.date-picker-days button {
    aspect-ratio: 1;
    border: none;
    background: transparent;
    font-size: 0.75rem;
    color: #2c3e50;
    cursor: pointer;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
}

.date-picker-days button:hover:not(.empty) {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
}

.date-picker-days button.today {
    background-color: #e9ecef;
    font-weight: 700;
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.date-picker-days button.selected {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
}

.date-picker-days button.empty {
    pointer-events: none;
    color: #cbd5e0;
}

.date-picker-days button.prev-month,
.date-picker-days button.next-month {
    color: #cbd5e0;
}

.date-picker-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.btn-clear, .btn-today {
    padding: 0.375rem 0.875rem;
    border: none;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-clear {
    background: #f8f9fa;
    color: #6c757d;
}

.btn-clear:hover {
    background: #e9ecef;
    color: var(--danger-color);
}

.btn-today {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
}

.btn-today:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(52, 84, 209, 0.3);
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
   SOFT BACKGROUNDS
   ============================================ */
.bg-primary-soft {
    background-color: rgba(52, 84, 209, 0.08);
}

/* ============================================
   PAGINATION INFO
   ============================================ */
.pagination-info {
    font-size: 0.75rem;
    color: #6c757d;
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

    .table td, .table th {
        padding: 0.5rem;
        white-space: nowrap;
    }
    
    .table input.form-control {
        min-width: 90px;
    }
    
    .table input[name*="description"] {
        min-width: 180px;
    }

    .custom-date-picker {
        width: 280px;
        left: auto;
        right: 0;
    }
}

@media (max-width: 576px) {
    .page-header-title {
        border-right: none !important;
        margin-right: 0 !important;
        padding-right: 0 !important;
    }
    
    .page-header-title h5 {
        margin-bottom: 5px !important;
    }
    
    .table-responsive {
        border-radius: 0;
        margin: 0 -1rem;
        width: calc(100% + 2rem);
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

// ========================================
// CUSTOM DATE PICKER
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    document.querySelectorAll('.item-row').forEach(attachListeners);
    document.getElementById('taxRate').addEventListener('input', hitungSemua);
    document.getElementById('otherInput').addEventListener('input', hitungSemua);
    hitungSemua();

    // Date Picker
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    
    const tanggalDisplay = document.getElementById('tanggalDisplay');
    const tanggalHidden = document.getElementById('tanggalHidden');
    const tanggalPicker = document.getElementById('tanggalPicker');
    const monthYear = document.getElementById('monthYear');
    const calendarDays = document.getElementById('calendarDays');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const clearDateBtn = document.getElementById('clearDate');
    const todayBtn = document.getElementById('todayDate');

    let currentDate = tanggalHidden.value ? new Date(tanggalHidden.value) : new Date();
    let selectedDate = tanggalHidden.value ? new Date(tanggalHidden.value) : new Date();

    function formatDate(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    function formatDateYMD(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${year}-${month}-${day}`;
    }

    function isSameDay(date1, date2) {
        return date1.getDate() === date2.getDate() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getFullYear() === date2.getFullYear();
    }

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        monthYear.textContent = `${monthNames[month]} ${year}`;
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevMonthDays = new Date(year, month, 0).getDate();
        
        let html = '';
        
        for (let i = firstDay; i > 0; i--) {
            const day = prevMonthDays - i + 1;
            html += `<button type="button" class="prev-month">${day}</button>`;
        }
        
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const isToday = isSameDay(date, new Date());
            const isSelected = selectedDate && isSameDay(date, selectedDate);
            
            let classes = '';
            if (isToday) classes += ' today';
            if (isSelected) classes += ' selected';
            
            html += `<button type="button" class="${classes}" data-day="${day}">${day}</button>`;
        }
        
        const totalDays = firstDay + daysInMonth;
        const remainingCells = 42 - totalDays;
        for (let day = 1; day <= remainingCells; day++) {
            html += `<button type="button" class="next-month">${day}</button>`;
        }
        
        calendarDays.innerHTML = html;
        
        calendarDays.querySelectorAll('button:not(.prev-month):not(.next-month)').forEach(btn => {
            btn.addEventListener('click', function() {
                const day = parseInt(this.dataset.day);
                const newDate = new Date(year, month, day);
                selectedDate = newDate;
                tanggalDisplay.value = formatDate(newDate);
                tanggalHidden.value = formatDateYMD(newDate);
                renderCalendar();
                tanggalPicker.classList.remove('show');
            });
        });
    }

    if (tanggalDisplay) {
        tanggalDisplay.addEventListener('click', function(e) {
            e.stopPropagation();
            tanggalPicker.classList.toggle('show');
            renderCalendar();
        });
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-date-input')) {
            if (tanggalPicker) tanggalPicker.classList.remove('show');
        }
    });

    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });
    }

    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });
    }

    if (clearDateBtn) {
        clearDateBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            selectedDate = null;
            tanggalDisplay.value = '';
            tanggalHidden.value = '';
            renderCalendar();
            tanggalPicker.classList.remove('show');
        });
    }

    if (todayBtn) {
        todayBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const today = new Date();
            currentDate = new Date(today);
            selectedDate = new Date(today);
            tanggalDisplay.value = formatDate(today);
            tanggalHidden.value = formatDateYMD(today);
            renderCalendar();
            tanggalPicker.classList.remove('show');
        });
    }

    renderCalendar();
});
</script>
@endpush