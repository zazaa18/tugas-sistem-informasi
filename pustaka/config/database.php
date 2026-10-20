<?php
// Konfigurasi Database Pustaka
$host     = 'localhost';
$db_name  = 'pustaka_db';
$username = 'root';     // Sesuaikan dengan username database kamu (default XAMPP/Laragon: root)
$password = '';         // Sesuaikan dengan password database kamu (default XAMPP/Laragon: kosong)

try {
    // Membuat koneksi PDO
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db_name};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Menampilkan error jika terjadi masalah query
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mengembalikan data berupa array asosiatif
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Menggunakan prepared statements asli dari MySQL
        ]
    );
} catch (PDOException $e) {
    // Menampilkan pesan error jika koneksi gagal
    die("Koneksi ke database gagal: " . $e->getMessage());
}
?>