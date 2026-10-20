<?php
session_start();
require_once '../config/database.php';

// Autentikasi Admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// -------------------------------------------------------------
// PROCESS: TAMBAH KATEGORI
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nama_kategori = trim($_POST['nama_kategori']);

    if (!empty($nama_kategori)) {
        $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
        if ($stmt->execute([$nama_kategori])) {
            $_SESSION['msg_success'] = "Kategori '$nama_kategori' berhasil ditambahkan!";
        } else {
            $_SESSION['msg_error'] = "Gagal menambahkan kategori.";
        }
    } else {
        $_SESSION['msg_error'] = "Nama kategori tidak boleh kosong.";
    }
    header("Location: kategori.php");
    exit;
}

// -------------------------------------------------------------
// PROCESS: EDIT KATEGORI
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id_kategori   = (int)$_POST['id_kategori'];
    $nama_kategori = trim($_POST['nama_kategori']);

    if ($id_kategori > 0 && !empty($nama_kategori)) {
        $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = ? WHERE id_kategori = ?");
        if ($stmt->execute([$nama_kategori, $id_kategori])) {
            $_SESSION['msg_success'] = "Nama kategori berhasil diperbarui!";
        } else {
            $_SESSION['msg_error'] = "Gagal mengupdate kategori.";
        }
    }
    header("Location: kategori.php");
    exit;
}

// -------------------------------------------------------------
// PROCESS: HAPUS KATEGORI
// -------------------------------------------------------------
if (isset($_GET['delete'])) {
    $id_kategori = (int)$_GET['delete'];
    
    // Cek apakah ada buku yang masih terikat dengan kategori ini
    $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM buku WHERE id_kategori = ?");
    $stmtCek->execute([$id_kategori]);
    $jumlahBuku = $stmtCek->fetchColumn();

    if ($jumlahBuku > 0) {
        $_SESSION['msg_error'] = "Gagal menghapus! Kategori ini masih memiliki $jumlahBuku buku terhubung.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM kategori WHERE id_kategori = ?");
        if ($stmt->execute([$id_kategori])) {
            $_SESSION['msg_success'] = "Kategori berhasil dihapus.";
        } else {
            $_SESSION['msg_error'] = "Gagal menghapus kategori.";
        }
    }
    header("Location: kategori.php");
    exit;
}

// FETCH SEMUA KATEGORI BESERTA JUMLAH BUKU TERHUBUNG
$kategoriList = $pdo->query("
    SELECT k.*, COUNT(b.id_buku) as total_buku 
    FROM kategori k 
    LEFT JOIN buku b ON k.id_kategori = b.id_kategori 
    GROUP BY k.id_kategori 
    ORDER BY k.id_kategori DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori Buku - Pustaka Admin</title>
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
                    <li class="nav-item"><a class="nav-link" href="buku.php"><i class="fas fa-book me-1"></i> Data Buku</a></li>
                    <li class="nav-item"><a class="nav-link active" href="kategori.php"><i class="fas fa-tags me-1"></i> Kategori</a></li>
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-0"><i class="fas fa-tags text-primary me-2"></i>Kelola Kategori Buku</h4>
                <p class="text-muted small mb-0">Kelola kelompok & klasifikasi buku untuk filter katalog publik</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                <i class="fas fa-plus-circle me-1"></i> Tambah Kategori
            </button>
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

        <!-- Table Data Kategori -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Kategori</th>
                                <th class="text-center">Jumlah Koleksi Buku</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($kategoriList)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada data kategori.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($kategoriList as $kat): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($kat['nama_kategori']); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?= $kat['total_buku']; ?> Buku</span>
                                        </td>
                                        <td class="text-center">
                                            <!-- Button Edit Modal Trigger -->
                                            <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEditKategori<?= $kat['id_kategori']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <!-- Button Delete -->
                                            <a href="kategori.php?delete=<?= $kat['id_kategori']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- MODAL EDIT KATEGORI -->
                                    <div class="modal fade" id="modalEditKategori<?= $kat['id_kategori']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="kategori.php">
                                                    <input type="hidden" name="action" value="edit">
                                                    <input type="hidden" name="id_kategori" value="<?= $kat['id_kategori']; ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Edit Kategori</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Nama Kategori</label>
                                                            <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($kat['nama_kategori']); ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END MODAL EDIT -->

                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL TAMBAH KATEGORI -->
    <div class="modal fade" id="modalTambahKategori" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="kategori.php">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Teknologi, Novel, Sains" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>