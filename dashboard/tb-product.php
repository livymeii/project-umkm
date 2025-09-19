<?php
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include('../service/db.php');

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

$name = $price = $stock = $photo = $description = "";
$id_kategori = "";

// Ambil data untuk edit
if ($action == "edit" && $id != "") {
    $result = mysqli_query($conn, "SELECT * FROM tb_produk WHERE id = '$id'");
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        $name = $row['name'];
        $price = $row['price'];
        $stock = $row['stock'];
        $photo = $row['photo'];
        $description = $row['description'];
        $id_kategori = $row['id_kategori'];
    }
}

// Hapus produk
if ($action == "hapus" && $id != '') {
    mysqli_query($conn, "DELETE FROM tb_produk WHERE id='$id'");
    $_SESSION['status'] = 'hapus';
    header("Location: tb-product.php");
    exit;
}

// Tambah / edit produk
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_post = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $description = $_POST['description'] ?? '';
    $id_kategori = $_POST['id_kategori'] ?? '';

    $photo_name = $_FILES['photo']['name'] ?? '';
    $photo_tmp = $_FILES['photo']['tmp_name'] ?? '';
    $photo_lama = $_POST['photo_lama'] ?? '';
    $path = "../images/" . $photo_name;

    if ($action == "edit" && $id_post != "") {
        $getld = mysqli_query($conn, "SELECT * FROM tb_produk WHERE id='$id_post'");
        $olddata = mysqli_fetch_assoc($getld);

        if ($photo_name == '') {
            $photo_name = $olddata['photo'];
        } else {
            move_uploaded_file($photo_tmp, $path);
        }

        mysqli_query($conn, "UPDATE tb_produk SET 
            name='$name',
            price='$price',
            stock='$stock',
            photo='$photo_name',
            id_kategori='$id_kategori',
            description='$description' 
            WHERE id='$id_post'");

        $_SESSION['status'] = 'edit';
        header("Location: tb-product.php");
        exit;
    } else {
        move_uploaded_file($photo_tmp, $path);
        mysqli_query($conn, "INSERT INTO tb_produk (name, price, stock, photo, id_kategori, description)
            VALUES ('$name', '$price', '$stock', '$photo_name', '$id_kategori', '$description')");

        $_SESSION['status'] = 'tambah';
        header("Location: tb-product.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Dashboard Admin - Products</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<style>
body {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
  background: #f5f5f5;
}

/* --- NAVBAR --- */
.navbar {
  background: #fff;
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
.nav-links a:hover { color: #000; }

/* --- SIDEBAR --- */
.sidebar {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  width: 220px;
  background: #f8f9fa;
  padding-top: 20px;
}
.sidebar .nav-link {
  color: #444;
  padding: 10px 20px;
  display: block;
  text-decoration: none;
  border-radius: 5px;
  margin: 5px 10px;
}
.sidebar .nav-link.active {
  background: #444;
  color: #fff;
}

/* --- MAIN CONTENT --- */
.main {
  margin-left: 220px;
  padding: 20px;
}

/* --- TABLE STYLE --- */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
table th, table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #eee;
}
table th {
  background: #444;
  color: #fff;
  font-weight: 600;
}
table tr:hover {
  background: #f9f9f9;
}
.action-btn {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 13px;
  text-decoration: none;
  margin: 0 3px;
  transition: 0.3s;
}
.action-btn.edit {
  background: #666;
  color: #fff;
}
.action-btn.edit:hover { background: #333; }
.action-btn.delete {
  background: #999;
  color: #fff;
}
.action-btn.delete:hover { background: #555; }

/* --- FORM STYLE --- */
form {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  margin-bottom: 30px;
  max-width: 700px;
}
form h3 {
  margin-bottom: 20px;
  color: #333;
}
form input, 
form select, 
form textarea {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 14px;
  margin-bottom: 15px;
  transition: all 0.3s;
}
form input:focus, 
form select:focus, 
form textarea:focus {
  border-color: #666;
  outline: none;
  box-shadow: 0 0 5px rgba(100,100,100,0.3);
}
form textarea {
  min-height: 100px;
  resize: vertical;
}
form button {
  background: #444;
  color: #fff;
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  cursor: pointer;
  transition: background 0.3s;
}
form button:hover {
  background: #222;
}

/* --- RESPONSIVE --- */
@media (max-width: 768px) {
  .sidebar {
    width: 180px;
  }
  .main {
    margin-left: 180px;
  }
  table th, table td {
    font-size: 13px;
    padding: 10px;
  }
  .nav-links {
    gap: 15px;
  }
}

</style>

</head>
<body>

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
    <i class="fas fa-sign-out-alt"></i> Logout
  </a>
</nav>

<!-- Sidebar -->
<div class="sidebar">
  <a class="nav-link" href="index.php">Dashboard</a>
  <a class="nav-link" href="tb-transaction.php">Orders</a>
  <a class="nav-link active" href="tb-product.php">Products</a>
  <a class="nav-link" href="tb-customers.php">Customers</a>
</div>

<!-- Main -->
<div class="main">
  <h1>Data Produk</h1>

  <h3><?= $action == "edit" ? "Edit Produk" : "Tambah Produk Baru" ?></h3>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= @$id ?>">
    <input type="text" name="name" value="<?= @$name ?>" placeholder="Name" required>
    <input type="number" name="price" value="<?= @$price ?>" placeholder="Price" required>
    <input type="number" name="stock" value="<?= @$stock ?>" placeholder="Stock" required>
    <?php if ($action == "edit" && $photo): ?>
      <div style="margin-bottom: 10px;">
        <img src="../images/<?= $photo ?>" alt="Foto Produk" style="max-height: 100px;">
        <input type="hidden" name="photo_lama" value="<?= $photo ?>">
      </div>
    <?php endif; ?>
    <input type="file" name="photo" <?= $action != "edit" ? "required" : "" ?>>
    <label for="id_kategori">Category</label>
    <select class="form-select" name="id_kategori" required>
      <option value="">Choose Category</option>
      <?php
      $qKategori = mysqli_query($conn, "SELECT * FROM tb_kategori ORDER BY nama_kategori ASC");
      while ($kat = mysqli_fetch_assoc($qKategori)) {
          $selected = ($kat['id_kategori'] == @$id_kategori) ? "selected" : "";
          echo "<option value='{$kat['id_kategori']}' $selected>{$kat['nama_kategori']}</option>";
      }
      ?>
    </select>
    <textarea name="description" placeholder="Description" required><?= @$description ?></textarea>
    <button type="submit"><?= $action == "edit" ? "Update Produk" : "Add Product" ?></button>
  </form>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Photo</th>
        <th>Category</th>
        <th>Description</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $no = 1;
    $result = mysqli_query($conn, 
      "SELECT p.*, k.nama_kategori 
       FROM tb_produk p 
       LEFT JOIN tb_kategori k ON p.id_kategori = k.id_kategori 
       ORDER BY p.id DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['name']}</td>
                <td>Rp " . number_format($row['price'], 0, ',', '.') . "</td>
                <td>{$row['stock']}</td>
                <td><img src='../images/{$row['photo']}' alt='foto' style='max-height:60px'></td>
                <td>{$row['nama_kategori']}</td>
                <td>{$row['description']}</td>
                <td>
                  <a href='?action=edit&id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                  <a href='?action=hapus&id={$row['id']}' class='btn btn-sm btn-danger btn-delete'>Delete</a>
                </td>
              </tr>";
        $no++;
    }
    ?>
    </tbody>
  </table>
</div>

<script>
document.querySelectorAll(".btn-delete").forEach(button => {
  button.addEventListener("click", function(e) {
    e.preventDefault();
    const url = this.getAttribute("href");

    swal({
      title: "Yakin ingin menghapus produk?",
      text: "Data produk akan terhapus permanen!",
      icon: "warning",
      buttons: ["Batal", "Hapus"],
      dangerMode: true,
    }).then((willDelete) => {
      if (willDelete) {
        window.location.href = url;
      }
    });
  });
});

<?php if (isset($_SESSION['status'])): ?>
swal("Sukses!", 
  "<?php 
    if ($_SESSION['status'] == 'tambah') echo 'Produk berhasil ditambahkan';
    elseif ($_SESSION['status'] == 'edit') echo 'Produk berhasil diupdate';
    elseif ($_SESSION['status'] == 'hapus') echo 'Produk berhasil dihapus';
  ?>", 
  "success"
);
<?php unset($_SESSION['status']); endif; ?>
</script>

<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
