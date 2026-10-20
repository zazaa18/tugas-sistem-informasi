<?php
// Pastikan session aktif jika header dipanggil
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek Autentikasi Admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Menentukan halaman aktif untuk highlight menu navbar
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard Admin'; ?> - Pustaka</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

    <!-- Navbar Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 no-print">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-book-reader me-2"></i>Pustaka Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'pengajuan.php' ? 'active' : ''; ?>" href="pengajuan.php">
                            <i class="fas fa-clock me-1"></i> Pengajuan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'peminjaman.php' ? 'active' : ''; ?>" href="peminjaman.php">
                            <i class="fas fa-list-alt me-1"></i> Peminjaman Aktif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'pengembalian.php' ? 'active' : ''; ?>" href="pengembalian.php">
                            <i class="fas fa-history me-1"></i> Riwayat Pengembalian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'laporan.php' ? 'active' : ''; ?>" href="laporan.php">
                            <i class="fas fa-file-alt me-1"></i> Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'buku.php' ? 'active' : ''; ?>" href="buku.php">
                            <i class="fas fa-book me-1"></i> Data Buku
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'kategori.php' ? 'active' : ''; ?>" href="kategori.php">
                            <i class="fas fa-tags me-1"></i> Kategori
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center text-white">
                    <span class="me-3"><i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></span>
                    <a href="logout.php" class="btn btn-sm btn-outline-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>