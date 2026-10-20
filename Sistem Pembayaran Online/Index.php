<?php
require_once 'TransferBank.php';
require_once 'EWallet.php';
require_once 'QRIS.php';
require_once 'COD.php';
require_once 'VirtualAccount.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simulasi Pembayaran</title>
</head>
<body style="font-family: Arial;">

    <h2>Simulasi Pembayaran</h2>

    <!-- FORM INPUT -->
    <form method="POST">
        <label>Jumlah Pembayaran:</label><br>
        <input type="number" name="jumlah" required><br><br>

        <label>Pilih Metode:</label><br>
        <select name="metode" required>
            <option value="transfer">Transfer Bank</option>
            <option value="ewallet">E-Wallet</option>
            <option value="qris">QRIS</option>
            <option value="cod">COD</option>
            <option value="va">Virtual Account</option>
        </select><br><br>

        <button type="submit" name="submit">Proses</button>
    </form>

    <hr>

    <?php
    // PROSES DATA
    if (isset($_POST['submit'])) {
        $jumlah = $_POST['jumlah'];
        $metode = $_POST['metode'];

        switch ($metode) {
            case 'transfer':
                $bayar = new TransferBank($jumlah);
                break;
            case 'ewallet':
                $bayar = new EWallet($jumlah);
                break;
            case 'qris':
                $bayar = new QRIS($jumlah);
                break;
            case 'cod':
                $bayar = new COD($jumlah);
                break;
            case 'va':
                $bayar = new VirtualAccount($jumlah);
                break;
        }

        echo "<h3>Hasil Pembayaran</h3>";
        echo $bayar->prosesPembayaran();
        echo "<br><br>";
        echo $bayar->cetakStruk();
    }
    ?>

</body>
</html>