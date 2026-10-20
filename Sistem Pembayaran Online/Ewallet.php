<?php
require_once 'Pembayaran.php';
require_once 'Cetak.php';

#Penggunaan Class EWallet
class EWallet extends Pembayaran implements Cetak {

    public function prosesPembayaran() {
        if ($this->validasi()) {
            return "Pembayaran E-Wallet Rp {$this->jumlah} berhasil";
        }
        return "Jumlah tidak valid";
    }

    public function cetakStruk() {
        return "
        <b>Struk E-Wallet</b><br>
        Total Awal: Rp " . number_format($this->jumlah, 0, ',', '.') . " <br>
        Diskon (10%): Rp " . number_format($this->hitungDiskon(), 0, ',', '.') . " <br>
        Pajak (11%): Rp " . number_format($this->hitungPajak(), 0, ',', '.') . " <br>
        <b>Total Bayar: Rp " . number_format($this->totalBayar(), 0, ',', '.') . "</b>
        ";
    }
}
?>