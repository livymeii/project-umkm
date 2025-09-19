<?php
session_start();
include "../service/db.php";

if (!isset($_GET['id'])) { die("Produk tidak ditemukan"); }
$id = $_GET['id'];

$query = "SELECT * FROM tb_produk WHERE id='$id' LIMIT 1";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);
if(!$product){ die("Produk tidak ditemukan"); }

// ===== ADD TO CART =====
$cartMessage = "";
if(isset($_POST['add_to_cart'])){
    $quantity = intval($_POST['quantity'] ?? 1);
    if($quantity < 1) $quantity = 1;

    $days = intval($_POST['days'] ?? 1);
    if($days < 1) $days = 1;

    $price = $product['price'];
    if($days > 1){
        $price += ($days - 1) * 10000; // Rp10.000 per hari tambahan
    }

    if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $found = false;

    foreach($_SESSION['cart'] as &$item){
        if($item['id'] == $id && $item['days'] == $days){
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    if(!$found){
        $_SESSION['cart'][] = [
            'id'       => $id,
            'name'     => $product['name'],
            'price'    => $price,
            'quantity' => $quantity,
            'days'     => $days,
            'photo'    => $product['photo']
        ];
    }

    $cartMessage = "Berhasil ditambahkan ke keranjang";
}

// ===== WISHLIST =====
$wishlistMessage = "";
if(isset($_POST['add_to_wishlist'])){
    if(!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
    $key = array_search($id,$_SESSION['wishlist']);
    if($key === false){
        $_SESSION['wishlist'][] = $id;
        $wishlistMessage = "Ditambahkan ke wishlist";
    } else {
        unset($_SESSION['wishlist'][$key]);
        $wishlistMessage = "Dihapus dari wishlist";
    }
}

// ===== BADGE COUNT =====
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'],'quantity')) : 0;
$wishlistCount = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title><?= htmlspecialchars($product['name']) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #f5f5f5;
  margin: 0;
  color: #333;
}

nav {
  background: #fff;
  display: flex;
  justify-content: space-between;
  padding: 15px 50px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  position: sticky;
  top: 0;
  z-index: 999;
  align-items: center;
}

.logo {
  font-size: 24px;
  font-weight: bold;
  color: #444;
  text-decoration: none;
}

.nav-links {
  display: flex;
  gap: 25px;
}

.nav-links a {
  text-decoration: none;
  color: #555;
  font-weight: 500;
  transition: .3s;
}

.nav-links a:hover {
  color: #000;
}

.icons {
  display: flex;
  gap: 18px;
  font-size: 18px;
  position: relative;
}

.icons a {
  color: inherit;
  text-decoration: none;
  position: relative;
  display: inline-flex;
}

.badge-pill {
  min-width: 20px;
  height: 20px;
  line-height: 20px;
  padding: 0 6px;
  border-radius: 999px;
  background: #666;
  color: #fff;
  font-size: 12px;
  text-align: center;
  position: absolute;
  top: -8px;
  right: -10px;
}

.container {
  width: 90%;
  max-width: 1200px;
  margin: 40px auto;
}

.product-detail-premium {
  display: flex;
  flex-wrap: wrap;
  gap: 50px;
  background: #fff;
  padding: 40px;
  border-radius: 15px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.product-detail-premium img {
  width: 100%;
  border-radius: 15px;
  object-fit: cover;
  max-height: 550px;
}

.product-info-premium {
  flex: 1;
  min-width: 300px;
}

.product-info-premium h1 {
  font-size: 36px;
  margin-bottom: 15px;
  font-weight: bold;
}

.price {
  font-size: 28px;
  color: #444;
  font-weight: bold;
  margin-bottom: 15px;
}

.availability {
  font-size: 16px;
  margin-bottom: 15px;
}

.description {
  font-size: 16px;
  line-height: 1.7;
  margin-bottom: 25px;
  color: #555;
}

.add-to-cart-form {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 15px;
}

.add-to-cart-form input[type="number"] {
  width: 90px;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

select {
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.btn {
  background: #444;
  color: #fff;
  padding: 12px 25px;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  border: none;
  transition: .3s;
}

.btn:hover {
  background: #666;
}

.cart-wishlist-msg {
  margin-top: 10px;
  font-weight: bold;
  color: green;
}

@media(max-width:768px){
  .product-detail-premium {
    flex-direction: column;
  }
}
</style>
</head>
<body>
<nav>
<a href="../index.php" class="logo">Gifaattire</a>
<div class="nav-links">
<a href="../index.php">Home</a>
<a href="index.php">Shop</a>
<a href="../cart.php">Cart</a>
<a href="../wishlist.php">Wishlist</a>
</div>
<div class="icons">
<a href="../wishlist.php"><i class="fas fa-heart"></i><span class="badge-pill"><?= $wishlistCount ?></span></a>
<a href="../cart.php"><i class="fas fa-shopping-cart"></i><span class="badge-pill"><?= $cartCount ?></span></a>
</div>
</nav>

<div class="container">
<div class="product-detail-premium">
  <div class="product-image-premium">
    <img src="../images/<?= htmlspecialchars($product['photo']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"/>
  </div>
  <div class="product-info-premium">
    <h1><?= htmlspecialchars($product['name']) ?></h1>
    <div class="price">Rp <?= number_format($product['price'],0,',','.') ?> / hari</div>

    <!-- STATUS PRODUK -->
    <?php if($product['stock'] > 0): ?>
      <p class="availability">Status: <span style="color:green;">Tersedia</span></p>
    <?php else: ?>
      <p class="availability">Status: <span style="color:red;">Sudah tersewa, booking di tanggal lain</span></p>
    <?php endif; ?>

    <p class="description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

    <!-- FORM ADD TO CART -->
    <?php if($product['stock'] > 0): ?>
      <form class="add-to-cart-form" method="post">
        <label for="days">Durasi sewa:</label>
        <select id="days" name="days" onchange="updatePriceInfo()">
          <option value="1">1 hari</option>
          <option value="3">3 hari</option>
          <option value="7">1 minggu</option>
        </select>

        <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>"/>
        <button type="submit" name="add_to_cart" class="btn">
          <i class="fas fa-shopping-cart"></i> add to cart
        </button>
      </form>
    <?php else: ?>
      <button class="btn" disabled style="background:#aaa; cursor:not-allowed;">
        <i class="fas fa-ban"></i> Tidak tersedia
      </button>
    <?php endif; ?>

    <!-- INFO TAMBAHAN HARGA -->
    <p id="price-info" style="margin-top:8px; color:#c0392b; font-weight:bold;"></p>

    <script>
    function updatePriceInfo() {
      const days = parseInt(document.getElementById("days")?.value || 1);
      const priceInfo = document.getElementById("price-info");
      
      if(days > 1){
        const extra = (days - 1) * 10000;
        priceInfo.textContent = `Ada biaya tambahan ketika anda menyewa lebih dari satu hari yaitu Rp10.000/hari. Total tambahan: Rp${extra.toLocaleString('id-ID')}`;
      } else {
        priceInfo.textContent = "";
      }
    }
    updatePriceInfo();
    </script>

    <!-- FORM WISHLIST -->
    <form method="post" style="margin-top:10px;">
      <button type="submit" name="add_to_wishlist" class="btn" style="background:#c0392b;">
      <i class="fas fa-heart"></i> Wishlist</button>
    </form>

    <?php if($cartMessage || $wishlistMessage): ?>
      <div class="cart-wishlist-msg"><?= $cartMessage ?> <?= $wishlistMessage ?></div>
    <?php endif; ?>
  </div>
</div>
</div>

</body>
</html>
