<?php
require_once __DIR__ . '/Pembayaran.php';

class COD extends Pembayaran {

    public function prosesPembayaran() {
        if (!$this->validasi()) {
            return "Jumlah pembayaran tidak valid!";
        }
        return "Pembayaran COD (Bayar di tempat) sebesar Rp " . number_format($this->jumlah, 0, ',', '.');
    }

    public function cetakStruk() {
        return "Struk COD: Rp " . number_format($this->jumlah, 0, ',', '.');
    }
}
?>