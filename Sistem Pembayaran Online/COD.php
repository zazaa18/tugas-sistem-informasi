<?php
require_once __DIR__ . '/Pembayaran.php';
require_once 'Cetak.php';

class COD extends Pembayaran implements Cetak {

    public function prosesPembayaran() {
        if (!$this->validasi()) {
            return "Jumlah pembayaran tidak valid!";
        } 
        return "Pembayaran COD (Bayar di tempat) sebesar Rp " . number_format($this->jumlah, 0, ',', '.');
    }

    public function cetakStruk() {
        return "
        <b>Struk COD</b><br>
        Total Awal: Rp " . number_format($this->jumlah, 0, ',', '.') . " <br>
        Diskon (10%): Rp " . number_format($this->hitungDiskon(), 0, ',', '.') . " <br>
        Pajak (11%): Rp " . number_format($this->hitungPajak(), 0, ',', '.') . " <br>
        <b>Total Bayar: Rp " . number_format($this->totalBayar(), 0, ',', '.') . "</b>
        ";
    }
}
?>