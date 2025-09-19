<?php
session_start();
include "service/db.php"; // koneksi database

// --- Cek login ---
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['id'];

// --- Logout ---
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

// --- Ambil data user ---
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id='$userId'");
$userData  = mysqli_fetch_assoc($userQuery);
if (!$userData) die("User tidak ditemukan");

// --- Fungsi rupiah ---
function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

$success_message = "";
$error_message   = "";

// --- Update profile ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];

    $updates = [];
    $updates[] = "fullname='$fullname'";
    $updates[] = "username='$username'";
    $updates[] = "phone='$phone'";

    // --- Update foto profil ---
    if (isset($_FILES['profile-photo']) && $_FILES['profile-photo']['error'] == 0) {
        $filename = time() . "_" . basename($_FILES['profile-photo']['name']);
        $target   = "images/" . $filename;

        if (move_uploaded_file($_FILES['profile-photo']['tmp_name'], $target)) {
            $updates[] = "profile_picture='$filename'";
        } else {
            $error_message = "Gagal mengupload foto profil.";
        }
    }

    // --- Update password jika diisi ---
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $updates[] = "password='$password_hash'";
    }

    // --- Jalankan update ---
    if (!empty($updates)) {
        $update_sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id='$userId'";
        if (mysqli_query($conn, $update_sql)) {
            $success_message = "Profil berhasil diperbarui!";
            // Refresh data user
            $userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id='$userId'");
            $userData  = mysqli_fetch_assoc($userQuery);
        } else {
            $error_message = "Terjadi kesalahan saat update profil: " . mysqli_error($conn);
        }
    }
}

// --- Ambil wishlist dari session ---
$wishlist_ids = isset($_SESSION['wishlist']) ? array_keys($_SESSION['wishlist']) : [];
$wishlist_items = [];
if (!empty($wishlist_ids)) {
    $ids = implode(',', array_map('intval', $wishlist_ids));
    $res = mysqli_query($conn, "SELECT * FROM tb_produk WHERE id IN ($ids)");
    while ($row = mysqli_fetch_assoc($res)) {
        $wishlist_items[] = $row;
    }
}

// --- Ambil cart dari session ---
$cart_ids = isset($_SESSION['cart']) ? array_keys($_SESSION['cart']) : [];
$cart_items = [];
if (!empty($cart_ids)) {
    $ids = implode(',', array_map('intval', $cart_ids));
    $res = mysqli_query($conn, "SELECT * FROM tb_produk WHERE id IN ($ids)");
    while ($row = mysqli_fetch_assoc($res)) {
        $row['quantity'] = $_SESSION['cart'][$row['id']]['quantity'] ?? 1;
        $cart_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="css/profile.css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/> 
<title>Profil Saya</title>
<style>
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
.nav-links a { text-decoration: none; color: #444; font-weight: 500; transition: 0.3s; }
.nav-links a:hover { color: #222; }
.icons { display: flex; align-items: center; gap: 15px; font-size: 18px; color: #444; }
.icons a { color: inherit; text-decoration: none; }

.activity-box { margin: 15px 0; padding: 12px; border: 1px solid #eee; border-radius: 8px; background: #fafafa; }
.activity-box img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; margin-right: 10px; }
.activity-item { display: flex; align-items: center; margin-bottom: 8px; }
.activity-item i { margin-right: 10px; }
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

<div class="container bootstrap snippets bootdey">
<div class="row">
  <!-- Sidebar -->
  <div class="profile-nav col-md-3">
      <div class="panel">
          <div class="user-heading round">
              <a href="#"><img src="images/<?= $userData['profile_picture'] ?? 'default-profile.png' ?>" alt=""></a>
              <h1><?= htmlspecialchars($userData['fullname']) ?></h1>
              <p><?= htmlspecialchars($userData['email']) ?></p>
              <small class="joined-date">Member since <?= date("d M Y", strtotime($userData['created_at'])) ?></small>
          </div>
          <ul class="nav nav-pills nav-stacked">
              <li class="active"><a href="profile.php"> <i class="fa fa-user"></i> Profile</a></li>
              <li><a href="#recent"> <i class="fa fa-calendar"></i> Recent Activity 
                <span class="label label-warning pull-right r-activity"><?= count($wishlist_items) + count($cart_items) ?></span></a></li>
              <li><a href="#history"> <i class="fa fa-file-invoice"></i> Purchase History</a></li>
              <li><a href="#edit"> <i class="fa fa-edit"></i> Edit Profile</a></li>
              <li><a href="profile.php?logout=true"> <i class="fa fa-sign-out"></i> Logout</a></li>
          </ul>
      </div>
  </div>

  <!-- Profile info -->
  <div class="profile-info col-md-9">
      <div class="panel">
          <div class="bio-graph-heading">Welcome <?= htmlspecialchars($userData['username']) ?></div>
          <div class="panel-body bio-graph-info">
              <h1>Bio Data</h1>
              <div class="row">
                <div class="bio-row"><p><span>User ID </span>: User <?= $userData['id'] ?></p></div>
                <div class="bio-row"><p><span>Full Name </span>: <?= htmlspecialchars($userData['fullname']) ?></p></div>
                <div class="bio-row"><p><span>Username </span>: <?= htmlspecialchars($userData['username']) ?></p></div>
                <div class="bio-row"><p><span>Phone </span>: <?= htmlspecialchars($userData['phone']) ?></p></div>
                <div class="bio-row"><p><span>Email </span>: <?= htmlspecialchars($userData['email']) ?></p></div>
                <div class="bio-row"><p><span>Address </span>: <?= htmlspecialchars($userData['address']) ?></p></div>
              </div>
          </div>
      </div>

      <!-- Recent Activity -->
      <div class="panel" id="recent">
          <div class="panel-body">
              <h3>Recent Activity</h3>
              <?php if (empty($wishlist_items) && empty($cart_items)): ?>
                  <p>No recent activity yet.</p>
              <?php endif; ?>

              <?php if (!empty($wishlist_items)): ?>
              <div class="activity-box">
                  <h4><i class="fa fa-heart"></i> Wishlist</h4>
                  <?php foreach ($wishlist_items as $item): ?>
                      <div class="activity-item">
                          <a href="single-product.php?id=<?= $item['id'] ?>">
                              <img src="images/<?= htmlspecialchars($item['photo']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                          </a>
                          <div><b><?= htmlspecialchars($item['name']) ?></b><br>
                              <small><?= rupiah($item['price']) ?></small>
                          </div>
                      </div>
                  <?php endforeach; ?>
              </div>
              <?php endif; ?>

              <?php if (!empty($cart_items)): ?>
              <div class="activity-box">
                  <h4><i class="fa fa-shopping-cart"></i> Cart</h4>
                  <?php foreach ($cart_items as $item): ?>
                      <div class="activity-item">
                          <a href="single-product.php?id=<?= $item['id'] ?>">
                              <img src="images/<?= htmlspecialchars($item['photo']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                          </a>
                          <div><b><?= htmlspecialchars($item['name']) ?></b><br>
                              <small>Quantity: <?= (int)($item['quantity'] ?? 1) ?> | <?= rupiah($item['price']) ?></small>
                          </div>
                      </div>
                  <?php endforeach; ?>
              </div>
              <?php endif; ?>
          </div>
      </div>

      <!-- Purchase History -->
      <div class="panel" id="history">
          <div class="panel-body">
              <h3>Purchase History</h3>
              <?php
              $riwayat = mysqli_query($conn, "SELECT * FROM tb_transaksi WHERE id_user='$userId' ORDER BY date DESC");
              if (mysqli_num_rows($riwayat) == 0): ?>
                  <p>No purchase history yet.</p>
              <?php else: ?>
                  <div class="activity-box">
                      <?php while ($row = mysqli_fetch_assoc($riwayat)): ?>
                          <div class="activity-item">
                              <i class="fa fa-file-invoice" style="font-size:24px;margin-right:10px;color:#800020;"></i>
                              <div>
                                  <b>Transaction #<?= $row['id_transaction'] ?></b><br>
                                  <small>
                                      Date: <?= date("d M Y H:i", strtotime($row['date'])) ?><br>
                                      Total: <?= rupiah($row['total_price']) ?><br>
                                      Method: <?= htmlspecialchars($row['payment_method']) ?>
                                  </small><br>
                                  <a href="pages/invoice.php?id_transaction=<?= $row['id_transaction'] ?>" class="btn btn-xs btn-primary" style="margin-top:5px;">
                                      View Invoice
                                  </a>
                              </div>
                          </div>
                      <?php endwhile; ?>
                  </div>
              <?php endif; ?>
          </div>
      </div>

      <!-- Edit Form -->
      <div class="panel" id="edit">
          <div class="panel-body">
              <h3>Edit Profile</h3>
              <?php if (!empty($success_message)): ?>
                  <div class="alert alert-success"><?= $success_message ?></div>
              <?php endif; ?>
              <form method="POST" enctype="multipart/form-data" class="edit-form">
                  <label>Profile Picture</label><br>
                  <img src="images/<?= $userData['profile_picture'] ?? 'default-profile.png' ?>" 
                       style="width:100px; height:100px; border-radius:50%; margin-bottom:10px;" 
                       id="profilePreview"><br>
                  <input type="file" name="profile-photo" id="profile-photo">

                  <label>Full Name</label>
                  <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($userData['fullname']) ?>" required>

                  <label>Username</label>
                  <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($userData['username']) ?>" required>

                  <label>Phone</label>
                  <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($userData['phone']) ?>" id="phoneField">

                  <label>Email</label>
                  <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($userData['email']) ?>" readonly id="emailField">

                  <label>New Password (leave blank if not changing)</label>
                  <input type="password" name="password" class="form-control">

                  <button type="submit" class="btn btn-warning">Save Changes</button>
              </form>
          </div>
      </div>
  </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Preview foto
document.getElementById('profile-photo').addEventListener('change', function(){
    const file = this.files[0];
    if (file) { document.getElementById('profilePreview').src = URL.createObjectURL(file); }
});

// Cuma email yang tidak bisa diubah
document.getElementById('emailField').addEventListener('click', function(){
    Swal.fire({ icon: 'warning', title: 'Tidak Bisa Diedit', text: 'Email tidak bisa diubah.', confirmButtonColor: '#800020' });
});
</script>

</body>
</html>
