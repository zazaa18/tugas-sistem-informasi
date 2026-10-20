<?php 
 
include_once "controllers/MahasiswaController.php"; 
 
$controller = new MahasiswaController(); 
 
if(isset($_GET['hapus'])) { 
 
    $controller->model->delete($_GET['hapus']); 
 
    header("Location: index.php"); 
} 
 
include_once "views/list.php"; 
 
?>