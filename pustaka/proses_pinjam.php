<?php
session_start();
require_once 'config/database.php';

// Pastikan diakses melalui metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Ambil dan bersihkan data input dari form
$id_buku               = (int)($_POST['id_buku'] ?? 0);
$nama_peminjam         = trim($_POST['nama_peminjam'] ?? '');
$no_hp                 = trim($_POST['no_hp'] ?? '');
$tanggal_kembali_rencana = trim($_POST['tanggal_kembali_rencana'] ?? '');

// Validasi input wajib
if ($id_buku <= 0 || empty($nama_peminjam) || empty($no_hp) || empty($tanggal_kembali_rencana)) {
    $_SESSION['msg_error'] = "Semua kolom pada form peminjaman wajib diisi!";
    header("Location: index.php");
    exit;
}

// Cek apakah buku valid dan stok tersedia masih ada (> 0)
$stmtBuku = $pdo->prepare("SELECT * FROM buku WHERE id_buku = ?");
$stmtBuku->execute([$id_buku]);
$buku = $stmtBuku->fetch();

if (!$buku) {
    $_SESSION['msg_error'] = "Buku yang dipilih tidak ditemukan dalam sistem.";
    header("Location: index.php");
    exit;
}

if ($buku['stok_tersedia'] <= 0) {
    $_SESSION['msg_error'] = "Maaf, stok untuk buku '<strong>{$buku['judul_buku']}</strong>' saat ini sedang habis.";
    header("Location: index.php");
    exit;
}

// Validasi tanggal rencana kembali tidak boleh di masa lalu
$todayObj = new DateTime('today');
$dueObj   = new DateTime($tanggal_kembali_rencana);

if ($dueObj <= $todayObj) {
    $_SESSION['msg_error'] = "Tanggal pengembalian rencana minimal harus H+1 dari hari ini.";
    header("Location: index.php");
    exit;
}

// -------------------------------------------------------------
// GENERATE KODE TRANSAKSI UNIK (Contoh: TRX20261020XXXX)
// -------------------------------------------------------------
$kode_transaksi = 'TRX' . date('Ymd') . strtoupper(substr(uniqid(), -4));

// Tanggal pengajuan otomatis hari ini
$tanggal_pengajuan = date('Y-m-d H:i:s');
$status = 'Pending'; // Status awal menunggu persetujuan admin

try {
    // Simpan ke tabel peminjaman
    $stmtInsert = $pdo->prepare("
        INSERT INTO peminjaman (
            kode_transaksi, 
            id_buku, 
            nama_peminjam, 
            no_hp, 
            tanggal_pengajuan, 
            tanggal_kembali_rencana, 
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmtInsert->execute([
        $kode_transaksi,
        $id_buku,
        $nama_peminjam,
        $no_hp,
        $tanggal_pengajuan,
        $tanggal_kembali_rencana,
        $status
    ]);

    // Ambil ID peminjaman yang baru saja dimasukkan untuk dibawa ke halaman bukti
    $id_peminjaman = $pdo->lastInsertId();

    // Simpan data transaksi ke session sementara agar halaman bukti bisa menampilkannya
    $_SESSION['last_trx'] = [
        'id_peminjaman'          => $id_peminjaman,
        'kode_transaksi'         => $kode_transaksi,
        'nama_peminjam'          => $nama_peminjam,
        'no_hp'                  => $no_hp,
        'judul_buku'             => $buku['judul_buku'],
        'penulis'                => $buku['penulis'],
        'tanggal_pengajuan'      => $tanggal_pengajuan,
        'tanggal_kembali_rencana' => $tanggal_kembali_rencana,
        'status'                 => $status
    ];

    // Alihkan ke halaman bukti peminjaman
    header("Location: bukti-pinjam.php");
    exit;

} catch (Exception $e) {
    $_SESSION['msg_error'] = "Gagal memproses pengajuan peminjaman: " . $e->getMessage();
    header("Location: index.php");
    exit;
}
?>