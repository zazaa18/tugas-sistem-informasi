<?php
session_start();
require_once '../config/database.php';

// Autentikasi Admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Tarif denda per hari (dalam Rupiah)
$tarifDendaPerHari = 2000;

// -------------------------------------------------------------
// PROCESS: PROSES PENGEMBALIAN BUKU
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'return') {
    $id_peminjaman = (int)$_POST['id_peminjaman'];
    $denda         = (float)$_POST['denda'];
    $today         = date('Y-m-d');

    // Ambil data peminjaman
    $stmt = $pdo->prepare("SELECT * FROM peminjaman WHERE id_peminjaman = ? AND status = 'Dipinjam'");
    $stmt->execute([$id_peminjaman]);
    $pinjam = $stmt->fetch();

    if ($pinjam) {
        $pdo->beginTransaction();
        try {
            // 1. Update status transaksi menjadi 'Selesai', set tgl pengembalian real & denda
            $updatePinjam = $pdo->prepare("
                UPDATE peminjaman 
                SET status = 'Selesai', tanggal_kembali_real = ?, denda = ? 
                WHERE id_peminjaman = ?
            ");
            $updatePinjam->execute([$today, $denda, $id_peminjaman]);

            // 2. Kembalikan stok_tersedia buku (+1)
            $updateStok = $pdo->prepare("UPDATE buku SET stok_tersedia = stok_tersedia + 1 WHERE id_buku = ?");
            $updateStok->execute([$pinjam['id_buku']]);

            $pdo->commit();
            $_SESSION['msg_success'] = "Pengembalian buku untuk kode <strong>{$pinjam['kode_transaksi']}</strong> berhasil diproses. Stok buku telah bertambah +1.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['msg_error'] = "Gagal memproses pengembalian: " . $e->getMessage();
        }
    }
    header("Location: peminjaman.php");
    exit;
}

// -------------------------------------------------------------
// SEARCH & FETCH PEMINJAMAN AKTIF
// -------------------------------------------------------------
$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT p.*, b.judul_buku 
    FROM peminjaman p 
    JOIN buku b ON p.id_buku = b.id_buku 
    WHERE p.status = 'Dipinjam'
";

$params = [];
if (!empty($search)) {
    $sql .= " AND (p.nama_peminjam LIKE ? OR p.kode_transaksi LIKE ? OR b.judul_buku LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$sql .= " ORDER BY p.tanggal_kembali_rencana ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$listDipinjam = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Aktif - Pustaka Admin</title>
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
                    <li class="nav-item"><a class="nav-link active" href="peminjaman.php"><i class="fas fa-list-alt me-1"></i> Peminjaman Aktif</a></li>
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
                <h4 class="fw-bold mb-0"><i class="fas fa-hand-holding-book text-primary me-2"></i>Daftar Peminjaman Aktif</h4>
                <p class="text-muted small mb-0">Pantau peminjam buku dan kelola proses pengembalian serta denda</p>
            </div>
            
            <!-- Form Pencarian -->
            <form method="GET" action="peminjaman.php" class="d-flex gap-2" style="max-width: 350px;">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Kode TRX / Nama / Buku..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                <?php if (!empty($search)): ?>
                    <a href="peminjaman.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-undo"></i></a>
                <?php endif; ?>
            </form>
        </div>

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

        <!-- Table Data Peminjaman Aktif -->
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
                                <th>Batas Kembali</th>
                                <th>Status / Denda</th>
                                <th class="text-center" style="width: 170px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listDipinjam)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                        <?= !empty($search) ? 'Data peminjaman tidak ditemukan.' : 'Saat ini tidak ada buku yang sedang dipinjam.'; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listDipinjam as $row): 
                                    // Hitung keterlambatan
                                    $todayObj = new DateTime();
                                    $dueObj   = new DateTime($row['tanggal_kembali_rencana']);
                                    $isOverdue = $todayObj > $dueObj;
                                    
                                    $hariTerlambat = 0;
                                    $denda = 0;

                                    if ($isOverdue) {
                                        $diff = $todayObj->diff($dueObj);
                                        $hariTerlambat = $diff->days;
                                        $denda = $hariTerlambat * $tarifDendaPerHari;
                                    }
                                ?>
                                    <tr class="<?= $isOverdue ? 'table-danger' : ''; ?>">
                                        <td><span class="badge bg-secondary font-monospace"><?= htmlspecialchars($row['kode_transaksi']); ?></span></td>
                                        <td class="fw-bold"><?= htmlspecialchars($row['nama_peminjam']); ?></td>
                                        <td>
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $row['no_hp']); ?>" target="_blank" class="text-decoration-none text-success">
                                                <i class="fab fa-whatsapp me-1"></i><?= htmlspecialchars($row['no_hp']); ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($row['judul_buku']); ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                        <td>
                                            <span class="fw-semibold <?= $isOverdue ? 'text-danger' : 'text-dark'; ?>">
                                                <?= date('d/m/Y', strtotime($row['tanggal_kembali_rencana'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($isOverdue): ?>
                                                <span class="badge bg-danger mb-1">Terlambat <?= $hariTerlambat; ?> Hari</span><br>
                                                <small class="text-danger fw-bold">Denda: Rp <?= number_format($denda, 0, ',', '.'); ?></small>
                                            <?php else: ?>
                                                <span class="badge bg-success">Dipinjam (Lancar)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <!-- Button Process Return Trigger Modal -->
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalKembali<?= $row['id_peminjaman']; ?>">
                                                <i class="fas fa-undo me-1"></i> Kembalikan
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- MODAL KONFIRMASI PENGEMBALIAN -->
                                    <div class="modal fade" id="modalKembali<?= $row['id_peminjaman']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="peminjaman.php">
                                                    <input type="hidden" name="action" value="return">
                                                    <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman']; ?>">
                                                    <input type="hidden" name="denda" value="<?= $denda; ?>">
                                                    
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title fw-bold"><i class="fas fa-box-check me-2"></i>Konfirmasi Pengembalian</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3 p-3 bg-light rounded border">
                                                            <div class="row mb-1">
                                                                <div class="col-4 text-muted">Kode TRX:</div>
                                                                <div class="col-8 fw-bold font-monospace"><?= htmlspecialchars($row['kode_transaksi']); ?></div>
                                                            </div>
                                                            <div class="row mb-1">
                                                                <div class="col-4 text-muted">Peminjam:</div>
                                                                <div class="col-8 fw-bold"><?= htmlspecialchars($row['nama_peminjam']); ?></div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-4 text-muted">Buku:</div>
                                                                <div class="col-8"><?= htmlspecialchars($row['judul_buku']); ?></div>
                                                            </div>
                                                        </div>

                                                        <?php if ($isOverdue): ?>
                                                            <div class="alert alert-warning mb-3">
                                                                <i class="fas fa-exclamation-triangle me-1"></i> Peminjam terlambat <strong><?= $hariTerlambat; ?> hari</strong>.
                                                                <hr class="my-2">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <span>Total Denda:</span>
                                                                    <strong class="fs-5 text-danger">Rp <?= number_format($denda, 0, ',', '.'); ?></strong>
                                                                </div>
                                                            </div>
                                                        <?php else: ?>
                                                            <p class="text-success small mb-3"><i class="fas fa-check-circle me-1"></i> Peminjaman tepat waktu (Tidak ada denda).</p>
                                                        <?php endif; ?>

                                                        <p class="mb-0 small text-muted">Pastikan fisik buku sudah diperiksa dan diterima dalam kondisi baik sebelum mengonfirmasi.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i> Selesaikan Transaksi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END MODAL KONFIRMASI -->

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