<?php
require_once 'User.php';
require_once 'CetakLaporan.php';

class Mahasiswa extends User implements CetakLaporan {
    private $jurusan;
    private $semester;
    private $nilai = [];

    public function __construct($nim, $nama, $jurusan, $semester){
        parent::__construct($nim, $nama);
        $this->jurusan = $jurusan;
        $this->semester = $semester;
    }

    public function tambahNilai($mk, $nilai, $sks){
        $this->nilai[] = [
            'mk' => $mk,
            'nilai' => $nilai,
            'sks' => $sks
        ];
    }

    private function bobot($nilai){
        if($nilai >= 85) return 4;
        elseif($nilai >= 75) return 3;
        elseif($nilai >= 65) return 2;
        elseif($nilai >= 50) return 1;
        return 0;
    }

    public function hitungIPK(){
        $totalBobot = 0;
        $totalSks = 0;

        foreach($this->nilai as $n){
            $totalBobot += $this->bobot($n['nilai']) * $n['sks'];
            $totalSks += $n['sks'];
        }

        return $totalBobot / $totalSks;
    }

    public function infoUser(){
        return $this->nama;
    }

    public function cetak(){
        return $this->nilai;
    }

    public function getData(){
        return [
            'nim' => $this->id,
            'nama' => $this->nama,
            'jurusan' => $this->jurusan,
            'semester' => $this->semester,
            'ipk' => number_format($this->hitungIPK(), 2)
        ];
    }
}
?>