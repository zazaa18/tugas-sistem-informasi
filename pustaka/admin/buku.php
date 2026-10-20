
<?php
session_start();
require_once '../config/database.php';

// Autentikasi Admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// -------------------------------------------------------------
// FUNGSI UPLOAD GAMBAR BUKU (tambahan)
// -------------------------------------------------------------
function uploadGambarBuku($file)
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2 * 1024 * 1024) {
        return false;
    }

    $infoGambar = @getimagesize($file['tmp_name']);
    if ($infoGambar === false) {
        return false;
    }

    $ekstensiValid = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];

    if (!isset($ekstensiValid[$infoGambar['mime']])) {
        return false;
    }

    $folderUpload = '../assets/uploads/';

    if (!is_dir($folderUpload) && !mkdir($folderUpload, 0775, true)) {
        return false;
    }

    $namaFile = uniqid('buku_', true) . '.' . $ekstensiValid[$infoGambar['mime']];

    if (move_uploaded_file($file['tmp_name'], $folderUpload . $namaFile)) {
        return $namaFile;
    }

    return false;
}

// -------------------------------------------------------------
// PROCESS: TAMBAH BUKU
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $judul_buku  = trim($_POST['judul_buku']);
    $id_kategori = (int)$_POST['id_kategori'];
    $penulis     = trim($_POST['penulis']);
    $penerbit    = trim($_POST['penerbit']);
    $stok_total  = (int)$_POST['stok_total'];

    $gambar = uploadGambarBuku($_FILES['gambar'] ?? null);

    if ($gambar === false) {
        $_SESSION['msg_error'] = "Gagal mengunggah gambar. Gunakan JPG, PNG, GIF, atau WEBP maksimal 2 MB.";
    } elseif (!empty($judul_buku) && $id_kategori > 0 && $stok_total > 0) {
        $stmt = $pdo->prepare("INSERT INTO buku (id_kategori, judul_buku, penulis, penerbit, stok_total, stok_tersedia, gambar) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Stok tersedia disamakan dengan stok total saat buku ditambahkan
        if ($stmt->execute([$id_kategori, $judul_buku, $penulis, $penerbit, $stok_total, $stok_total, $gambar])) {
            $_SESSION['msg_success'] = "Buku '$judul_buku' berhasil ditambahkan!";
        } else {
            $_SESSION['msg_error'] = "Gagal menambahkan buku.";
        }
    } else {
        $_SESSION['msg_error'] = "Mohon isi semua data wajib dengan benar.";
    }

    header("Location: buku.php");
    exit;
}

// -------------------------------------------------------------
// PROCESS: EDIT BUKU
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id_buku     = (int)$_POST['id_buku'];
    $judul_buku  = trim($_POST['judul_buku']);
    $id_kategori = (int)$_POST['id_kategori'];
    $penulis     = trim($_POST['penulis']);
    $penerbit    = trim($_POST['penerbit']);
    $stok_total  = (int)$_POST['stok_total'];

    $gambarBaru = uploadGambarBuku($_FILES['gambar'] ?? null);

    // Ambil data lama untuk kalkulasi stok dan gambar
    $stmtOld = $pdo->prepare("SELECT stok_total, stok_tersedia, gambar FROM buku WHERE id_buku = ?");
    $stmtOld->execute([$id_buku]);
    $oldData = $stmtOld->fetch();

    if ($gambarBaru === false) {
        $_SESSION['msg_error'] = "Gagal mengunggah gambar. Gunakan JPG, PNG, GIF, atau WEBP maksimal 2 MB.";
    } elseif ($oldData) {
        $selisihStok = $stok_total - $oldData['stok_total'];
        $stok_tersedia_baru = $oldData['stok_tersedia'] + $selisihStok;

        // Jika tidak memilih gambar baru, gunakan gambar lama
        $gambar = ($gambarBaru === null) ? $oldData['gambar'] : $gambarBaru;

        if ($stok_tersedia_baru < 0) {
            $_SESSION['msg_error'] = "Stok total tidak boleh lebih kecil dari jumlah buku yang sedang dipinjam!";
        } else {
            $stmt = $pdo->prepare("UPDATE buku SET id_kategori = ?, judul_buku = ?, penulis = ?, penerbit = ?, stok_total = ?, stok_tersedia = ?, gambar = ? WHERE id_buku = ?");

            if ($stmt->execute([$id_kategori, $judul_buku, $penulis, $penerbit, $stok_total, $stok_tersedia_baru, $gambar, $id_buku])) {
                $_SESSION['msg_success'] = "Data buku berhasil diperbarui!";
            } else {
                $_SESSION['msg_error'] = "Gagal mengurutkan update buku.";
            }
        }
    }

    header("Location: buku.php");
    exit;
}

// -------------------------------------------------------------
// PROCESS: HAPUS BUKU
// -------------------------------------------------------------
if (isset($_GET['delete'])) {
    $id_buku = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM buku WHERE id_buku = ?");

    if ($stmt->execute([$id_buku])) {
        $_SESSION['msg_success'] = "Buku berhasil dihapus dari katalog.";
    } else {
        $_SESSION['msg_error'] = "Gagal menghapus buku.";
    }

    header("Location: buku.php");
    exit;
}

// FETCH KATEGORI UNTUK DROPDOWN
$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori ASC")->fetchAll();

// FETCH DATA BUKU BESERTA NAMA KATEGORI
$bukuList = $pdo->query("
    SELECT b.*, k.nama_kategori
    FROM buku b
    JOIN kategori k ON b.id_kategori = k.id_kategori
    ORDER BY b.id_buku DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Buku - Pustaka Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <li class="nav-item"><a class="nav-link active" href="buku.php"><i class="fas fa-book me-1"></i> Data Buku</a></li>
                    <li class="nav-item"><a class="nav-link" href="kategori.php"><i class="fas fa-tags me-1"></i> Kategori</a></li>
                </ul>
                <div class="d-flex align-items-center text-white">
                    <a href="dashboard.php" class="text-white text-decoration-none me-3"><i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></a>
                    <a href="logout.php" class="btn btn-sm btn-outline-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5">

        <!-- Header Page -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-0"><i class="fas fa-book text-primary me-2"></i>Kelola Data Buku</h4>
                <p class="text-muted small mb-0">Tambah, ubah, dan hapus koleksi buku di perpustakaan</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahBuku">
                <i class="fas fa-plus-circle me-1"></i> Tambah Buku Baru
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

        <!-- Table Data Buku -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th class="text-center" style="width: 100px;">Sampul</th>
                                <th>Judul Buku</th>
                                <th>Kategori</th>
                                <th>Penulis / Penerbit</th>
                                <th class="text-center">Stok Total</th>
                                <th class="text-center">Stok Tersedia</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bukuList)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data buku.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($bukuList as $buku): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($buku['gambar'])): ?>
                                                <img src="../assets/uploads/<?= htmlspecialchars($buku['gambar']); ?>" alt="Sampul buku" style="width: 55px; height: 75px; object-fit: cover;" class="rounded border">
                                            <?php else: ?>
                                                <span class="text-muted small">Belum ada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($buku['judul_buku']); ?></td>
                                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($buku['nama_kategori']); ?></span></td>
                                        <td class="small">
                                            <strong><?= htmlspecialchars($buku['penulis']); ?></strong><br>
                                            <span class="text-muted"><?= htmlspecialchars($buku['penerbit']); ?></span>
                                        </td>
                                        <td class="text-center fw-bold"><?= $buku['stok_total']; ?></td>
                                        <td class="text-center">
                                            <?php if ($buku['stok_tersedia'] > 0): ?>
                                                <span class="badge bg-success"><?= $buku['stok_tersedia']; ?> Tersedia</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Habis</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEditBuku<?= $buku['id_buku']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="buku.php?delete=<?= $buku['id_buku']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- MODAL EDIT BUKU -->
                                    <div class="modal fade" id="modalEditBuku<?= $buku['id_buku']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="buku.php" enctype="multipart/form-data">
                                                    <input type="hidden" name="action" value="edit">
                                                    <input type="hidden" name="id_buku" value="<?= $buku['id_buku']; ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Edit Data Buku</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Judul Buku</label>
                                                            <input type="text" name="judul_buku" class="form-control" value="<?= htmlspecialchars($buku['judul_buku']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Kategori</label>
                                                            <select name="id_kategori" class="form-select" required>
                                                                <?php foreach ($kategoriList as $kat): ?>
                                                                    <option value="<?= $kat['id_kategori']; ?>" <?= $kat['id_kategori'] == $buku['id_kategori'] ? 'selected' : ''; ?>>
                                                                        <?= htmlspecialchars($kat['nama_kategori']); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Penulis</label>
                                                            <input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($buku['penulis']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Penerbit</label>
                                                            <input type="text" name="penerbit" class="form-control" value="<?= htmlspecialchars($buku['penerbit']); ?>">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Stok Total</label>
                                                            <input type="number" name="stok_total" class="form-control" value="<?= $buku['stok_total']; ?>" min="1" required>
                                                            <small class="text-muted fs-7">*Stok tersedia akan menyesuaikan perubahan stok total.</small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Sampul Buku</label>
                                                            <?php if (!empty($buku['gambar'])): ?>
                                                                <div class="mb-2">
                                                                    <img src="../assets/uploads/<?= htmlspecialchars($buku['gambar']); ?>" alt="Sampul buku saat ini" style="width: 75px; height: 100px; object-fit: cover;" class="rounded border">
                                                                </div>
                                                            <?php endif; ?>
                                                            <input type="file" name="gambar" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                                                            <small class="text-muted">Opsional. JPG, PNG, GIF, atau WEBP maksimal 2 MB. Kosongkan jika tidak ingin mengganti gambar.</small>
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

    <!-- MODAL TAMBAH BUKU -->
    <div class="modal fade" id="modalTambahBuku" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="buku.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Tambah Buku Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Buku</label>
                            <input type="text" name="judul_buku" class="form-control" placeholder="Masukkan judul buku" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="id_kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategoriList as $kat): ?>
                                    <option value="<?= $kat['id_kategori']; ?>"><?= htmlspecialchars($kat['nama_kategori']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Penulis</label>
                            <input type="text" name="penulis" class="form-control" placeholder="Nama penulis" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Penerbit</label>
                            <input type="text" name="penerbit" class="form-control" placeholder="Nama penerbit">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Stok Total</label>
                            <input type="number" name="stok_total" class="form-control" value="10" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sampul Buku</label>
                            <input type="file" name="gambar" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                            <small class="text-muted">Opsional. JPG, PNG, GIF, atau WEBP maksimal 2 MB.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah Buku</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
