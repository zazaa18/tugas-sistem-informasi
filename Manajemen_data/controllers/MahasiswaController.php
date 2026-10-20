<?php 

include_once dirname(__DIR__) . "/config/Database.php"; 
include_once dirname(__DIR__) . "/models/Mahasiswa.php"; 

class MahasiswaController { 

    public $model; 

    public function __construct() { 
        $database = new Database(); 
        $db = $database->connect(); 
        $this->model = new Mahasiswa($db); 
    } 

    
    public function index() {
        echo "Controller berhasil jalan";
    }
}
?>