<?php
session_start();
require_once '../config/database.php';

// Autentikasi Admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// -------------------------------------------------------------
// FILTER PERIODE & STATUS
// -------------------------------------------------------------
$tgl_mulai  = $_GET['tgl_mulai'] ?? date('Y-m-01'); // Default awal bulan ini
$tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-d'); // Default hari ini
$status     = $_GET['status'] ?? 'Semua';

$sql = "
    SELECT p.*, b.judul_buku, b.penulis
    FROM peminjaman p 
    JOIN buku b ON p.id_buku = b.id_buku 
    WHERE DATE(p.tanggal_pengajuan) BETWEEN ? AND ?
";

$params = [$tgl_mulai, $tgl_selesai];

if ($status !== 'Semua') {
    $sql .= " AND p.status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY p.id_peminjaman DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$listLaporan = $stmt->fetchAll();

// Hitung total ringkasan
$totalTransaksi = count($listLaporan);
$totalDenda     = 0;
$totalSelesai   = 0;
$totalDipinjam  = 0;

foreach ($listLaporan as $row) {
    $totalDenda += (float)$row['denda'];
    if ($row['status'] === 'Selesai') $totalSelesai++;
    if ($row['status'] === 'Dipinjam') $totalDipinjam++;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - Pustaka Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS khusus untuk menyembunyikan elemen non-cetak saat Print */
        @media print {
            .no-print, nav, .btn, form {
                display: none !important;
            }
            body {
                background-color: #fff !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar Admin (TIDAK DICETAK) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 no-print">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-book-reader me-2"></i>Pustaka Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="pengajuan.php"><i class="fas fa-clock me-1"></i> Pengajuan</a></li>
                    <li class="nav-item"><a class="nav-link" href="peminjaman.php"><i class="fas fa-list-alt me-1"></i> Peminjaman Aktif</a></li>
                    <li class="nav-item"><a class="nav-link" href="pengembalian.php"><i class="fas fa-history me-1"></i> Riwayat Pengembalian</a></li>
                    <li class="nav-item"><a class="nav-link active" href="laporan.php"><i class="fas fa-file-alt me-1"></i> Laporan</a></li>
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
                <h4 class="fw-bold mb-0"><i class="fas fa-file-invoice text-primary me-2"></i>Laporan Transaksi Peminjaman</h4>
                <p class="text-muted small mb-0">Rekapitulasi aktivitas peminjaman buku perpustakaan</p>
            </div>
            
            <button class="btn btn-success no-print" onclick="window.print();">
                <i class="fas fa-print me-1"></i> Cetak Laporan
            </button>
        </div>

        <!-- Form Filter Periode (TIDAK DICETAK) -->
        <div class="card shadow-sm border-0 mb-4 no-print">
            <div class="card-body">
                <form method="GET" action="laporan.php" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" class="form-control form-control-sm" value="<?= htmlspecialchars($tgl_mulai); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control form-control-sm" value="<?= htmlspecialchars($tgl_selesai); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Status Transaksi</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="Semua" <?= $status === 'Semua' ? 'selected' : ''; ?>>-- Semua Status --</option>
                            <option value="Dipinjam" <?= $status === 'Dipinjam' ? 'selected' : ''; ?>>Dipinjam</option>
                            <option value="Selesai" <?= $status === 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                            <option value="Pending" <?= $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Ditolak" <?= $status === 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Judul Cetak (HANYA MUNCUL SAAT PRINTER CETAK) -->
        <div class="d-none d-print-block text-center mb-4">
            <h3 class="fw-bold mb-1">PUSTAKA - LAPORAN PEMINJAMAN BUKU</h3>
            <p class="mb-0">Periode: <?= date('d/m/Y', strtotime($tgl_mulai)); ?> s.d. <?= date('d/m/Y', strtotime($tgl_selesai)); ?> | Status: <?= htmlspecialchars($status); ?></p>
            <hr class="mt-2 mb-4">
        </div>

        <!-- Summary Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card bg-light border text-center p-3">
                    <span class="text-muted small">Total Transaksi</span>
                    <h4 class="fw-bold mb-0 text-primary"><?= $totalTransaksi; ?></h4>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card bg-light border text-center p-3">
                    <span class="text-muted small">Selesai Dikembalikan</span>
                    <h4 class="fw-bold mb-0 text-success"><?= $totalSelesai; ?></h4>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card bg-light border text-center p-3">
                    <span class="text-muted small">Masih Dipinjam</span>
                    <h4 class="fw-bold mb-0 text-info"><?= $totalDipinjam; ?></h4>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card bg-light border text-center p-3">
                    <span class="text-muted small">Total Denda Terkumpul</span>
                    <h4 class="fw-bold mb-0 text-danger">Rp <?= number_format($totalDenda, 0, ',', '.'); ?></h4>
                </div>
            </div>
        </div>

        <!-- Table Laporan -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Kode TRX</th>
                                <th>Nama Peminjam</th>
                                <th>No. HP / WA</th>
                                <th>Judul Buku</th>
                                <th>Tgl Pengajuan</th>
                                <th>Rencana Kembali</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listLaporan)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        Tidak ada data transaksi peminjaman pada periode ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($listLaporan as $row): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><span class="font-monospace fw-bold"><?= htmlspecialchars($row['kode_transaksi']); ?></span></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($row['nama_peminjam']); ?></td>
                                        <td><?= htmlspecialchars($row['no_hp']); ?></td>
                                        <td><?= htmlspecialchars($row['judul_buku']); ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_pengajuan'])); ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_kembali_rencana'])); ?></td>
                                        <td class="text-center">
                                            <?php
                                                $badge = match($row['status']) {
                                                    'Selesai' => 'bg-success',
                                                    'Dipinjam' => 'bg-info',
                                                    'Pending' => 'bg-warning text-dark',
                                                    'Ditolak' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                            ?>
                                            <span class="badge <?= $badge; ?>"><?= htmlspecialchars($row['status']); ?></span>
                                        </td>
                                        <td class="text-end">
                                            Rp <?= number_format($row['denda'], 0, ',', '.'); ?>
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