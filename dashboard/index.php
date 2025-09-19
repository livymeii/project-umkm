<?php
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include('../service/db.php');

// Ambil data ringkasan
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_produk"))['total'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM `tb_transaksi`"))['total'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'"))['total'];

// Data chart penjualan per bulan
$chart_data = [];
$result = mysqli_query($conn, "
    SELECT MONTH(date) as month, SUM(total_price) as total 
    FROM `tb_transaksi` 
    GROUP BY MONTH(date)
");
while($row = mysqli_fetch_assoc($result)){
    $chart_data[$row['month']] = $row['total'];
}
// Buat array 12 bulan supaya chart lengkap
$monthly_sales = [];
for($i=1;$i<=12;$i++){
    $monthly_sales[$i] = $chart_data[$i] ?? 0;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Dashboard Admin - Gifaattire</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/> 
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body {font-family:'Poppins',sans-serif;}
/* Navbar */
/* Navbar */
.navbar {
  background-color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 40px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.logo {
  font-size: 20px;
  font-weight: 600;
  color: #000;
  text-decoration: none;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 25px;
}

.nav-links a {
  text-decoration: none;
  color: #444;
  font-weight: 500;
  transition: 0.3s;
}

.nav-links a:hover {
  color: #000;
}

.icons {
  display: flex;
  align-items: center;
  gap: 15px;
  font-size: 18px;
}

.icons a {
  color: #444;
  transition: 0.3s;
}

.icons a:hover {
  color: #000;
}

.container-fluid{padding:0;}
.cards {display:flex; gap:20px; margin:20px 0;}
.card {flex:1; padding:20px; background:#f4f4f4; border-radius:10px; text-align:center;}
.card h3 {margin-bottom:10px;}
.shortcut-btns {display:flex; gap:15px; flex-wrap:wrap; margin-bottom:30px;}
.shortcut-btns a {flex:1; text-decoration:none; padding:15px; background:#444; color:#fff; border-radius:10px; text-align:center; transition:0.3s;}
.shortcut-btns a:hover {background:#666;}
.sidebar {position:fixed; top:0; bottom:0; left:0; width:220px; background:#f8f9fa; padding-top:20px;}
.sidebar .nav-link {color:#444; padding:10px 20px; display:block;}
.sidebar .nav-link.active {background:#444; color:#fff;}
.main {margin-left:220px; padding:20px;}
</style>
</head>
<body>

<!-- Navbar -->
<!-- Navbar -->
<nav class="navbar">
  <a href="#" class="logo">Gifaattire Admin</a>
  <div class="nav-links">
    <a href="../index.php">Home</a>
    <a href="about.php">About</a>
    <a href="index.php">Shop</a>
    <a href="contact.php">Contact</a>
    <a href="cart.php">Cart</a>
  </div>
  <div class="icons">
    <a href="#"><i class="fas fa-search"></i></a>
    <a href="#"><i class="fas fa-heart"></i></a>
    <a href="#"><i class="fas fa-shopping-cart"></i></a>
    <a href="../index.php?logout=true" title="Logout">
      <i class="fas fa-sign-out-alt"></i>
    </a>
  </div>
</nav>

  <!-- <a href="../index.php?logout=true" class="logout-btn">
      <i class="fas fa-sign-out-alt"></i> Logout
  </a> -->


<div class="sidebar">
    <a class="nav-link active" href="index.php">Dashboard</a>
    <a class="nav-link" href="tb-transaction.php">Orders</a>
    <a class="nav-link" href="tb-product.php">Products</a>
    <a class="nav-link" href="tb-customers.php">Customers</a>
</div>

<div class="main">
    <h1>Dashboard Overview</h1>

    <!-- Ringkasan Cards -->
    <div class="cards">
        <div class="card">
            <h3>Total Products</h3>
            <p><?= $total_products ?></p>
        </div>
        <div class="card">
            <h3>Total Orders</h3>
            <p><?= $total_orders ?></p>
        </div>
        <div class="card">
            <h3>Total Customers</h3>
            <p><?= $total_customers ?></p>
        </div>
    </div>

    <!-- Shortcut Buttons -->
    <div class="shortcut-btns">
        <a href="tb-product.php">Manage Products</a>
        <a href="tb-transaction.php">Manage Orders</a>
        <a href="tb-customers.php">Manage Customers</a>
    </div>

    <!-- Chart Penjualan -->
    <canvas id="salesChart" height="100"></canvas>
    <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'Sales per Month',
                data: <?= json_encode(array_values($monthly_sales)) ?>,
                backgroundColor: '#444'
            }]
        },
        options: {
            responsive:true,
            plugins: { legend:{display:false} }
        }
    });
    </script>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
