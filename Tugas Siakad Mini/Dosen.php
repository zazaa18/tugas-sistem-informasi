<?php

class Dosen extends User implements CetakLaporan {
    private $mataKuliah;

    public function __construct($id, $nama, $mataKuliah) {
        parent::__construct($id, $nama);
        $this->mataKuliah = $mataKuliah;
    }

    public function infoUser() {
        return $this->nama;
    }

    public function cetak() {
        return "
            <p>NIDN : {$this->id}</p>
            <p>Nama Dosen : {$this->nama}</p>
            <p>Mata Kuliah : {$this->mataKuliah}</p>
        ";
    }
}
?>