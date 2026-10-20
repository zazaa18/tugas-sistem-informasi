<!DOCTYPE html>
<html>
<head>
    <title>Edit Produk</title>
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
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
        }

        button:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Edit Produk</h2>

    <form method="POST" action="../controllers/ProdukController.php?action=update">
        <input type="hidden" name="id" value="<?= $data['id']; ?>">

        <input type="text" name="nama_item" value="<?= $data['nama_item']; ?>">
        <input type="text" name="kategori" value="<?= $data['kategori']; ?>">
        <input type="number" name="harga" value="<?= $data['harga']; ?>">
        <input type="number" name="stok" value="<?= $data['stok']; ?>">

        <button type="submit">Update</button>
    </form>
</div>

</body>
</html>