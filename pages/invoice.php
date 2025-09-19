<?php
session_start();
include("../service/db.php");

// cek login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// ambil id transaksi dari URL
if (!isset($_GET['id_transaction'])) {
    die("ID transaksi tidak ditemukan.");
}
$id_transaksi = (int) $_GET['id_transaction'];

// ambil data transaksi + user
$sql_transaksi = "
    SELECT 
        t.id_transaction, 
        t.id_user, 
        t.total_price, 
        t.date, 
        u.username, 
        u.email
    FROM tb_transaksi t
    JOIN users u ON t.id_user = u.id
    WHERE t.id_transaction = '$id_transaksi'
";
$result_transaksi = mysqli_query($conn, $sql_transaksi);

if (!$result_transaksi || mysqli_num_rows($result_transaksi) === 0) {
    die("Data transaksi tidak ditemukan.");
}

$transaksi = mysqli_fetch_assoc($result_transaksi);

// ambil detail produk
$sql_detail = "
    SELECT 
        d.amount, 
        p.name, 
        p.price, 
        p.photo
    FROM tb_detail d
    JOIN tb_produk p ON d.id_product = p.id
    WHERE d.id_transaction = '$id_transaksi'
";
$result_detail = mysqli_query($conn, $sql_detail);

function rupiah($angka) {
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice #<?= $id_transaksi ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
<style>
   body { 
    font-family: 'Poppins', sans-serif; 
    margin: 20px; 
    background: #f5f5f5; 
}
.invoice-box { 
    max-width: 850px; 
    margin: auto; 
    border: 1px solid #ddd; 
    background: #fff; 
    padding: 25px; 
    border-radius: 12px; 
    box-shadow: 0 4px 10px rgba(0,0,0,0.08); 
}
h2, h3 { 
    margin: 0 0 10px; 
    color: #333;
}
table { 
    width: 100%; 
    border-collapse: collapse; 
    margin-top: 20px; 
    font-size: 14px;
}
table, th, td { 
    border: 1px solid #ccc; 
    padding: 10px; 
    text-align: center; 
}
th { 
    background: #e0e0e0; 
    font-weight: 600; 
    color: #222;
}
td { 
    background: #fafafa; 
    color: #444;
}
.total { 
    font-weight: bold; 
    background: #f0f0f0; 
    color: #000;
}
.btn-print, 
.btn-home { 
    margin-top: 20px; 
    padding: 10px 20px; 
    border: none; 
    cursor: pointer; 
    border-radius: 6px; 
    font-size: 14px; 
    transition: 0.3s;
}
.btn-print { 
    background: #444; 
    color: #fff; 
}
.btn-print:hover { 
    background: #666; 
}
.btn-home { 
    background: #888; 
    color: #fff; 
    margin-right: 10px; 
}
.btn-home:hover { 
    background: #666; 
}
.product-photo { 
    max-width: 70px; 
    max-height: 70px; 
    object-fit: cover; 
    border-radius: 6px; 
    border: 1px solid #ccc;
}

</style>
</head>
<body>

<div class="invoice-box">
    <div class="header" style="display:flex; align-items:center; margin-bottom:20px; border-bottom:2px solid #333; padding-bottom:10px;">
        <!-- <img src="img/logoweb.png" alt="Logo Toko" style="max-height:70px; margin-right:20px;"> -->
        <div>
            <h2 style="margin:0;">Gifaattire</h2>
            <p style="margin:0; font-size:14px; color:#555;">
               Jl. Banjarwangunan Blok Seda RT 01 RW 02 no.29, Banjarwangunan, Kec. Mundu, Kabupaten Cirebon, Jawa Barat 45173<br>
                Telp: 083120442710 | Email: gifaattire@gmail.com
            </p>
        </div>
    </div>

    <h2>Invoice #<?= $id_transaksi ?></h2>
    <p><b>Tanggal:</b> <?= $transaksi['date'] ?></p>
    <p><b>Nama Pemesan:</b> <?= htmlspecialchars($transaksi['username']) ?></p>
    <p><b>Email:</b> <?= htmlspecialchars($transaksi['email']) ?></p>

    <h3>Detail Pesanan</h3>
    <table>
        <tr>
            <th>Photo </th>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
        <?php 
        $grand_total = 0;
        while ($row = mysqli_fetch_assoc($result_detail)): 
            $subtotal = $row['price'] * $row['amount'];
            $grand_total += $subtotal;
        ?>
        <tr>
            <td>
                <?php if (!empty($row['photo'])): ?>
                    <img src="../images/<?= htmlspecialchars($row['photo']) ?>" class="product-photo">
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= rupiah($row['price']) ?></td>
            <td><?= $row['amount'] ?></td>
            <td><?= rupiah($subtotal) ?></td>
        </tr>
        <?php endwhile; ?>
        <tr>
            <td colspan="4" class="total">Total</td>
            <td class="total"><?= rupiah($transaksi['total_price']) ?></td>
        </tr>
    </table>
    <button class="btn-home" onclick="window.location.href='../index.php'">Home</button>
    <button class="btn-print" onclick="window.print()">Cetak Invoice</button>
</div>

</body>
</html>
