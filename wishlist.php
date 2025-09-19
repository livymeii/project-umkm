<?php
session_start();
include "service/db.php"; // koneksi database

// Ambil wishlist ID dari session
$wishlist_ids = isset($_SESSION['wishlist']) ? array_keys($_SESSION['wishlist']) : [];
$products = [];

// Ambil data produk dari DB
if (!empty($wishlist_ids)) {
    $ids = implode(',', array_map('intval', $wishlist_ids));
    $query = "SELECT * FROM tb_produk WHERE id IN ($ids)";
    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
}

// Hitung cart & wishlist untuk badge
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
$wishlistCount = count($wishlist_ids);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Wishlist - Gifaattire</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<style>
body { font-family: 'Poppins', sans-serif; font-size: 14px; background: #f6f5f7; color: #222; margin:0; padding:20px 10px; }
nav { background-color:#fff; display:flex; justify-content:space-between; align-items:center; padding:15px 60px; box-shadow:0 2px 5px rgba(0,0,0,0.05); }
.logo { font-size:22px; font-weight:bold; color:#000; text-decoration:none; }
.nav-links { display:flex; align-items:center; gap:25px; }
.nav-links a { text-decoration:none; color:#444; font-weight:500; transition:0.3s; }
.nav-links a:hover { color:#222; }
.icons { display:flex; align-items:center; gap:15px; font-size:18px; color:#444; }
.icons a { color:inherit; text-decoration:none; position:relative; }
.badge-pill { min-width:20px; height:20px; line-height:20px; padding:0 6px; border-radius:999px; background:#dc143c; color:#fff; font-size:12px; text-align:center; position:absolute; top:-8px; right:-10px; display:inline-block; }
.hidden { display:none !important; }

.container { max-width:700px; margin:auto; background:#fff; border-radius:10px; padding:25px 30px; box-shadow:0 8px 20px rgba(0,0,0,0.1); }
h2 { margin-bottom:20px; font-weight:600; color:#444; }
table { border-collapse:collapse; width:100%; }
th, td { padding:12px 10px; border-bottom:1px solid #eee; text-align:left; vertical-align:middle; }
th { background-color:#888; color:white; font-weight:600; font-size:14px; }
td { font-size:13px; color:#444; }
.product-info { display:flex; align-items:center; gap:15px; }
.product-img { width:90px; height:90px; border-radius:8px; object-fit:cover; box-shadow:0 3px 8px rgba(0,0,0,0.1); }
.btn-small { background-color:#666; color:white; padding:6px 12px; font-size:13px; border-radius:25px; text-decoration:none; display:inline-block; text-align:center; transition:background-color 0.3s ease; cursor:pointer; }
.btn-small:hover { background-color:#444; }
.btn-delete { background:transparent; color:#a8324e; font-weight:600; border:none; cursor:pointer; padding:4px 8px; font-size:13px; border-radius:6px; transition:background-color 0.3s ease; }
.btn-delete:hover { background-color:#eee; }
.empty-wishlist { text-align:center; padding:20px 0; color:#888; font-style:italic; }
@keyframes bump {0%{transform:scale(1);}30%{transform:scale(1.2);}100%{transform:scale(1);}}
.bump {animation:bump .25s ease;}
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
    <a href="#"><i class="fas fa-heart"></i><span id="wishlist-count" class="badge-pill <?= $wishlistCount ? '' : 'hidden' ?>"><?= $wishlistCount ?></span></a>
    <a href="#"><i class="fas fa-shopping-cart"></i><span id="cart-count" class="badge-pill <?= $cartCount ? '' : 'hidden' ?>"><?= $cartCount ?></span></a>
  </div>
</nav>

<div class="container">
    <h2>Wishlist</h2>

    <?php if (!empty($products)): ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="images/<?= htmlspecialchars($product['photo'] ?? '') ?>" 
                                 alt="<?= htmlspecialchars($product['name'] ?? '') ?>" 
                                 class="product-img">
                            <span><?= htmlspecialchars($product['name'] ?? '') ?></span>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($product['category'] ?? '') ?></td>
                    <td>Rp <?= number_format($product['price'] ?? 0,0,',','.') ?></td>
                    <td>
                        <button class="btn-delete" data-id="<?= $product['id'] ?>">Remove</button>
                        <br>
                        <button class="btn-small add-to-cart-btn" data-id="<?= $product['id'] ?>" style="margin-top:5px;">Add to Cart</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <div class="empty-wishlist">Your wishlist is empty</div>
    <?php endif; ?>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function updateBadge($el, count){
    if(count>0){ $el.removeClass('hidden').text(count); } 
    else { $el.addClass('hidden').text(0); }
}

// Add to Cart
$(document).on('click', '.add-to-cart-btn', function(){
    const id = $(this).data('id');
    $.post('service/add-to-cart.php', { id: id }, function(res){
        try{ var data = (typeof res==='string')? JSON.parse(res): res; }catch(e){return;}
        if(data.success){
            updateBadge($('#cart-count'), data.count);
            $('.fa-shopping-cart').addClass('bump');
            setTimeout(()=>$('.fa-shopping-cart').removeClass('bump'),250);
            // redirect ke cart
            window.location.href = 'cart.php';
        } else {
            alert(data.message);
        }
    });
});

// Hapus wishlist
$(document).on('click', '.btn-delete', function(){
    const id = $(this).data('id');
    $.post('service/remove-wishlist.php', { id: id }, function(res){
        try{ var data = (typeof res==='string')? JSON.parse(res): res; }catch(e){return;}
        if(data.success){
            updateBadge($('#wishlist-count'), data.count);
            location.reload(); // refresh tabel wishlist
        } else {
            alert(data.message);
        }
    });
});
</script>
</body>
</html>
