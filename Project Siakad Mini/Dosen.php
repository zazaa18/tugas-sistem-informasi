<?php
require_once "User.php";
require_once "Cetak.php";

class Dosen extends User implements CetakLaporan {
    private $mataKuliah;

    public function __construct($id, $nama, $mataKuliah = "-") {
        parent::__construct($id, $nama);
        $this->mataKuliah = $mataKuliah;
    }

    public function getMataKuliah() {
        return $this->mataKuliah;
    }

    public function infoUser() {
        return $this->nama;
    }

    public function cetakLaporan() {
        return "
            <div style='margin-top:20px'>
                <p><b>Dosen Wali</b></p>
                <p>NIDN : {$this->id}</p>
                <p>Nama : {$this->nama}</p>
                <p>Mata Kuliah : {$this->mataKuliah}</p>
            </div>
        ";
    }
}
?>