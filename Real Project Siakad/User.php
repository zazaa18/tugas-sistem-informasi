<?php
abstract class User {
    protected $nama;
    protected $nim;

    public function __construct($nama, $nim){
        $this->nama = $nama;
        $this->nim = $nim;
    }

    public function getNama(){
        return $this->nama;
    }

    public function getNim(){
        return $this->nim;
    }

    abstract public function getInfo();
}
?>