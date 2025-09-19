<?php
session_start();
include "service/db.php";

// HANDLE CART ACTIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {
    foreach ($_POST['quantity'] as $index => $qty) {
        $qty = (int)$qty;
        if($qty > 0 && isset($_SESSION['cart'][$index])){
            $_SESSION['cart'][$index]['quantity'] = $qty;
        }
    }
    header("Location: cart.php");
    exit;
}

if(isset($_GET['remove'])){
    $index = (int)$_GET['remove'];
    if(isset($_SESSION['cart'][$index])) unset($_SESSION['cart'][$index]);
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Keranjang Belanja - Gifaattire</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<style>
body {
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  background: #f6f5f7;
  color: #222;
  margin: 0;
  padding: 20px 10px;
}

/* Navbar */
nav {
  background-color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 60px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.logo {
  font-size: 22px;
  font-weight: bold;
  color: #444;
  text-decoration: none;
}
.nav-links {
  display: flex;
  align-items: center;
  gap: 25px;
}
.nav-links a {
  text-decoration: none;
  color: #555;
  font-weight: 500;
  transition: 0.3s;
}
.nav-links a:hover {
  color: #222;
}
.icons {
  display: flex;
  align-items: center;
  gap: 15px;
  font-size: 18px;
  color: #444;
}
.icons a {
  color: inherit;
  text-decoration: none;
}

/* Container utama */
.container {
  max-width: 900px;
  margin: auto;
  background: #fff;
  border-radius: 10px;
  padding: 25px 30px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

/* Heading */
h2 {
  margin-bottom: 20px;
  font-weight: 600;
  color: #444;
}

/* Tabel produk */
table {
  border-collapse: collapse;
  width: 100%;
}
th, td {
  padding: 12px 10px;
  border-bottom: 1px solid #eee;
  text-align: left;
  vertical-align: middle;
}
th {
  background-color: #888; /* header abu abu */
  color: white;
  font-weight: 600;
  font-size: 14px;
}
td {
  font-size: 13px;
  color: #444;
}
table tr:hover {
  background-color: #f5f5f5;
}

/* Info produk */
.product-info {
  display: flex;
  align-items: center;
  gap: 15px;
}
.product-img {
  width: 90px;
  height: 90px;
  border-radius: 8px;
  object-fit: cover;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Input jumlah */
input[type="number"] {
  width: 60px;
  padding: 6px 8px;
  border: 1.8px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  transition: border-color 0.3s ease;
}
input[type="number"]:focus {
  border-color: #888; /* focus abu abu */
  outline: none;
}

/* Tombol */
.actions {
  margin-top: 25px;
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}
.btn {
  background-color: #888; /* tombol abu abu */
  color: white;
  padding: 12px 28px;
  border: none;
  border-radius: 50px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  text-decoration: none;
  text-align: center;
  transition: background-color 0.3s ease;
  display: inline-block;
}
.btn:hover {
  background-color: #666; /* hover lebih gelap */
}
.btn-delete {
  background: transparent;
  color: #555; /* abu abu */
  font-weight: 600;
  border: none;
  cursor: pointer;
  padding: 4px 8px;
  font-size: 13px;
  border-radius: 6px;
  transition: background-color 0.3s ease;
}
.btn-delete:hover {
  background-color: #eee;
}

/* Total baris */
.total-row td {
  font-weight: 700;
  font-size: 16px;
  color: #444;
}

/* Empty cart */
.empty-cart {
  text-align: center;
  padding: 20px 0;
  color: #888;
  font-style: italic;
}

/* Responsive */
@media(max-width:768px){
  .product-info {
    flex-direction: column;
    align-items: flex-start;
  }
  .actions {
    flex-direction: column;
    width: 100%;
  }
  input[type="number"] {
    width: 100%;
  }
}

</style>
</head>
<body>
<nav>
  <a href="index.php" class="logo">Gifaattire</a>
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
<h2>Shopping Cart</h2>
<?php if(!empty($_SESSION['cart'])): ?>
<form method="post">
<table>
<tr>
<th>Product</th>
<th>Price / item</th>
<th>Days</th>
<th>Quantity</th>
<th>Subtotal</th>
<th>Action</th>
</tr>
<?php
$total = 0;
foreach($_SESSION['cart'] as $index => $item){
    $name = htmlspecialchars($item['name']);
    $price = $item['price'];
    $days = $item['days'] ?? 1;
    $qty = $item['quantity'];
    $photo = $item['photo'] ?? '';
    $subtotal = $price * $qty;
    $total += $subtotal;
    $photoPath = $photo ? "images/$photo" : "images/no-image.png";
    echo "<tr>
    <td><div class='product-info'><img src='$photoPath' class='product-img'> $name</div></td>
    <td>Rp ".number_format($price,0,',','.')."</td>
    <td>$days hari</td>
    <td><input type='number' name='quantity[$index]' value='$qty' min='1'/></td>
    <td>Rp ".number_format($subtotal,0,',','.')."</td>
    <td><a href='?remove=$index' class='btn-delete' onclick='return confirm(\"Hapus item ini?\");'>Remove</a></td>
    </tr>";
}
?>
<tr class="total-row">
<td colspan="4" style="text-align:right;">Total:</td>
<td colspan="2">Rp <?= number_format($total,0,',','.') ?></td>
</tr>
</table>

<div class="actions">
<button type="submit" class="btn">Update Cart</button>
<a href="checkout.php" class="btn">Proceed to Checkout</a>
</div>
</form>
<?php else: ?>
<p class="empty-cart">Keranjang Anda kosong.</p>
<?php endif; ?>
</div>
</body>
</html>
