<?php
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include('../service/db.php');

// Handle Delete Transaction
if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']); // biar aman dari SQL Injection
    // hapus detail dulu biar ga ada constraint error
    mysqli_query($conn, "DELETE FROM tb_detail WHERE id_transaction='$id'");
    mysqli_query($conn, "DELETE FROM tb_transaksi WHERE id_transaction='$id'");
    header("Location: tb-transaction.php?deleted=success");
    exit;
}

$deleted = ($_GET['deleted'] ?? '') == 'success';
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Orders - Gifaattire Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>  
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<style>
body {font-family:'Poppins',sans-serif; margin:0; padding:0;}
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

.icons {display: flex; align-items: center; gap: 15px; font-size: 18px; color: #444;}
.icons a {color: inherit; text-decoration: none;}
.sidebar {position:fixed; top:0; bottom:0; left:0; width:220px; background:#f8f9fa; padding-top:20px;}
.sidebar .nav-link {color:#444; padding:10px 20px; display:block; text-decoration:none;}
.sidebar .nav-link.active {background:#444; color:#fff;}
.main {margin-left:220px; padding:20px;}
table {width:100%; border-collapse:collapse; margin-top:20px;}
th, td {padding:12px; border:1px solid #ddd; text-align:center;}
th {background:#444; color:#fff;}
button {padding:5px 10px; border:none; border-radius:5px; cursor:pointer;}
button.delete {background:#c0392b; color:#fff;}
button.delete:hover {background:#e74c3c;}
button.view {background:#2980b9; color:#fff; margin-right:5px;}
button.view:hover {background:#3498db;}
</style>
</head>
<body>

<!-- Navbar -->
<!-- Navbar -->
<nav class="navbar">
  <a href="index.php" class="logo">Gifaattire Admin</a>
  <div class="nav-links">
    <a href="index.php">Dashboard</a>
    <a href="tb-transaction.php">Orders</a>
    <a href="tb-product.php" class="active">Products</a>
    <a href="tb-customers.php">Customers</a>
  </div>
  <a href="../index.php?logout=true" class="logout-btn">
    <i data-feather="log-out"></i> Logout
  </a>
</nav>

<div class="sidebar">
    <a class="nav-link" href="index.php">Dashboard</a>
    <a class="nav-link active" href="tb_transaksi.php">Orders</a>
    <a class="nav-link" href="tb-product.php">Products</a>
    <a class="nav-link" href="tb-customers.php">Customers</a>
</div>

<div class="main">
<h1>Manage Orders</h1>

<table id="orders-table">
<thead>
<tr>
<th>No</th>
<th>Transaction ID</th>
<th>Customer</th>
<th>Date</th>
<th>Total Price</th>
<th>Payment Method</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php
$no=1;
$orders = mysqli_query($conn, "SELECT t.*, u.fullname 
                               FROM tb_transaksi t 
                               JOIN users u ON t.id_user=u.id 
                               ORDER BY t.id_transaction DESC");
while($row=mysqli_fetch_assoc($orders)):
?>
<tr>
<td><?= $no ?></td>
<td><?= $row['id_transaction'] ?></td>
<td><?= htmlspecialchars($row['fullname']) ?></td>
<td><?= $row['date'] ?></td>
<td>Rp <?= number_format($row['total_price'],0,',','.') ?></td>
<td><?= htmlspecialchars($row['payment_method']) ?></td>
<td>
<a href="../pages/invoice.php?id_transaction=<?= $row['id_transaction'] ?>" target="_blank">
<button class="view">View Invoice</button>
</a>
<button class="delete" onclick="deleteOrder(<?= $row['id_transaction'] ?>)">Delete</button>
</td>
</tr>
<?php $no++; endwhile; ?>
</tbody>
</table>

</div>

<script>
function deleteOrder(id){
    swal({
        title:"Yakin ingin menghapus order ini?",
        text:"Data yang dihapus tidak bisa dikembalikan!",
        icon:"warning",
        buttons:true,
        dangerMode:true
    }).then((willDelete)=>{
        if(willDelete){
            window.location="tb-transaction.php?delete_id="+id;
        }
    });
}

<?php if($deleted): ?>swal("Sukses!","Order berhasil dihapus.","success");<?php endif; ?>
</script>

</body>
</html>
