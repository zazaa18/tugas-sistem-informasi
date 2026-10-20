<?php 
include_once "controllers/MahasiswaController.php"; 
 
$controller = new MahasiswaController(); 
$data = $controller->model->getAll(); 
?> 
 
<h2>Data Mahasiswa</h2> 
 
<a href="views/tambah.php">Tambah Data</a> 
<br><br> 
 
<table border="1" cellpadding="10"> 
    <tr> 
        <th>No</th> 
        <th>Nama</th> 
        <th>Jurusan</th> 
        <th>Aksi</th> 
    </tr> 
 
<?php 
$no = 1; 
while($row = $data->fetch_assoc()) { 
?> 
<tr> 
    <td><?= $no++; ?></td> 
    <td><?= $row['nama']; ?></td> 
    <td><?= $row['jurusan']; ?></td> 
    <td> 
        <a href="views/edit.php?id=<?= $row['id']; ?>">Edit</a> 
        | 
        <a href="index.php?hapus=<?= $row['id']; ?>">Hapus</a> 
    </td> 
</tr> 
<?php } ?> 
 
</table> 