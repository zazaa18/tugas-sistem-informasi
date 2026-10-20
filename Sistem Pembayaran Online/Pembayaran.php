<?php
#Penggunaan Abstrak Class
abstract class Pembayaran {
    protected $jumlah;

    public function __construct($jumlah) {
        $this->jumlah = $jumlah;
    }

    // method wajib
    abstract public function prosesPembayaran();

    // method umum
    public function validasi() {
        return $this->jumlah > 0;
    }

    // diskon 10%
    public function hitungDiskon() {
        return $this->jumlah * 0.10;
    }

    // pajak 11% (setelah diskon)
    public function hitungPajak() {
        $setelahDiskon = $this->jumlah - $this->hitungDiskon();
        return $setelahDiskon * 0.11;
    }

    // total bayar akhir
    public function totalBayar() {
        return $this->jumlah - $this->hitungDiskon() + $this->hitungPajak();
    }
}
?>