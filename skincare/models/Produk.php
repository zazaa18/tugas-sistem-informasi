<?php
require_once __DIR__ . "/../config/Database.php";

class Produk {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function getAll() {
        $result = mysqli_query($this->conn, "SELECT * FROM produk");
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getById($id) {
        $result = mysqli_query($this->conn, "SELECT * FROM produk WHERE id=$id");
        return mysqli_fetch_assoc($result);
    }

    public function insert($data) {
        mysqli_query($this->conn, "INSERT INTO produk VALUES(
            null,
            '{$data['nama_item']}',
            '{$data['kategori']}',
            '{$data['harga']}',
            '{$data['stok']}'
        )");
    }

    public function update($data) {
        mysqli_query($this->conn, "UPDATE produk SET
            nama_item='{$data['nama_item']}',
            kategori='{$data['kategori']}',
            harga='{$data['harga']}',
            stok='{$data['stok']}'
            WHERE id={$data['id']}
        ");
    }

    public function delete($id) {
        mysqli_query($this->conn, "DELETE FROM produk WHERE id=$id");
    }
}