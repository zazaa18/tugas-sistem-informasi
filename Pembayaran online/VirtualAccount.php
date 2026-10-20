<?php
require_once __DIR__ . '/Pembayaran.php';

class VirtualAccount extends Pembayaran {

    public function prosesPembayaran() {
        if (!$this->validasi()) {
            return "Jumlah pembayaran tidak valid!";
        }
        return "Pembayaran via Virtual Account sebesar Rp " . number_format($this->jumlah, 0, ',', '.');
    }

    public function cetakStruk() {
        return "Struk Virtual Account: Rp " . number_format($this->jumlah, 0, ',', '.');
    }
}
?>