<?php
session_start();
require_once 'config/database.php';

$page_title = 'Detail Buku';

// Ambil ID Buku dari URL
$id_buku = (int)($_GET['id'] ?? 0);

if ($id_buku <= 0) {
    header("Location: index.php");
    exit;
}

// Query Ambil Detail Buku beserta Nama Kategori
$sql = "
    SELECT b.*, k.nama_kategori 
    FROM buku b 
    JOIN kategori k ON b.id_kategori = k.id_kategori 
    WHERE b.id_buku = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_buku]);
$buku = $stmt->fetch();

// Jika buku tidak ditemukan
if (!$buku) {
    header("Location: index.php");
    exit;
}

// -------------------------------------------------------------
// LOGIKA AUTO-DETECT PATH GAMBAR BUKU
// -------------------------------------------------------------
$imgPath = '';
$namaGambar = trim($buku['gambar'] ?? '');

if (!empty($namaGambar)) {
    if (filter_var($namaGambar, FILTER_VALIDATE_URL)) {
        $imgPath = $namaGambar;
    } else {
        $cleanFileName = basename($namaGambar);
        $possiblePaths = [
            'assets/uploads/' . $cleanFileName,
            'assets/img/buku/' . $cleanFileName,
            'uploads/' . $cleanFileName,
            $namaGambar
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists(__DIR__ . '/' . $path)) {
                $imgPath = $path;
                break;
            }
        }
    }
}

// Angka Stok
$stokTersedia = (int)$buku['stok_tersedia'];
$stokTotal    = (int)$buku['stok_total'];

// Include Header Publik
include_once 'includes/header.php';
?>

<!-- Style Kustom Khusus Halaman Detail Buku -->
<style>
    .detail-card {
        border-radius: 20px !important;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        background: #ffffff;
    }
    
    .cover-box {
        background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 360px;
        border: 1px solid #eaeaea;
    }

    .cover-img-detail {
        max-height: 320px;
        width: 210px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 10px 24px rgba(0,0,0,0.15);
        transition: transform 0.3s ease;
    }

    .cover-img-detail:hover {
        transform: scale(1.02);
    }

    .info-box {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid #eee;
        height: 100%;
    }

    .btn-pinjam-custom {
        background-color: #0d6efd;
        color: #ffffff;
        border: none;
        padding: 12px 20px;
        font-weight: 600;
        font-size: 0.95rem;
        border-radius: 12px;
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .btn-pinjam-custom:hover {
        background-color: #0b5ed7;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(13, 110, 253, 0.25);
    }

    .badge-category-detail {
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.5em 1em;
        border-radius: 30px;
    }

    .badge-status-detail {
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.5em 1em;
        border-radius: 30px;
    }
</style>

<main class="container my-4 my-lg-5">

    <!-- Navigasi Kembali -->
    <div class="mb-4">
        <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Katalog
        </a>
    </div>

    <!-- Card Utama Detail Buku -->
    <div class="card detail-card shadow-sm overflow-hidden">
        <div class="card-body p-4 p-md-5">
            <div class="row g-4 g-lg-5 align-items-center">
                
                <!-- Kolom Foto Cover -->
                <div class="col-md-5 col-lg-4 text-center">
                    <div class="cover-box shadow-sm">
                        <?php if (!empty($imgPath)): ?>
                            <img src="<?= htmlspecialchars($imgPath); ?>" alt="Cover <?= htmlspecialchars($buku['judul_buku']); ?>" class="cover-img-detail">
                        <?php else: ?>
                            <div class="text-secondary py-4">
                                <div class="bg-white p-3 rounded-circle shadow-sm d-inline-block mb-3">
                                    <i class="fas fa-book-open fa-3x text-primary opacity-75"></i>
                                </div>
                                <div class="small fw-semibold text-muted">Sampul Belum Tersedia</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Kolom Detail Informasi -->
                <div class="col-md-7 col-lg-8">
                    
                    <!-- Kategori & Badge Ketersediaan -->
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 badge-category-detail">
                            <i class="fas fa-bookmark me-1"></i> <?= htmlspecialchars($buku['nama_kategori']); ?>
                        </span>

                        <?php if ($stokTersedia > 2): ?>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 badge-status-detail">
                                <i class="fas fa-circle-check me-1"></i> Tersedia (<?= $stokTersedia; ?>)
                            </span>
                        <?php elseif ($stokTersedia > 0): ?>
                            <span class="badge bg-warning bg-opacity-15 text-dark border border-warning border-opacity-50 badge-status-detail">
                                <i class="fas fa-triangle-exclamation me-1"></i> Stok Sisa <?= $stokTersedia; ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 badge-status-detail">
                                <i class="fas fa-circle-xmark me-1"></i> Stok Habis
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Judul Buku -->
                    <h2 class="fw-bold text-dark mb-4 lh-sm"><?= htmlspecialchars($buku['judul_buku']); ?></h2>

                    <!-- Baris Sejajar: Penulis, Penerbit, dan Tombol Pinjam -->
                    <div class="row g-3 mb-4 align-items-stretch">
                        <div class="col-md-4 col-sm-6">
                            <div class="info-box d-flex align-items-center">
                                <div class="bg-white p-2.5 rounded-3 me-3 text-primary shadow-sm border">
                                    <i class="fas fa-user-pen fa-lg"></i>
                                </div>
                                <div>
                                    <span class="d-block text-muted text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Penulis</span>
                                    <strong class="text-dark fs-6"><?= htmlspecialchars($buku['penulis']); ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="info-box d-flex align-items-center">
                                <div class="bg-white p-2.5 rounded-3 me-3 text-primary shadow-sm border">
                                    <i class="fas fa-building fa-lg"></i>
                                </div>
                                <div>
                                    <span class="d-block text-muted text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Penerbit</span>
                                    <strong class="text-dark fs-6"><?= htmlspecialchars($buku['penerbit'] ?? 'Tidak Diketahui'); ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <?php if ($stokTersedia > 0): ?>
                                <button type="button" 
                                        class="btn btn-pinjam-custom shadow-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalPinjamUniversal"
                                        data-id="<?= $buku['id_buku']; ?>"
                                        data-judul="<?= htmlspecialchars($buku['judul_buku']); ?>">
                                    <i class="fas fa-hand-holding-hand me-2"></i> Ajukan Peminjaman
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100 h-100 rounded-3 fw-semibold" disabled>
                                    <i class="fas fa-ban me-2"></i> Stok Habis
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <!-- Info Stok Eksemplar -->
                    <div>
                        <span class="text-muted d-block small mb-1" style="font-size: 0.75rem;">Informasi Ketersediaan</span>
                        <span class="fw-bold fs-4 text-dark">
                            <span class="<?= $stokTersedia > 0 ? 'text-primary' : 'text-danger'; ?>"><?= $stokTersedia; ?></span>
                            <span class="text-muted fs-6 fw-normal"> / <?= $stokTotal; ?> Eksemplar</span>
                        </span>
                    </div>

                </div>
            </div>
        </div>
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