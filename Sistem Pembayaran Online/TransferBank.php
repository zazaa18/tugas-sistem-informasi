<?php
require_once 'Pembayaran.php';
require_once 'Cetak.php';

#Penggunaan Class Transfer Bank dari Extend
class TransferBank extends Pembayaran implements Cetak {

    public function prosesPembayaran() {
        if ($this->validasi()) {
            return "Transfer Bank sebesar Rp {$this->jumlah}";
        }
        return "Jumlah tidak valid";
    }

    public function cetakStruk() {
        return "
        <b>Struk Transfer Bank</b><br>
        Total Awal: Rp {$this->jumlah} <br>
        Diskon (10%): Rp {$this->hitungDiskon()} <br>
        Pajak (11%): Rp {$this->hitungPajak()} <br>
        <b>Total Bayar: Rp {$this->totalBayar()}</b>
        ";
    }
}
?>