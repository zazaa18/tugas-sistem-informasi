<?php
session_start();
require_once 'config/database.php';

$page_title = 'Katalog Buku';

// -------------------------------------------------------------
// FILTER & PENCARIAN
// -------------------------------------------------------------
$search      = trim($_GET['search'] ?? '');
$id_kategori = (int)($_GET['kategori'] ?? 0);

// Query Ambil Semua Kategori
$stmtKategori = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$listKategori = $stmtKategori->fetchAll();

// Query Ambil Daftar Buku
$sql = "
    SELECT b.*, k.nama_kategori 
    FROM buku b 
    JOIN kategori k ON b.id_kategori = k.id_kategori 
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $sql .= " AND (b.judul_buku LIKE ? OR b.penulis LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($id_kategori > 0) {
    $sql .= " AND b.id_kategori = ?";
    $params[] = $id_kategori;
}

$sql .= " ORDER BY b.id_buku DESC";

$stmtBuku = $pdo->prepare($sql);
$stmtBuku->execute($params);
$listBuku = $stmtBuku->fetchAll();

// Include Header Publik
include_once 'includes/header.php';
?>

<!-- Style Kustom UI/UX Katalog Modern -->
<style>
    :root {
        --card-radius: 18px;
    }
    
    .hero-banner {
        background: linear-gradient(135deg, #0d6efd 0%, #1a237e 100%);
        border-radius: var(--card-radius);
    }

    .book-card {
        border-radius: var(--card-radius) !important;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        background: #ffffff;
    }
    
    .book-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 35px rgba(13, 110, 253, 0.12) !important;
        border-color: rgba(13, 110, 253, 0.25) !important;
    }

    .cover-wrapper {
        height: 230px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        border-top-left-radius: var(--card-radius);
        border-top-right-radius: var(--card-radius);
    }

    .cover-img {
        height: 195px;
        width: 135px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.18);
        transition: transform 0.35s ease;
    }

    .book-card:hover .cover-img {
        transform: scale(1.06) rotate(1deg);
    }

    .cover-placeholder {
        width: 130px;
        height: 180px;
        border-radius: 10px;
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 2px dashed #90caf9;
        color: #1565c0;
        padding: 10px;
        text-align: center;
    }

    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .badge-category {
        font-size: 0.7rem;
        letter-spacing: 0.3px;
        font-weight: 600;
        padding: 0.4em 0.8em;
    }

    .badge-status {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.4em 0.8em;
        border-radius: 30px;
    }
</style>

<main class="container my-4">

    <!-- Hero Banner -->
    <div class="hero-banner p-4 p-md-5 mb-4 text-white shadow-sm position-relative overflow-hidden">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-md-8">
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm">
                    <i class="fas fa-book-open-reader me-1"></i> Perpustakaan Digital
                </span>
                <h1 class="display-6 fw-bold mb-2">Eksplorasi Koleksi Perpustakaan</h1>
                <p class="lead mb-4 opacity-90" style="font-size: 1.05rem;">Cari buku favoritmu, cek ketersediaan eksemplar secara akurat, dan ajukan peminjaman secara instan.</p>
                <a href="cek_status.php" class="btn btn-light text-primary fw-bold px-4 py-2 rounded-3 shadow-sm">
                    <i class="fas fa-search me-2"></i> Cek Status Peminjaman Saya
                </a>
            </div>
            <div class="col-md-4 text-center d-none d-md-block opacity-25">
                <i class="fas fa-books fa-10x"></i>
            </div>
        </div>
    </div>

    <!-- Alert Session Notification -->
    <?php if (isset($_SESSION['msg_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['msg_error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['msg_error']); ?>
    <?php endif; ?>

    <!-- Form Pencarian & Filter Kategori -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="index.php" class="row g-3">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0 ps-0" placeholder="Cari berdasarkan judul buku atau nama penulis..." value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="kategori" class="form-select bg-light border-0">
                        <option value="0">-- Semua Kategori --</option>
                        <?php foreach ($listKategori as $kat): ?>
                            <option value="<?= $kat['id_kategori']; ?>" <?= $id_kategori == $kat['id_kategori'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($kat['nama_kategori']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary rounded-3 fw-semibold">
                        <i class="fas fa-filter me-1"></i> Cari Buku
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Filter Aktif -->
    <?php if (!empty($search) || $id_kategori > 0): ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="text-muted small mb-0">
                Menampilkan hasil pencarian
                <?php if (!empty($search)): ?> untuk "<strong><?= htmlspecialchars($search); ?></strong>"<?php endif; ?>.
            </p>
            <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-pill"><i class="fas fa-times me-1"></i> Reset Filter</a>
        </div>
    <?php endif; ?>

    <!-- Grid Katalog Buku -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php if (empty($listBuku)): ?>
            <div class="col-12 w-100">
                <div class="text-center py-5 bg-white rounded-4 border">
                    <i class="fas fa-book-skull fa-3x text-muted mb-3 opacity-50"></i>
                    <h5 class="fw-semibold text-secondary">Buku Tidak Ditemukan</h5>
                    <p class="text-muted small">Coba cari dengan kata kunci lain atau pilih kategori yang berbeda.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($listBuku as $buku): ?>
                <?php 
                    // Logika Path Gambar (Memeriksa Folder assets/uploads/)
                    $imgPath = '';
                    if (!empty($buku['gambar'])) {
                        if (filter_var($buku['gambar'], FILTER_VALIDATE_URL)) {
                            $imgPath = $buku['gambar'];
                        } elseif (file_exists('assets/uploads/' . $buku['gambar'])) {
                            $imgPath = 'assets/uploads/' . $buku['gambar'];
                        }
                    }

                    // Angka Stok
                    $stokTersedia = (int)$buku['stok_tersedia'];
                    $stokTotal    = (int)$buku['stok_total'];
                ?>
                <div class="col">
                    <div class="card h-100 book-card shadow-sm overflow-hidden">
                        
                        <!-- Area Cover Buku -->
                        <div class="cover-wrapper border-bottom">
                            <?php if (!empty($imgPath)): ?>
                                <img src="<?= htmlspecialchars($imgPath); ?>" alt="Cover <?= htmlspecialchars($buku['judul_buku']); ?>" class="cover-img">
                            <?php else: ?>
                                <div class="cover-placeholder shadow-sm">
                                    <i class="fas fa-book fa-2x mb-2 opacity-75"></i>
                                    <small class="fw-bold lh-sm text-uppercase" style="font-size: 0.65rem;">
                                        <?= htmlspecialchars(mb_strimwidth($buku['judul_buku'], 0, 25, '...')); ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Area Detail Informasi -->
                        <div class="card-body d-flex flex-column p-3">
                            
                            <!-- Badges (Kategori & Status Stok) -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 rounded-pill badge-category text-truncate" style="max-width: 58%;">
                                    <?= htmlspecialchars($buku['nama_kategori']); ?>
                                </span>

                                <?php if ($stokTersedia > 2): ?>
                                    <span class="badge-status bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                        <i class="fas fa-circle-check me-1"></i>Tersedia
                                    </span>
                                <?php elseif ($stokTersedia > 0): ?>
                                    <span class="badge-status bg-warning bg-opacity-15 text-dark border border-warning border-opacity-50">
                                        <i class="fas fa-triangle-exclamation me-1"></i>Sisa <?= $stokTersedia; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge-status bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                        <i class="fas fa-circle-xmark me-1"></i>Habis
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Judul Buku -->
                            <h6 class="card-title fw-bold text-dark mb-1 text-truncate-2" style="min-height: 2.6rem; line-height: 1.3;" title="<?= htmlspecialchars($buku['judul_buku']); ?>">
                                <?= htmlspecialchars($buku['judul_buku']); ?>
                            </h6>
                            
                            <!-- Nama Penulis -->
                            <p class="card-text text-muted small mb-3">
                                <i class="fas fa-user-pen me-1 text-secondary"></i> <?= htmlspecialchars($buku['penulis']); ?>
                            </p>

                            <!-- Footer Kartu: Info Jumlah Stok & Tombol Pinjam -->
                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted d-block" style="font-size: 0.68rem;">Sisa Stok</span>
                                    <span class="fw-bold fs-6 <?= $stokTersedia > 0 ? 'text-dark' : 'text-danger'; ?>">
                                        <?= $stokTersedia; ?> <span class="text-muted fw-normal" style="font-size: 0.72rem;">/ <?= $stokTotal; ?> buku</span>
                                    </span>
                                </div>
                                
                                <div class="btn-group">
                                    <a href="detail_buku.php?id=<?= $buku['id_buku']; ?>" class="btn btn-sm btn-outline-secondary rounded-start-2" title="Detail Buku">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                    <?php if ($stokTersedia > 0): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-primary px-3 rounded-end-2 fw-semibold" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalPinjamUniversal"
                                                data-id="<?= $buku['id_buku']; ?>"
                                                data-judul="<?= htmlspecialchars($buku['judul_buku']); ?>">
                                            <i class="fas fa-hand-holding-hand me-1"></i> Pinjam
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary px-3 rounded-end-2" disabled>Habis</button>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</main>

<?php
// Include Modal Pinjam Universal
include_once 'includes/modal_pinjam.php';

// Include Footer Publik
include_once 'includes/footer.php';
?>

<!-- Link JS Kustom -->
<script src="assets/js/main.js"></script>