<?php
session_start();
require_once 'config/database.php';

$page_title = 'Bukti Peminjaman Buku';

// Ambil ID Peminjaman dari URL
$id_peminjaman = (int)($_GET['id'] ?? 0);

if ($id_peminjaman <= 0) {
    header("Location: index.php");
    exit;
}

// Query Ambil Detail Peminjaman beserta Informasi Buku
$sql = "
    SELECT p.*, b.judul_buku, b.penulis, k.nama_kategori
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    JOIN kategori k ON b.id_kategori = k.id_kategori
    WHERE p.id_peminjaman = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_peminjaman]);
$data = $stmt->fetch();

// Jika data peminjaman tidak ditemukan
if (!$data) {
    $_SESSION['msg_error'] = "Data peminjaman tidak ditemukan.";
    header("Location: index.php");
    exit;
}

// Include Header Publik
include_once 'includes/header.php';
?>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background-color: #fff !important;
        }
        .card-bukti {
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }
    }
    .card-bukti {
        border-radius: 16px;
        background: #ffffff;
    }
</style>

<main class="container my-4 my-lg-5">

    <!-- Navigasi Aksi (Tidak Ikut Tercetak) -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Katalog
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm">
            <i class="fas fa-print me-1"></i> Cetak Bukti Pinjam
        </button>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            
            <!-- Card Bukti Peminjaman -->
            <div class="card card-bukti shadow-sm border-0 p-4 p-md-5">
                
                <!-- Header Kartu -->
                <div class="text-center border-bottom pb-4 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary d-inline-block p-3 rounded-circle mb-3">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">Bukti Pengajuan Peminjaman</h4>
                    <p class="text-muted small mb-0">Simpan atau cetak bukti ini untuk ditunjukkan kepada petugas perpustakaan.</p>
                </div>

                <!-- Status Badge -->
                <div class="text-center mb-4">
                    <?php if ($data['status'] == 'Pending'): ?>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6 fw-semibold">
                            <i class="fas fa-clock me-1"></i> Menunggu Persetujuan Admin
                        </span>
                    <?php elseif ($data['status'] == 'Dipinjam'): ?>
                        <span class="badge bg-success px-3 py-2 rounded-pill fs-6 fw-semibold">
                            <i class="fas fa-book-reader me-1"></i> Disetujui / Sedang Dipinjam
                        </span>
                    <?php elseif ($data['status'] == 'Ditolak'): ?>
                        <span class="badge bg-danger px-3 py-2 rounded-pill fs-6 fw-semibold">
                            <i class="fas fa-times-circle me-1"></i> Pengajuan Ditolak
                        </span>
                    <?php else: ?>
                        <span class="badge bg-secondary px-3 py-2 rounded-pill fs-6 fw-semibold">
                            <i class="fas fa-check-double me-1"></i> <?= htmlspecialchars($data['status']); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Informasi Peminjam & Buku -->
                <div class="bg-light p-3 p-md-4 rounded-3 mb-4 border">
                    <div class="row g-3">
                        <div class="col-6 col-sm-4 text-muted small">Kode Transaksi</div>
                        <div class="col-6 col-sm-8 fw-bold text-dark">#PINJAM-<?= str_pad($data['id_peminjaman'], 5, '0', STR_PAD_LEFT); ?></div>

                        <div class="col-6 col-sm-4 text-muted small">Nama Peminjam</div>
                        <div class="col-6 col-sm-8 fw-semibold text-dark"><?= htmlspecialchars($data['nama_peminjam']); ?></div>

                        <div class="col-6 col-sm-4 text-muted small">NIM / Identitas</div>
                        <div class="col-6 col-sm-8 fw-semibold text-dark"><?= htmlspecialchars($data['nim_peminjam'] ?? '-'); ?></div>

                        <div class="col-6 col-sm-4 text-muted small">Judul Buku</div>
                        <div class="col-6 col-sm-8 fw-bold text-primary"><?= htmlspecialchars($data['judul_buku']); ?></div>

                        <div class="col-6 col-sm-4 text-muted small">Penulis / Kategori</div>
                        <div class="col-6 col-sm-8 text-dark"><?= htmlspecialchars($data['penulis']); ?> (<?= htmlspecialchars($data['nama_kategori']); ?>)</div>

                        <div class="col-6 col-sm-4 text-muted small">Tanggal Pinjam</div>
                        <div class="col-6 col-sm-8 text-dark"><?= date('d M Y', strtotime($data['tgl_pinjam'])); ?></div>

                        <div class="col-6 col-sm-4 text-muted small">Batas Kembali</div>
                        <div class="col-6 col-sm-8 text-danger fw-semibold"><?= date('d M Y', strtotime($data['tgl_kembali'])); ?></div>
                    </div>
                </div>

                <div class="text-center text-muted small">
                    <p class="mb-0"><i class="fas fa-info-circle me-1"></i> Tunjukkan kode transaksi ini ke layanan sirkulasi perpustakaan saat mengambil buku.</p>
                </div>

            </div>

        </div>
    </div>

</main>

<?php
// Include Footer Publik
include_once 'includes/footer.php';
?>