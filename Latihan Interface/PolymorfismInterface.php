<?php
$pembayaran = [
    new TransferBank(),
    new EWallet()
];

foreach ($pembayaran as $p) {
    echo $p->bayar(100000);
    echo "<br>";
}
?>