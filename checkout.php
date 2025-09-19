<?php
session_start();
include "service/db.php";

// Cek apakah user sudah login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id'];
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Kalau keranjang kosong
if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

// Hitung subtotal
$subtotal = 0;
foreach ($cart as $item) {
    $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
    $subtotal += $item['price'] * $qty;
}

$ongkir = 10000;
$total  = $subtotal + $ongkir;

// Proses checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);
    $payment = trim($_POST['payment']);
    $agree   = isset($_POST['agree']) ? $_POST['agree'] : "";

    if ($name == "" || $email == "" || $address == "" || $payment == "") {
        $error = "Semua field harus diisi!";
    } elseif ($agree != "yes") {
        $error = "Anda harus membaca dan menyetujui rules terlebih dahulu!";
    } else {
        $tgl = date('Y-m-d H:i:s');

        // Simpan transaksi
        $query = "INSERT INTO tb_transaksi (id_user, date, total_price, payment_method)
                  VALUES ('$id_user', '$tgl', '$total', '$payment')";
        mysqli_query($conn, $query);
        $id_transaction = mysqli_insert_id($conn);

        // Simpan detail transaksi
        foreach ($cart as $item) {
            $id_product = $item['id'];
            $amount     = isset($item['quantity']) ? (int)$item['quantity'] : 1;
            mysqli_query($conn, "INSERT INTO tb_detail (id_transaction, id_product, amount)
                                 VALUES ('$id_transaction', '$id_product', '$amount')");
        }

        // Hapus cart
        unset($_SESSION['cart']);

        // Redirect ke invoice
        header("Location: pages/invoice.php?id_transaction=" . $id_transaction);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Checkout - Gifaattire</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<style>
 body {
  font-family: 'Poppins', sans-serif;
  background: #f9f9f9;
  color: #222;
  margin: 0;
  padding: 20px;
}
nav {
  background-color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 60px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.logo { font-size: 22px; font-weight: bold; color: #000; text-decoration: none; }
.nav-links { display: flex; align-items: center; gap: 25px; }
.nav-links a { text-decoration: none; color: #222; font-weight: 500; transition: 0.3s; }
.nav-links a:hover { color: #000; }
.icons { display: flex; align-items: center; gap: 15px; font-size: 18px; color: #222; }
.icons a { color: inherit; text-decoration: none; }

.container {
  max-width: 700px;
  margin: auto;
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
h2, h3 {
  color: #444;
  margin-bottom: 15px;
}
.details__item {
  border-bottom: 1px solid #eee;
  padding: 10px 0;
}
.summary {
  border-top: 2px solid #bbb;
  padding-top: 10px;
  font-weight: bold;
  text-align: right;
  color: #444;
}
label {
  font-weight: 600;
  display: block;
  margin-top: 10px;
}
input, textarea, select {
  width: 100%;
  padding: 8px;
  border: 1.5px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}
input:focus, textarea:focus, select:focus {
  border-color: #888;
  outline: none;
}
button {
  background: #666;
  color: #fff;
  border: none;
  padding: 10px;
  margin-top: 15px;
  font-size: 15px;
  font-weight: bold;
  border-radius: 50px;
  width: 100%;
  cursor: pointer;
}
button:hover {
  background: #444;
}
.error {
  background: #ffe3e3;
  padding: 10px;
  color: #a10000;
  border-radius: 6px;
  margin-bottom: 15px;
}
.success {
  background: #ddffdd;
  padding: 10px;
  color: #237a00;
  border-radius: 6px;
  margin-bottom: 15px;
}
.rules-check {
  margin-top: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.rules-check a {
  color: #555; /* abu-abu gelap */
  font-weight: 600;
  text-decoration: none;
}

.rules-check a:hover {
  text-decoration: underline;
}

/* Custom checkbox */
.checkbox-container {
  display: flex;
  align-items: center;
  font-size: 14px;
  cursor: pointer;
  position: relative;
  padding-left: 28px;
  user-select: none;
  color: #333;
}

.checkbox-container a {
  color: #555; /* abu-abu gelap */
  font-weight: 600;
  margin-left: 4px;
  text-decoration: none;
}

.checkbox-container a:hover {
  text-decoration: underline;
}

.checkbox-container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkmark {
  position: absolute;
  left: 0;
  top: 2px;
  height: 18px;
  width: 18px;
  background-color: #eee;
  border-radius: 4px;
  border: 1.5px solid #ccc;
  transition: all 0.3s ease;
}

.checkbox-container input:checked ~ .checkmark {
  background-color: #666; /* abu-abu gelap */
  border-color: #666;
}

.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

.checkbox-container input:checked ~ .checkmark:after {
  display: block;
}

.checkbox-container .checkmark:after {
  left: 5px;
  top: 1px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}


</style>
</head>
<body>

<nav>
  <a href="#" class="logo">Gifaattire</a>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="index.php">Shop</a>
    <a href="contact.php">Contact</a>
    <a href="cart.php">Cart</a>
  </div>
  <div class="icons">
    <a href="#"><i class="fas fa-search"></i></a>
    <a href="#"><i class="fas fa-heart"></i></a>
    <a href="#"><i class="fas fa-shopping-cart"></i></a>
  </div>
</nav>

<div class="container">
    <h2>Checkout</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success"><?= $success ?></div>
        <a href="index.php" style="text-decoration:none;">
            <button>Back to Home</button>
        </a>
    <?php else: ?>

        <h3>Order Summary</h3>
        <?php foreach ($cart as $item): 
            $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
        ?>
            <div class="details__item">
                <div><strong><?= htmlspecialchars($item['name']) ?></strong></div>
                <div>Price: <?= rupiah($item['price']) ?></div>
                <div>Quantity: <?= $qty ?></div>
            </div>
        <?php endforeach; ?>

        <div class="summary">
            <p>Subtotal: <?= rupiah($subtotal) ?></p>
            <p>Shipping: <?= rupiah($ongkir) ?></p>
            <p>Total: <?= rupiah($total) ?></p>
        </div>

        <h3>Customer Information</h3>
        <form method="POST" action="">
            <label>Full Name:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Full Address:</label>
            <textarea name="address" rows="3" required></textarea>

            <label>Payment Method:</label>
            <select name="payment" required>
                <option value="">-- Select --</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="COD">Cash On Delivery (COD)</option>
                <option value="E-Wallet">E-Wallet</option>
            </select>

              <div class="rules-check">
                <label class="checkbox-container">
                 <input type="checkbox" name="agree" value="yes" required>
                 <span class="checkmark"></span>
                 Saya sudah membaca dan menyetujui 
                 <a href="pages/rules.php" target="_blank">rules</a>.
                 </label>
                </div>

            <button type="submit" name="checkout">Pay Now</button>
        </form>

    <?php endif; ?>
</div>

</body>
</html>
