<?php

class QRIS {
    private $jumlah;

    public function __construct($jumlah) {
        $this->jumlah = $jumlah;
    }

    public function prosesPembayaran() {
        return "Pembayaran menggunakan QRIS sebesar Rp " . number_format($this->jumlah, 0, ',', '.');
    }

    public function cetakStruk() {
        return "Struk QRIS: Pembayaran berhasil sebesar Rp " . number_format($this->jumlah, 0, ',', '.');
    }
}

?>