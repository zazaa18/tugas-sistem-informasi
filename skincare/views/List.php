<!DOCTYPE html>
<html>
<head>
    <title>Data Skincare</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #f9fafb;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 950px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-size: 14px;
        }

        .btn-add {
            background: #ec4899;
        }

        .btn-add:hover {
            background: #db2777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 10px;
        }

        thead {
            background: #f3f4f6;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            color: #555;
            font-weight: 600;
        }

        tbody tr {
            border-bottom: 1px solid #eee;
        }

        tbody tr:hover {
            background: #fdf2f8;
        }

        .action a {
            margin: 0 5px;
            font-weight: 500;
            text-decoration: none;
        }

        .edit {
            color: #3b82f6;
        }

        .delete {
            color: #ef4444;
        }

        .price {
            font-weight: bold;
            color: #16a34a;
        }

    </style>
</head>
<body>

<div class="container">

    <div class="top-bar">
        <h2> Data Produk Skincare</h2>
        <a href="controllers/ProdukController.php?action=tambah" class="btn btn-add">+ Tambah</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Item</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php $no = 1; ?>
            <?php foreach($data as $item): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $item['nama_item']; ?></td>
                <td><?= $item['kategori']; ?></td>
                <td class="price">Rp <?= number_format($item['harga'],0,',','.'); ?></td>
                <td><?= $item['stok']; ?></td>
                <td class="action">
                    <a class="edit" href="controllers/ProdukController.php?action=edit&id=<?= $item['id']; ?>">Edit</a>
                    |
                    <a class="delete" href="controllers/ProdukController.php?action=delete&id=<?= $item['id']; ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

</body>
</html>