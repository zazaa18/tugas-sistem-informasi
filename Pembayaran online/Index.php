<?php
require_once 'TransferBank.php';
require_once 'EWallet.php';
require_once 'QRIS.php';
require_once 'COD.php';
require_once 'VirtualAccount.php';

// objek
$transfer = new TransferBank(100000);
$ewallet = new EWallet(50000);
$qris = new QRIS(75000);
$cod = new COD(60000);
$va = new VirtualAccount(80000);

// output Transfer Bank
echo $transfer->prosesPembayaran();
echo "<br>";
echo $transfer->cetakStruk();

echo "<hr>";

// output E-Wallet
echo $ewallet->prosesPembayaran();
echo "<br>";
echo $ewallet->cetakStruk();

echo "<hr>";

// output QRIS
echo $qris->prosesPembayaran();
echo "<br>";
echo $qris->cetakStruk();

echo "<hr>";

// output COD
echo $cod->prosesPembayaran();
echo "<br>";
echo $cod->cetakStruk();

echo "<hr>";

// output Virtual Account
echo $va->prosesPembayaran();
echo "<br>";
echo $va->cetakStruk();
?>