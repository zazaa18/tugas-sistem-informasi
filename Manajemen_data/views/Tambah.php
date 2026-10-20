<?php 
include_once "../controllers/MahasiswaController.php"; 
 
$controller = new MahasiswaController(); 
 
if(isset($_POST['simpan'])) { 
 
    $controller->model->create($_POST['nama'], $_POST['jurusan']); 
 
    header("Location: ../index.php"); 
} 
?> 
 
<h2>Tambah Data Mahasiswa</h2> 
 
<form method="POST"> 
 
Nama : 
<input type="text" name="nama"> 
<br><br> 
 
Jurusan : 
<input type="text" name="jurusan"> 
<br><br> 
 
<button type="submit" name="simpan">Simpan</button> 
 
</form> 