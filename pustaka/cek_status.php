<?php
session_start();
require_once 'config/database.php';

$hasil = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_transaksi = trim($_POST['kode_transaksi'] ?? '');

    if (!empty($kode_transaksi)) {
        $stmt = $pdo->prepare("
            SELECT p.*, b.judul_buku, b.penulis 
            FROM peminjaman p 
            JOIN buku b ON p.id_buku = b.id_buku 
            WHERE p.kode_transaksi = ?
        ");
        $stmt->execute([$kode_transaksi]);
        $hasil = $stmt->fetch();

        if (!$hasil) {
            $error = "Kode transaksi '<strong>" . htmlspecialchars($kode_transaksi) . "</strong>' tidak ditemukan dalam sistem.";
        }
    } else {
        $error = "Mohon masukkan kode transaksi terlebih dahulu.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Peminjaman - Pustaka</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100 justify-content-center py-5">

    <div class="container" style="max-width: 600px;">
        
        <!-- Navbar Sederhana -->
        <div class="text-center mb-4">
            <a href="index.php" class="text-decoration-none text-dark fw-bold fs-4">
                <i class="fas fa-book-reader text-primary me-2"></i>Pustaka Perpustakaan
            </a>
            <p class="text-muted small">Cek status pengajuan atau peminjaman buku kamu</p>
        </div>

        <div class="card shadow-sm border-0 rounded-4 p-4 p-sm-5 bg-white mb-4">
            <h4 class="fw-bold mb-3 text-center"><i class="fas fa-search-dollar text-primary me-2"></i>Lacak Status Peminjaman</h4>
            
            <form method="POST" action="cek_status.php" class="mb-4">
                <div class="input-group mb-3">
                    <input type="text" name="kode_transaksi" class="form-control" placeholder="Masukkan Kode Transaksi (Cth: TRX20261020...)" value="<?= htmlspecialchars($_POST['kode_transaksi'] ?? ''); ?>" required autofocus>
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="fas fa-search me-1"></i> Cek Status
                    </button>
                </div>
            </form>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                    <i class="fas fa-exclamation-triangle me-1"></i> <?= $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($hasil): ?>
                <div class="card bg-light border p-3 rounded-3">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <span class="text-muted small">Kode Transaksi</span>
                        <span class="font-monospace fw-bold text-primary"><?= htmlspecialchars($hasil['kode_transaksi']); ?></span>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted small">Nama Peminjam:</div>
                        <div class="col-7 fw-bold"><?= htmlspecialchars($hasil['nama_peminjam']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted small">Buku:</div>
                        <div class="col-7"><?= htmlspecialchars($hasil['judul_buku']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted small">Tgl Pengajuan:</div>
                        <div class="col-7"><?= date('d/m/Y H:i', strtotime($hasil['tanggal_pengajuan'])); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted small">Rencana Kembali:</div>
                        <div class="col-7 text-danger fw-semibold"><?= date('d/m/Y', strtotime($hasil['tanggal_kembali_rencana'])); ?></div>
                    </div>
                    <div class="row align-items-center mt-3 pt-2 border-top">
                        <div class="col-5 text-muted small">Status Saat Ini:</div>
                        <div class="col-7">
                            <?php 
                                $badgeStatus = match($hasil['status']) {
                                    'Pending' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending (Menunggu ACC)</span>',
                                    'Dipinjam' => '<span class="badge bg-info text-dark"><i class="fas fa-hand-holding-book me-1"></i> Sedang Dipinjam</span>',
                                    'Selesai' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i> Selesai (Dikembalikan)</span>',
                                    'Ditolak' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i> Ditolak</span>',
                                    default => '<span class="badge bg-secondary">'.$hasil['status'].'</span>'
                                };
                                echo $badgeStatus;
                            ?>
                        </div>
                    </div>

                    <?php if ($hasil['status'] === 'Pending'): ?>
                        <div class="alert alert-warning mt-3 mb-0 small">
                            <i class="fas fa-info-circle me-1"></i> Pengajuanmu sedang menunggu verifikasi dari Admin perpustakaan.
                        </div>
                    <?php elseif ($hasil['status'] === 'Dipinjam'): ?>
                        <div class="alert alert-info mt-3 mb-0 small">
                            <i class="fas fa-info-circle me-1"></i> Buku sedang kamu pinjam. Harap dikembalikan sebelum tanggal rencana kembali.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center">
            <a href="index.php" class="text-decoration-none small text-muted">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Katalog Buku
            </a>
        </div>

    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>