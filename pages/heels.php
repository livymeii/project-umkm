<?php
session_start();
include ("../service/db.php");

// hitung badge awal
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
$wishlistCount = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Heels Collection</title>

<link rel="stylesheet" href="../css/layout.css?v=1" />
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>

<style>
body{font-family:'Poppins',sans-serif;}
nav{background:#fff;display:flex;justify-content:space-between;align-items:center;padding:15px 60px;box-shadow:0 2px 5px rgba(0,0,0,0.05);position:sticky;top:0;z-index:999;}
.logo{font-size:22px;font-weight:bold;text-decoration:none;color:#000;}
.nav-links{display:flex;align-items:center;gap:25px;}
.nav-links a{text-decoration:none;color:#222;font-weight:500;transition:.3s;}
.nav-links a:hover{color:#000;}
.icons{display:flex;align-items:center;gap:18px;font-size:18px;color:#222;}
.icons a{color:inherit;text-decoration:none;position:relative;display:inline-flex;}
.badge-pill{min-width:20px;height:20px;line-height:20px;padding:0 6px;border-radius:999px;background:#dc143c;color:#fff;font-size:12px;text-align:center;position:absolute;top:-8px;right:-10px;display:inline-block;}
.hidden{display:none !important;}
.products{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;padding:0 30px;}
.row{position:relative;padding:10px;background:#fff;border-radius:10px;box-shadow:0 2px 5px rgba(0,0,0,0.1);margin-bottom:20px;text-align:center;overflow:hidden;}
.row img{width:100%;height:350px;object-fit:cover;border-radius:10px;display:block;transition:transform .3s ease;}
.row:hover img{transform:scale(1.1);}
.product-text h5{position:absolute;top:15px;left:15px;background:red;color:#fff;padding:3px 10px;font-size:12px;border-radius:5px;margin:0;}
.image-holder{position:relative;}
.action-icons{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;gap:12px;opacity:0;transform:translateY(10px);transition:all .2s ease;background:rgba(0,0,0,0);}
.row:hover .action-icons{opacity:1;transform:translateY(0);background:rgba(0,0,0,0.05);}
.btn-circle{width:42px;height:42px;border-radius:999px;border:none;display:inline-flex;align-items:center;justify-content:center;background:#ffffffd9;box-shadow:0 2px 8px rgba(0,0,0,.15);cursor:pointer;transition:transform .12s ease,background .12s ease;}
.btn-circle:hover{transform:translateY(-2px) scale(1.03);background:#fff;}
.btn-circle i{font-size:18px;}
.rating i{color:gold;font-size:16px;}
.price h4{margin-top:10px;font-size:18px;}
.price p{color:green;font-weight:bold;margin-bottom:8px;}
.price h9,.price h11{color:#404040;font-size:small;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.center-text{text-align:center;margin:30px 0;}
.center-text h2{font-size:32px;font-weight:bold;}
.center-text span{color:crimson;}
@keyframes bump{0%{transform:scale(1);}30%{transform:scale(1.2);}100%{transform:scale(1);}}
.bump{animation:bump .25s ease;}
@media(max-width:768px){nav{padding:15px 30px;flex-wrap:wrap;}.products{padding:0 15px;}.row img{height:300px;}}
@media(max-width:480px){.row img{height:250px;}}
</style>
</head>
<body>

<nav>
  <a href="#" class="logo">Gifaattire</a>
  <div class="nav-links">
    <a href="../index.php">Home</a>
    <a href="about.php">About</a>
    <a href="index.php">Shop</a>
    <a href="contact.php">Contact</a>
    <a href="../cart.php">Cart</a>
  </div>
  <div class="icons">
    <a href="../wishlist.php"><i class="fas fa-heart"></i>
      <span id="wishlist-count" class="badge-pill <?php echo $wishlistCount ? '' : 'hidden'; ?>"><?= $wishlistCount ?></span>
    </a>
    <a href="../cart.php"><i class="fas fa-shopping-cart"></i>
      <span id="cart-count" class="badge-pill <?php echo $cartCount ? '' : 'hidden'; ?>"><?= $cartCount ?></span>
    </a>
  </div>
</nav>

<section class="trending-product" id="trending">
  <div class="center-text">
    <h2>Our <span>Heels</span></h2>
  </div>

  <div class="products">
   <?php
      $query = "SELECT p.*, k.nama_kategori FROM tb_produk p JOIN tb_kategori k ON p.id_kategori = k.id_kategori WHERE k.nama_kategori = 'Heels'";
      $show = mysqli_query($conn, $query);
      while($row = mysqli_fetch_assoc($show)){
        $inWishlist = isset($_SESSION['wishlist'][$row['id']]);
        $shortDesc = substr($row['description'],0,50);
        if(strlen($row['description'])>50) $shortDesc .= '...';
    ?>
    <div class="row">
      <div class="image-holder">
        <img src="../images/<?= htmlspecialchars($row['photo']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" />
        <div class="product-text"><h5><?= htmlspecialchars(ucfirst($row['tag']??'New')) ?></h5></div>
        <div class="action-icons">
          <button class="btn-circle wishlist-toggle <?= $inWishlist?'active':'' ?>" data-id="<?= $row['id'] ?>"><i class="<?= $inWishlist?'fas':'far' ?> fa-heart"></i></button>
          <button class="btn-circle add-to-cart" data-id="<?= $row['id'] ?>"><i class="fas fa-shopping-cart"></i></button>
          <button class="btn-circle buy-now" data-id="<?= $row['id'] ?>"><i class="fas fa-credit-card"></i></button>
        </div>
      </div>
      <div class="rating"><i class='bx bx-star'></i><i class='bx bx-star'></i><i class='bx bx-star'></i><i class='bx bx-star'></i><i class='bx bxs-star-half'></i></div>
      <div class="price">
        <h4><?= htmlspecialchars($row['name']) ?></h4>
        <h11><?= htmlspecialchars($shortDesc) ?></h11>
        <p>Rp <?= number_format($row['price'],0,',','.') ?></p>
        <a href="single-product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary mt-2">Lihat Detail</a>
      </div>
    </div>
    <?php } ?>
  </div>
</section>

<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Helper badge
function updateBadge($el,count){ if(count>0){$el.removeClass('hidden').text(count);}else{$el.addClass('hidden').text(0);} }
// Wishlist toggle
$(document).on('click','.wishlist-toggle',function(){
  const $btn=$(this), id=$btn.data('id');
  $.post('../service/add-to-wishlist.php',{id:id,action:'toggle'},function(res){
    try{ var data=(typeof res==='string')?JSON.parse(res):res; }catch(e){ return; }
    if(data.success){ $btn.find('i').toggleClass('far fas'); updateBadge($('#wishlist-count'),data.count); }
  });
});
// Add to Cart
$(document).on('click','.add-to-cart',function(){
  const id=$(this).data('id');
  $.post('../service/add-to-cart.php',{id:id},function(res){
    try{ var data=(typeof res==='string')?JSON.parse(res):res; }catch(e){ return; }
    if(data.success){ window.location.href='../cart.php'; }
  });
});
// Buy Now
$(document).on('click','.buy-now',function(){
  const id=$(this).data('id');
  $.post('../service/add-to-cart.php',{id:id},function(res){ window.location.href='../checkout.php'; });
});
</script>
</body>
</html>
