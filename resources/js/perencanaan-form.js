class PerencanaanForm {
    constructor() {
        this.detailCounter = 0;
        this.init();
    }
    
    init() {
        console.log('PerencanaanForm initialized');
        
        // Hitung detail awal
        this.detailCounter = document.querySelectorAll('.detail-item').length;
        console.log('Initial detail count:', this.detailCounter);
        
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
        
        // Reset form jika ada tombol reset
        const resetBtn = document.querySelector('button[type="reset"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', (e) => this.handleReset(e));
        }
        
        // Inisialisasi awal
        this.updateNumbering();
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
                            <input type="text" 
                                   class="form-control" 
                                   name="perencanaan[]" 
                                   placeholder="Contoh: Rapat Orang Tua"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Target <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   name="target[]" 
                                   placeholder="Contoh: 20 Orang"
                                   required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea class="form-control" 
                                      name="deskripsi[]" 
                                      rows="2"
                                      placeholder="Deskripsi lengkap perencanaan"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Pelaksanaan</label>
                            <textarea class="form-control" 
                                      name="pelaksanaan[]" 
                                      rows="2"
                                      placeholder="Rencana pelaksanaan kegiatan"></textarea>
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
            
            // Update numbering
            this.updateNumbering();
            
            // Animasi
            const newElement = container.lastElementChild;
            newElement.classList.add('new-added');
            setTimeout(() => {
                newElement.classList.remove('new-added');
            }, 2000);
            
            // Scroll ke elemen baru
            newElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Focus ke input pertama
            setTimeout(() => {
                newElement.querySelector('input[name="perencanaan[]"]').focus();
            }, 300);
            
            console.log('Detail added. Total:', this.detailCounter);
        }
    }
    
    handleDelete(e) {
        if (e.target.closest('.btn-hapus')) {
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
                    this.detailCounter = document.querySelectorAll('.detail-item').length;
                    console.log('Detail removed. Total:', this.detailCounter);
                }
            }
        }
    }
    
    handleReset(e) {
        e.preventDefault();
        
        if (confirm('Yakin ingin mereset semua data form? Semua input akan dikosongkan.')) {
            // Reset form utama
            document.getElementById('judul').value = '';
            document.getElementById('bulan').value = '';
            document.getElementById('tahun').value = '';
            
            // Reset semua detail kecuali pertama
            const container = document.getElementById('detailContainer');
            const firstDetail = container.querySelector('.detail-item');
            
            // Kosongkan detail pertama
            if (firstDetail) {
                firstDetail.querySelector('input[name="perencanaan[]"]').value = '';
                firstDetail.querySelector('input[name="target[]"]').value = '';
                firstDetail.querySelector('textarea[name="deskripsi[]"]').value = '';
                firstDetail.querySelector('textarea[name="pelaksanaan[]"]').value = '';
            }
            
            // Hapus semua detail setelah pertama
            const allDetails = container.querySelectorAll('.detail-item');
            allDetails.forEach((detail, index) => {
                if (index > 0) detail.remove();
            });
            
            // Reset counter dan update numbering
            this.detailCounter = 1;
            this.updateNumbering();
            
            // Hapus error classes
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            
            // Focus ke judul
            document.getElementById('judul').focus();
            
            alert('Form telah direset!');
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
        console.log('Numbering updated. Total:', items.length);
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
        
        // Validasi duplicate perencanaan
        const perencanaanValues = [];
        let hasDuplicate = false;
        
        document.querySelectorAll('input[name="perencanaan[]"]').forEach(input => {
            const value = input.value.trim();
            if (perencanaanValues.includes(value)) {
                hasDuplicate = true;
                input.classList.add('is-invalid');
            } else {
                perencanaanValues.push(value);
            }
        });
        
        if (hasDuplicate) {
            e.preventDefault();
            alert('Error: Perencanaan tidak boleh ada yang sama!');
            return false;
        }
        
        // Konfirmasi submit
        if (!confirm('Apakah Anda yakin ingin menyimpan perencanaan ini?')) {
            e.preventDefault();
            return false;
        }
        
        console.log('Form validation passed!');
        return true;
    }
}

// Inisialisasi saat DOM siap
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready, initializing PerencanaanForm...');
    window.perencanaanForm = new PerencanaanForm();
});