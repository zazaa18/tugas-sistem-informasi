<?php 
include_once "../controllers/MahasiswaController.php"; 
 
$controller = new MahasiswaController(); 
 
$id = $_GET['id']; 
 
$data = $controller->model->getById($id); 
$row = $data->fetch_assoc(); 
 
if(isset($_POST['update'])) { 
 
    $controller->model->update( 
        $id, 
        $_POST['nama'], 
        $_POST['jurusan'] 
    ); 
 
    header("Location: ../index.php"); 
} 
?> 
 
<h2>Edit Data Mahasiswa</h2> 
 
<form method="POST"> 
 
Nama : 
<input type="text" name="nama" value="<?= $row['nama']; ?>"> 
<br><br> 
 
Jurusan : 
<input type="text" name="jurusan" value="<?= $row['jurusan']; ?>"> 
<br><br> 
 
<button type="submit" name="update">Update</button> 
 
</form> 