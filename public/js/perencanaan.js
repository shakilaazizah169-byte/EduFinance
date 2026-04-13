class PerencanaanForm {
    constructor() {
        this.detailCounter = 1;
        this.init();
    }
    
    init() {
        console.log('PerencanaanForm initialized');
        
        // Tombol tambah detail
        const tambahBtn = document.getElementById('tambahDetail');
        if (tambahBtn) {
            tambahBtn.addEventListener('click', (e) => this.tambahDetail(e));
            console.log('Tambah button found:', tambahBtn);
        } else {
            console.error('Tombol tambahDetail tidak ditemukan!');
        }
        
        // Event delegation untuk hapus
        const container = document.getElementById('detailContainer');
        if (container) {
            container.addEventListener('click', (e) => this.handleDelete(e));
        }
        
        // Validasi form
        const form = document.getElementById('perencanaanForm');
        if (form) {
            form.addEventListener('submit', (e) => this.validateForm(e));
        }
    }
    
    tambahDetail(e) {
        e.preventDefault();
        console.log('Tombol tambah detail diklik!');
        
        const newIndex = this.detailCounter;
        const newDetail = `
            <div class="detail-item card border mb-3" id="detail-${newIndex}">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Rencana #${newIndex + 1}</span>
                    <button type="button" class="btn btn-sm btn-danger btn-hapus" data-id="${newIndex}">
                        <i class="feather-trash me-1"></i> Hapus
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Perencanaan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="perencanaan[]" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Target <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="target[]" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi[]" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Pelaksanaan</label>
                            <textarea class="form-control" name="pelaksanaan[]" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Tambahkan ke container
        const container = document.getElementById('detailContainer');
        if (container) {
            container.insertAdjacentHTML('beforeend', newDetail);
            this.detailCounter++;
            this.updateNumbering();
            alert('Detail berhasil ditambahkan!');
        }
    }
    
    handleDelete(e) {
        if (e.target.closest('.btn-hapus')) {
            console.log('Tombol hapus diklik');
            const button = e.target.closest('.btn-hapus');
            const id = button.getAttribute('data-id');
            const totalDetails = document.querySelectorAll('.detail-item').length;
            
            if (totalDetails <= 1) {
                alert('Minimal harus ada 1 detail perencanaan!');
                return;
            }
            
            if (confirm('Apakah yakin ingin menghapus rencana ini?')) {
                const element = document.getElementById(`detail-${id}`);
                if (element) {
                    element.remove();
                    this.updateNumbering();
                }
            }
        }
    }
    
    updateNumbering() {
        const items = document.querySelectorAll('.detail-item');
        items.forEach((item, index) => {
            item.id = `detail-${index}`;
            const titleSpan = item.querySelector('.card-header span');
            if (titleSpan) {
                titleSpan.textContent = `Rencana #${index + 1}`;
            }
            const deleteBtn = item.querySelector('.btn-hapus');
            if (deleteBtn) {
                deleteBtn.setAttribute('data-id', index);
            }
        });
        this.detailCounter = items.length;
    }
    
    validateForm(e) {
        const items = document.querySelectorAll('.detail-item');
        
        // Validasi minimal 1 detail
        if (items.length === 0) {
            e.preventDefault();
            alert('Error: Minimal harus ada 1 detail perencanaan!');
            return false;
        }
        
        // Validasi input wajib
        let isValid = true;
        const requiredInputs = document.querySelectorAll('input[name="perencanaan[]"], input[name="target[]"]');
        
        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Error: Semua kolom Perencanaan dan Target wajib diisi!');
            return false;
        }
        
        return true;
    }
}

// Inisialisasi saat DOM siap
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready, initializing PerencanaanForm...');
    window.perencanaanForm = new PerencanaanForm();
});