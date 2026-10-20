<?php
session_start();
require_once '../config/database.php';

// Cek Autentikasi Admin (Jika belum login, lempar ke login.php)
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// -------------------------------------------------------------
// PROCESS APPROVAL / ACTION (Setujui atau Tolak)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id_peminjaman'])) {
    $id_peminjaman = (int)$_POST['id_peminjaman'];
    $action = $_POST['action'];

    // Ambil data peminjaman
    $stmt = $pdo->prepare("SELECT * FROM peminjaman WHERE id_peminjaman = ?");
    $stmt->execute([$id_peminjaman]);
    $pinjam = $stmt->fetch();

    if ($pinjam && $pinjam['status'] === 'Pending') {
        if ($action === 'approve') {
            // Cek ketersediaan stok
            $stmtBuku = $pdo->prepare("SELECT stok_tersedia FROM buku WHERE id_buku = ?");
            $stmtBuku->execute([$pinjam['id_buku']]);
            $buku = $stmtBuku->fetch();

            if ($buku && $buku['stok_tersedia'] > 0) {
                $pdo->beginTransaction();
                try {
                    // 1. Update status peminjaman jadi Dipinjam & set tanggal_pinjam hari ini
                    $today = date('Y-m-d');
                    $updatePinjam = $pdo->prepare("UPDATE peminjaman SET status = 'Dipinjam', tanggal_pinjam = ? WHERE id_peminjaman = ?");
                    $updatePinjam->execute([$today, $id_peminjaman]);

                    // 2. Kurangi stok_tersedia buku (-1)
                    $updateStok = $pdo->prepare("UPDATE buku SET stok_tersedia = stok_tersedia - 1 WHERE id_buku = ?");
                    $updateStok->execute([$pinjam['id_buku']]);

                    $pdo->commit();
                    $_SESSION['msg_success'] = "Pengajuan ID {$pinjam['kode_transaksi']} berhasil DISETUJUI!";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $_SESSION['msg_error'] = "Gagal memproses persetujuan: " . $e->getMessage();
                }
            } else {
                $_SESSION['msg_error'] = "Gagal menyetujui: Stok buku tidak tersedia!";
            }
        } elseif ($action === 'reject') {
            $updatePinjam = $pdo->prepare("UPDATE peminjaman SET status = 'Ditolak' WHERE id_peminjaman = ?");
            $updatePinjam->execute([$id_peminjaman]);
            $_SESSION['msg_success'] = "Pengajuan ID {$pinjam['kode_transaksi']} telah DITOLAK.";
        }
    }
    header("Location: index.php");
    exit;
}

// -------------------------------------------------------------
// FETCH STATISTIK DASHBOARD
// -------------------------------------------------------------
$totalBuku      = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
$totalDipinjam  = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'Dipinjam'")->fetchColumn();
$totalPending   = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'Pending'")->fetchColumn();
$totalTerlambat = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'Dipinjam' AND tanggal_kembali_rencana < CURDATE()")->fetchColumn();

// -------------------------------------------------------------
// FETCH PENGAJUAN PENDING (Butuh Verifikasi/Approval)
// -------------------------------------------------------------
$stmtPending = $pdo->prepare("
    SELECT p.*, b.judul_buku 
    FROM peminjaman p 
    JOIN buku b ON p.id_buku = b.id_buku 
    WHERE p.status = 'Pending' 
    ORDER BY p.id_peminjaman DESC
");
$stmtPending->execute();
$listPending = $stmtPending->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Pustaka</title>
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
                    <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="peminjaman.php"><i class="fas fa-list-alt me-1"></i> Peminjaman Aktif</a></li>
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

        <!-- Notification Alerts -->
        <?php if (isset($_SESSION['msg_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $_SESSION['msg_success']; unset($_SESSION['msg_success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['msg_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= $_SESSION['msg_error']; unset($_SESSION['msg_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1">Total Koleksi</h6>
                                <h2 class="mb-0 fw-bold"><?= $totalBuku; ?></h2>
                            </div>
                            <i class="fas fa-book fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1">Menunggu ACC</h6>
                                <h2 class="mb-0 fw-bold"><?= $totalPending; ?></h2>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1">Sedang Dipinjam</h6>
                                <h2 class="mb-0 fw-bold"><?= $totalDipinjam; ?></h2>
                            </div>
                            <i class="fas fa-hand-holding-book fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1">Terlambat</h6>
                                <h2 class="mb-0 fw-bold"><?= $totalTerlambat; ?></h2>
                            </div>
                            <i class="fas fa-exclamation-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Verification / Approval -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-clipboard-check text-primary me-2"></i>Pengajuan Peminjaman Menunggu Persetujuan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Transaksi</th>
                                <th>Peminjam</th>
                                <th>No. HP / WA</th>
                                <th>Buku yang Dipinjam</th>
                                <th>Tgl Pengajuan</th>
                                <th>Rencana Kembali</th>
                                <th class="text-center">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listPending)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Tidak ada pengajuan peminjaman baru yang perlu diverifikasi.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listPending as $row): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary font-monospace"><?= htmlspecialchars($row['kode_transaksi']); ?></span></td>
                                        <td class="fw-bold"><?= htmlspecialchars($row['nama_peminjam']); ?></td>
                                        <td><?= htmlspecialchars($row['no_hp']); ?></td>
                                        <td><?= htmlspecialchars($row['judul_buku']); ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['tanggal_pengajuan'])); ?></td>
                                        <td><span class="text-primary fw-semibold"><?= date('d/m/Y', strtotime($row['tanggal_kembali_rencana'])); ?></span></td>
                                        <td class="text-center">
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Setujui peminjaman buku ini? Stok buku akan otomatis berkurang 1.');">
                                                <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman']; ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-sm btn-success me-1">
                                                    <i class="fas fa-check me-1"></i> Setujui (ACC)
                                                </button>
                                            </form>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Tolak pengajuan peminjaman ini?');">
                                                <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-times me-1"></i> Tolak
                                                </button>
                                            </form>
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