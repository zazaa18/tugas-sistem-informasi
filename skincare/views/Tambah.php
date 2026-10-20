<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk</title>
    <style>
        body {
            font-family: 'Segoe UI';
            background: #f9fafb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 350px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #ec4899;
            color: white;
            border: none;
            border-radius: 8px;
        }

        button:hover {
            background: #db2777;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Tambah Produk</h2>

    <form method="POST" action="../controllers/ProdukController.php?action=store">
        <input type="text" name="nama_item" placeholder="Nama Item">
        <input type="text" name="kategori" placeholder="Kategori">
        <input type="number" name="harga" placeholder="Harga">
        <input type="number" name="stok" placeholder="Stok">

        <button type="submit">Simpan</button>
    </form>
</div>

</body>
</html>