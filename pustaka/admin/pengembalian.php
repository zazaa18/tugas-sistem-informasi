<?php
session_start();
require_once '../config/database.php';

// Autentikasi Admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// -------------------------------------------------------------
// SEARCH & FETCH RIWAYAT PENGEMBALIAN (STATUS = SELESAI)
// -------------------------------------------------------------
$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT p.*, b.judul_buku 
    FROM peminjaman p 
    JOIN buku b ON p.id_buku = b.id_buku 
    WHERE p.status = 'Selesai'
";

$params = [];
if (!empty($search)) {
    $sql .= " AND (p.nama_peminjam LIKE ? OR p.kode_transaksi LIKE ? OR b.judul_buku LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$sql .= " ORDER BY p.tanggal_kembali_real DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$listRiwayat = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengembalian - Pustaka Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <!-- Navbar Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-book-reader me-2"></i>Pustaka Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="peminjaman.php"><i class="fas fa-list-alt me-1"></i> Peminjaman Aktif</a></li>
                    <li class="nav-item"><a class="nav-link active" href="pengembalian.php"><i class="fas fa-history me-1"></i> Riwayat Pengembalian</a></li>
                    <li class="nav-item"><a class="nav-link" href="buku.php"><i class="fas fa-book me-1"></i> Data Buku</a></li>
                    <li class="nav-item"><a class="nav-link" href="kategori.php"><i class="fas fa-tags me-1"></i> Kategori</a></li>
                </ul>
                <div class="d-flex align-items-center text-white">
                    <span class="me-3"><i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></span>
                    <a href="logout.php" class="btn btn-sm btn-outline-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5">

        <!-- Header Page -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
            <div>
                <h4 class="fw-bold mb-0"><i class="fas fa-history text-primary me-2"></i>Riwayat Pengembalian Buku</h4>
                <p class="text-muted small mb-0">Daftar lengkap transaksi buku yang telah dikembalikan oleh peminjam</p>
            </div>
            
            <!-- Form Pencarian -->
            <form method="GET" action="pengembalian.php" class="d-flex gap-2" style="max-width: 350px;">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Kode TRX / Nama / Buku..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                <?php if (!empty($search)): ?>
                    <a href="pengembalian.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-undo"></i></a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table Riwayat -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode TRX</th>
                                <th>Nama Peminjam</th>
                                <th>No. HP / WA</th>
                                <th>Judul Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali (Asli)</th>
                                <th>Tgl Dikembalikan</th>
                                <th class="text-center">Denda Dibayar</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listRiwayat)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                        <?= !empty($search) ? 'Riwayat pengembalian tidak ditemukan.' : 'Belum ada riwayat buku yang dikembalikan.'; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listRiwayat as $row): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary font-monospace"><?= htmlspecialchars($row['kode_transaksi']); ?></span></td>
                                        <td class="fw-bold"><?= htmlspecialchars($row['nama_peminjam']); ?></td>
                                        <td><?= htmlspecialchars($row['no_hp']); ?></td>
                                        <td><?= htmlspecialchars($row['judul_buku']); ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_kembali_rencana'])); ?></td>
                                        <td class="fw-semibold text-success"><?= date('d/m/Y', strtotime($row['tanggal_kembali_real'])); ?></td>
                                        <td class="text-center">
                                            <?php if ($row['denda'] > 0): ?>
                                                <span class="badge bg-danger">Rp <?= number_format($row['denda'], 0, ',', '.'); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border">Rp 0 (Tepat Waktu)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i> Selesai</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>