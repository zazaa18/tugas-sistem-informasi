<?php 
 
class Mahasiswa { 
 
    private $conn; 
 
    public function __construct($db) { 
        $this->conn = $db; 
    } 
 
    public function getAll() { 
        return $this->conn->query("SELECT * FROM mahasiswa"); 
    } 
 
    public function getById($id) { 
        return $this->conn->query("SELECT * FROM mahasiswa WHERE id=$id"); 
    } 
 
    public function create($nama, $jurusan) { 
        return $this->conn->query("INSERT INTO mahasiswa(nama,jurusan) 
VALUES('$nama','$jurusan')"); 
    } 
 
    public function update($id, $nama, $jurusan) { 
        return $this->conn->query("UPDATE mahasiswa SET nama='$nama', 
jurusan='$jurusan' WHERE id=$id"); 
    } 
 
    public function delete($id) { 
        return $this->conn->query("DELETE FROM mahasiswa WHERE id=$id"); 
    } 
} 
?> 