<!-- MODAL DINAMIS PENGAJUAN PEMINJAMAN -->
<div class="modal fade" id="modalPinjamUniversal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="proses_pinjam.php">
                <!-- ID Buku akan diisi secara dinamis via JavaScript -->
                <input type="hidden" name="id_buku" id="modal_id_buku">
                
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-book-medical me-2"></i>Form Pengajuan Peminjaman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body text-start">
                    <div class="mb-3 p-3 bg-light rounded border">
                        <small class="text-muted d-block">Buku yang dipilih:</small>
                        <strong class="text-primary fs-6" id="modal_judul_buku">-</strong>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Peminjam</label>
                        <input type="text" name="nama_peminjam" class="form-control" placeholder="Masukkan nama lengkapmu" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. HP / WhatsApp Aktif</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 081234567890" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rencana Tanggal Pengembalian</label>
                        <input type="date" name="tanggal_kembali_rencana" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')); ?>" required>
                        <small class="text-muted" style="font-size: 0.8rem;">*Batas maksimal peminjaman mengikuti kebijakan perpustakaan.</small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script JavaScript untuk Mengisi Data Dinamis ke Modal -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalPinjam = document.getElementById('modalPinjamUniversal');
        if (modalPinjam) {
            modalPinjam.addEventListener('show.bs.modal', function (event) {
                // Tombol yang memicu modal
                const button = event.relatedTarget;
                
                // Ambil data dari atribut data-*
                const idBuku = button.getAttribute('data-id');
                const judulBuku = button.getAttribute('data-judul');
                
                // Masukkan ke dalam elemen form di dalam modal
                document.getElementById('modal_id_buku').value = idBuku;
                document.getElementById('modal_judul_buku').textContent = judulBuku;
            });
        }
    });
</script>