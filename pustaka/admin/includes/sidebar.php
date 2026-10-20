<?php
// Pastikan session aktif jika sidebar dipanggil
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek Autentikasi Admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Menentukan halaman aktif untuk highlight menu sidebar
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar Component -->
<div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white min-vh-100 no-print" style="width: 260px;">
    
    <!-- Brand Logo / Title -->
    <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none px-2">
        <i class="fas fa-book-reader fa-2x me-2 text-primary"></i>
        <div>
            <h5 class="fw-bold mb-0">Pustaka</h5>
            <small class="text-muted" style="font-size: 0.75rem;">Panel Admin Perpustakaan</small>
        </div>
    </a>
    
    <hr class="my-3">

    <!-- Navigation Menu -->
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
            <a href="index.php" class="nav-link <?= $current_page == 'index.php' ? 'active bg-primary' : 'text-white hover-light'; ?>">
                <i class="fas fa-tachometer-alt me-2 style-icon"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="pengajuan.php" class="nav-link <?= $current_page == 'pengajuan.php' ? 'active bg-primary' : 'text-white hover-light'; ?>">
                <i class="fas fa-clock me-2 style-icon"></i> Pengajuan
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="peminjaman.php" class="nav-link <?= $current_page == 'peminjaman.php' ? 'active bg-primary' : 'text-white hover-light'; ?>">
                <i class="fas fa-list-alt me-2 style-icon"></i> Peminjaman Aktif
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="pengembalian.php" class="nav-link <?= $current_page == 'pengembalian.php' ? 'active bg-primary' : 'text-white hover-light'; ?>">
                <i class="fas fa-history me-2 style-icon"></i> Riwayat Kembali
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="laporan.php" class="nav-link <?= $current_page == 'laporan.php' ? 'active bg-primary' : 'text-white hover-light'; ?>">
                <i class="fas fa-file-alt me-2 style-icon"></i> Laporan Transaksi
            </a>
        </li>

        <hr class="my-2 border-secondary">

        <li class="nav-item mb-1">
            <a href="buku.php" class="nav-link <?= $current_page == 'buku.php' ? 'active bg-primary' : 'text-white hover-light'; ?>">
                <i class="fas fa-book me-2 style-icon"></i> Data Buku
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="kategori.php" class="nav-link <?= $current_page == 'kategori.php' ? 'active bg-primary' : 'text-white hover-light'; ?>">
                <i class="fas fa-tags me-2 style-icon"></i> Kategori Buku
            </a>
        </li>
    </ul>

    <hr class="my-3">

    <!-- Admin User Info & Logout -->
    <div class="dropdown">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center text-white">
                <i class="fas fa-user-circle fa-2x me-2 text-secondary"></i>
                <div class="lh-1">
                    <strong class="d-block small"><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Petugas'); ?></strong>
                    <small class="text-muted" style="font-size: 0.7rem;">@<?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin'); ?></small>
                </div>
            </div>
            <a href="logout.php" class="btn btn-sm btn-outline-danger" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</div>