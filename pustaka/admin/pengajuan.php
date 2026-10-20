<?php
session_start();
require_once '../config/database.php';

// Autentikasi Admin
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
                    $_SESSION['msg_success'] = "Pengajuan Kode TRX <strong>{$pinjam['kode_transaksi']}</strong> berhasil DISETUJUI! Stok buku berkurang -1.";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $_SESSION['msg_error'] = "Gagal memproses persetujuan: " . $e->getMessage();
                }
            } else {
                $_SESSION['msg_error'] = "Gagal menyetujui: Stok buku saat ini tidak tersedia!";
            }
        } elseif ($action === 'reject') {
            $updatePinjam = $pdo->prepare("UPDATE peminjaman SET status = 'Ditolak' WHERE id_peminjaman = ?");
            $updatePinjam->execute([$id_peminjaman]);
            $_SESSION['msg_success'] = "Pengajuan Kode TRX <strong>{$pinjam['kode_transaksi']}</strong> telah DITOLAK.";
        }
    }
    header("Location: pengajuan.php");
    exit;
}

// -------------------------------------------------------------
// SEARCH & FETCH DATA PENGAJUAN (Pending & Ditolak)
// -------------------------------------------------------------
$filterStatus = $_GET['status'] ?? 'Pending';
$search       = trim($_GET['search'] ?? '');

$sql = "
    SELECT p.*, b.judul_buku 
    FROM peminjaman p 
    JOIN buku b ON p.id_buku = b.id_buku 
    WHERE p.status = ?
";

$params = [$filterStatus];

if (!empty($search)) {
    $sql .= " AND (p.nama_peminjam LIKE ? OR p.kode_transaksi LIKE ? OR b.judul_buku LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY p.id_peminjaman DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$listPengajuan = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengajuan - Pustaka Admin</title>
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
                    <li class="nav-item"><a class="nav-link active" href="pengajuan.php"><i class="fas fa-clock me-1"></i> Pengajuan</a></li>
                    <li class="nav-item"><a class="nav-link" href="peminjaman.php"><i class="fas fa-list-alt me-1"></i> Peminjaman Aktif</a></li>
                    <li class="nav-item"><a class="nav-link" href="pengembalian.php"><i class="fas fa-history me-1"></i> Riwayat Pengembalian</a></li>
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
                <h4 class="fw-bold mb-0"><i class="fas fa-clipboard-check text-primary me-2"></i>Verifikasi Pengajuan Peminjaman</h4>
                <p class="text-muted small mb-0">Kelola dan verifikasi peminjaman yang diajukan oleh peminjam publik</p>
            </div>

            <!-- Form Search -->
            <form method="GET" action="pengajuan.php" class="d-flex gap-2" style="max-width: 350px;">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus); ?>">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari TRX / Nama / Buku..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                <?php if (!empty($search)): ?>
                    <a href="pengajuan.php?status=<?= $filterStatus; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-undo"></i></a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Filter Tab Status -->
        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link <?= $filterStatus === 'Pending' ? 'active' : ''; ?>" href="pengajuan.php?status=Pending">
                    <i class="fas fa-hourglass-half me-1"></i> Menunggu Persetujuan (Pending)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $filterStatus === 'Ditolak' ? 'active' : ''; ?>" href="pengajuan.php?status=Ditolak">
                    <i class="fas fa-times-circle me-1"></i> Riwayat Ditolak
                </a>
            </li>
        </ul>

        <!-- Alert Notification -->
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

        <!-- Table Data Pengajuan -->
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
                                <th>Tgl Pengajuan</th>
                                <th>Rencana Kembali</th>
                                <th class="text-center">Status</th>
                                <?php if ($filterStatus === 'Pending'): ?>
                                    <th class="text-center" style="width: 200px;">Aksi Verifikasi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listPengajuan)): ?>
                                <tr>
                                    <td colspan="<?= $filterStatus === 'Pending' ? '8' : '7'; ?>" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Tidak ada pengajuan peminjaman berstatus <strong><?= htmlspecialchars($filterStatus); ?></strong>.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listPengajuan as $row): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary font-monospace"><?= htmlspecialchars($row['kode_transaksi']); ?></span></td>
                                        <td class="fw-bold"><?= htmlspecialchars($row['nama_peminjam']); ?></td>
                                        <td>
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $row['no_hp']); ?>" target="_blank" class="text-decoration-none text-success">
                                                <i class="fab fa-whatsapp me-1"></i><?= htmlspecialchars($row['no_hp']); ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($row['judul_buku']); ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['tanggal_pengajuan'])); ?></td>
                                        <td><span class="text-primary fw-semibold"><?= date('d/m/Y', strtotime($row['tanggal_kembali_rencana'])); ?></span></td>
                                        <td class="text-center">
                                            <?php if ($row['status'] === 'Pending'): ?>
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Ditolak</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($filterStatus === 'Pending'): ?>
                                            <td class="text-center">
                                                <form method="POST" action="pengajuan.php" class="d-inline" onsubmit="return confirm('Setujui peminjaman ini? Stok buku akan berkurang 1.');">
                                                    <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman']; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-sm btn-success me-1">
                                                        <i class="fas fa-check me-1"></i> ACC
                                                    </button>
                                                </form>
                                                <form method="POST" action="pengajuan.php" class="d-inline" onsubmit="return confirm('Tolak pengajuan peminjaman ini?');">
                                                    <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman']; ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-times me-1"></i> Tolak
                                                    </button>
                                                </form>
                                            </td>
                                        <?php endif; ?>
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