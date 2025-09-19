<?php
session_start(); // mulai session untuk simpan data cart
include "db.php"; // koneksi ke database

// supaya hasilnya berbentuk JSON
header('Content-Type: application/json');

// cek apakah ada id produk yang dikirim lewat POST
if (!isset($_POST['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID produk tidak ada'
    ]);
    exit;
}

// ambil id produk dan durasi sewa
$id = (int)($_POST['id']);
$days = (int)($_POST['days'] ?? 1); // default 1 hari
if ($days < 1) $days = 1;

// cari produk berdasarkan id
$sql = "SELECT * FROM tb_produk WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

// kalau produk tidak ditemukan
if (!$product) {
    echo json_encode([
        'success' => false,
        'message' => 'Produk tidak ditemukan'
    ]);
    exit;
}

// kalau cart belum ada, buat array kosong
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// kalau produk sudah ada di cart, update quantity dan durasi sewa
if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['quantity']++;
    $_SESSION['cart'][$id]['days'] = $days; // update days
} else {
    // kalau produk belum ada di cart, masukkan baru
    $_SESSION['cart'][$id] = [
        'id'       => $product['id'],
        'name'     => $product['name'],
        'price'    => $product['price'],
        'photo'    => $product['photo'],
        'quantity' => 1,
        'days'     => $days
    ];
}

// hitung total isi cart (semua quantity dijumlahkan)
$totalItem = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalItem += $item['quantity'];
}

// kirim hasil dalam bentuk JSON
echo json_encode([
    'success' => true,
    'count'   => $totalItem,
    'message' => 'Produk berhasil ditambahkan ke keranjang'
]);
exit;
