<?php
session_start();
require_once '../config/database.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = 'Dashboard Admin - Pustaka';

// Ambil Statistik Ringkasan
$total_buku       = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
$total_kategori   = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
$total_peminjaman = $pdo->query("SELECT COUNT(*) FROM peminjaman")->fetchColumn();
$pending_pinjam   = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'Pending'")->fetchColumn();

// Ambil 5 Transaksi Peminjaman Terbaru
$sql_terbaru = "
    SELECT p.*, b.judul_buku 
    FROM peminjaman p 
    JOIN buku b ON p.id_buku = b.id_buku 
    ORDER BY p.id_peminjaman DESC 
    LIMIT 5
";
$peminjaman_terbaru = $pdo->query($sql_terbaru)->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title; ?></title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background-color: #212529; color: #fff; }
        .sidebar .nav-link { color: rgba(255,255,255,0.75); border-radius: 8px; margin-bottom: 4px; padding: 10px 15px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background-color: #0d6efd; }
        .stat-card { border-radius: 16px; border: none; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
        .icon-shape { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <!-- Sidebar Navigasi -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-3 collapse">
            <div class="d-flex align-items-center mb-4 px-2">
                <div class="bg-primary text-white rounded-3 p-2 me-2">
                    <i class="fas fa-book-reader fa-lg"></i>
                </div>
                <h5 class="fw-bold mb-0">Pustaka Admin</h5>
            </div>
            <hr class="text-secondary">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="buku.php">
                        <i class="fas fa-book me-2"></i> Kelola Buku
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kategori.php">
                        <i class="fas fa-list me-2"></i> Kategori
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="peminjaman.php">
                        <i class="fas fa-hand-holding-hand me-2"></i> Peminjaman
                        <?php if ($pending_pinjam > 0): ?>
                            <span class="badge bg-warning text-dark float-end rounded-pill"><?= $pending_pinjam; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i> Keluar (Logout)
                    </a>
                </li>
            </ul>
        </div>

        <!-- Konten Utama Dashboard -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            
            <!-- Navbar Atas -->
            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <div>
                    <h3 class="fw-bold text-dark mb-0">Selamat Datang, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Petugas Perpustakaan'); ?>! 👋</h3>
                    <p class="text-muted small mb-0">Berikut adalah ringkasan data Perpustakaan Pustaka.</p>
                </div>
                <div>
                    <a href="../index.php" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-external-link-alt me-1"></i> Lihat Katalog Utama
                    </a>
                </div>
            </div>

            <!-- Kartu Statistik Row -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card shadow-sm p-3 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-primary bg-opacity-10 text-primary me-3">
                                <i class="fas fa-book fa-lg"></i>
                            </div>
                            <div>
                                <span class="text-muted small fw-semibold">Total Buku</span>
                                <h4 class="fw-bold mb-0"><?= $total_buku; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card shadow-sm p-3 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-success bg-opacity-10 text-success me-3">
                                <i class="fas fa-bookmark fa-lg"></i>
                            </div>
                            <div>
                                <span class="text-muted small fw-semibold">Kategori</span>
                                <h4 class="fw-bold mb-0"><?= $total_kategori; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card shadow-sm p-3 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-info bg-opacity-10 text-info me-3">
                                <i class="fas fa-file-invoice fa-lg"></i>
                            </div>
                            <div>
                                <span class="text-muted small fw-semibold">Total Transaksi</span>
                                <h4 class="fw-bold mb-0"><?= $total_peminjaman; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card shadow-sm p-3 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-warning bg-opacity-15 text-warning me-3">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                            <div>
                                <span class="text-muted small fw-semibold">Perlu Persetujuan</span>
                                <h4 class="fw-bold mb-0 text-warning"><?= $pending_pinjam; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Pengajuan Peminjaman Terbaru -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="fw-bold text-dark mb-0">Pengajuan Peminjaman Terbaru</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Peminjam</th>
                                <th>Buku</th>
                                <th>Tgl Pengajuan / Pinjam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($peminjaman_terbaru)): ?>
                                <?php foreach ($peminjaman_terbaru as $i => $p): ?>
                                    <tr>
                                        <td><?= $i + 1; ?></td>
                                        <td>
                                            <strong class="d-block text-dark"><?= htmlspecialchars($p['nama_peminjam']); ?></strong>
                                            <span class="text-muted small"><?= htmlspecialchars($p['no_hp'] ?? '-'); ?></span>
                                        </td>
                                        <td class="fw-semibold text-primary"><?= htmlspecialchars($p['judul_buku']); ?></td>
                                        <td>
                                            <?php 
                                                // Cek jika tanggal_pinjam ada nilainya, jika NULL pakai tanggal_pengajuan
                                                $tgl_tampil = !empty($p['tanggal_pinjam']) ? $p['tanggal_pinjam'] : ($p['tanggal_pengajuan'] ?? null);
                                                echo $tgl_tampil ? date('d/m/Y H:i', strtotime($tgl_tampil)) : '-';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($p['status'] == 'Pending'): ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php elseif ($p['status'] == 'Dipinjam'): ?>
                                                <span class="badge bg-success">Dipinjam</span>
                                            <?php elseif ($p['status'] == 'Ditolak'): ?>
                                                <span class="badge bg-danger">Ditolak</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($p['status']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data peminjaman.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>