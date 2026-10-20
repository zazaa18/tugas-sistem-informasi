<?php
abstract class User {
    protected $id;
    protected $nama;

    public function __construct($id, $nama){
        $this->id = $id;
        $this->nama = $nama;
    }

    //   Getter untuk ID
    public function getId(){
        return $this->id;
    }

    //   Getter untuk Nama (opsional tapi bagus)
    public function getNama(){
        return $this->nama;
    }

    abstract public function infoUser();
}
?>