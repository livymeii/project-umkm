<?php
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include('../service/db.php');

// --- Handle Delete User ---
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $q = mysqli_query($conn, "SELECT role FROM users WHERE id='$del_id'");
    $user = mysqli_fetch_assoc($q);

    if ($user && $user['role'] !== 'admin') {
        mysqli_query($conn, "DELETE FROM users WHERE id='$del_id'");
        $_SESSION['msg'] = "User ID $del_id berhasil dihapus.";
        header("Location: tb-customers.php?deleted=success");
        exit;
    } else {
        $_SESSION['msg'] = "Tidak bisa menghapus admin.";
        header("Location: tb-customers.php");
        exit;
    }
}

// --- Handle Approve Reset Password ---
if(isset($_GET['approve_reset'])){
    $uid = intval($_GET['approve_reset']);

    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT new_password, reset_request FROM users WHERE id='$uid'"));

    if($user && $user['reset_request']==1 && !empty($user['new_password'])){
        $new_pass = $user['new_password'];

        // Update password utama + status reset
        mysqli_query($conn, "UPDATE users 
                             SET password='$new_pass',
                                 password_status='reset',
                                 reset_request=0
                             WHERE id='$uid'");

        $_SESSION['reset_info'] = "Password user ID $uid berhasil direset. Password baru: <strong>".htmlspecialchars($new_pass)."</strong>";
        header("Location: tb-customers.php?reset=success");
        exit;
    }
}

// --- Status Notifikasi ---
$deleted   = ($_GET['deleted'] ?? '') === 'success';
$approved  = isset($_SESSION['reset_info']);
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Dashboard Admin - Customers</title>
<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<style>
body {font-family:'Poppins',sans-serif; margin:0; padding:0;}
.navbar {background:#fff; display:flex; justify-content:space-between; align-items:center; padding:15px 40px; box-shadow:0 2px 5px rgba(0,0,0,0.05); position:sticky; top:0; z-index:1000;}
.logo {font-size:20px; font-weight:600; color:#000; text-decoration:none;}
.nav-links {display:flex; align-items:center; gap:25px;}
.nav-links a {text-decoration:none; color:#444; font-weight:500; transition:0.3s;}
.nav-links a:hover {color:#000;}
.sidebar {position:fixed; top:0; bottom:0; left:0; width:220px; background:#f8f9fa; padding-top:20px;}
.sidebar .nav-link {color:#444; padding:10px 20px; display:block; text-decoration:none;}
.sidebar .nav-link.active {background:#444; color:#fff;}
.main {margin-left:220px; padding:20px;}
table {width:100%; border-collapse:collapse; margin-top:20px; background:#fff;}
th, td {padding:12px; border:1px solid #ddd; text-align:center; vertical-align:middle;}
th {background:#444; color:#fff;}
.btn-delete {background:#c0392b; color:#fff; padding:6px 12px; border:none; border-radius:5px; cursor:pointer;}
.btn-delete:hover {background:#e74c3c;}
.btn-approve {background:#2980b9; color:#fff; padding:6px 12px; border:none; border-radius:5px; cursor:pointer;}
.btn-approve:hover {background:#3498db;}
</style>
</head>
<body>

<div class="main">
  <h1>Data Customers</h1>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Fullname</th>
        <th>Phone</th>
        <th>Created At</th>
        <th>Status Password</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no=1;
      $users = mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");
      while($row=mysqli_fetch_assoc($users)):
      ?>
      <tr>
        <td><?= $no ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['role']) ?></td>
        <td><?= htmlspecialchars($row['fullname']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td>
        <td><?= date("d M Y H:i", strtotime($row['created_at'])) ?></td>
        <td>
          <?php
            $status = $row['password_status'] ?? 'normal';
            if($row['reset_request'] == 1 && $status=='pending') echo '<span style="color:orange;">Pending</span>';
            elseif($status=='reset') echo '<span style="color:blue;">Reset</span>';
            else echo '<span style="color:gray;">Normal</span>';
          ?>
        </td>
        <td>
          <?php if($row['role']!=='admin'): ?>
              <button class="btn-delete" onclick="deleteUser(<?= $row['id'] ?>)">Delete</button>
              <?php if($row['reset_request'] == 1 && $status=='pending'): ?>
                  <button class="btn-approve" onclick="approveReset(<?= $row['id'] ?>)">Approve Reset</button>
              <?php endif; ?>
          <?php else: ?>
            <span style="color:gray;">Admin</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php $no++; endwhile; ?>
    </tbody>
  </table>
</div>

<script>
function deleteUser(id){
  swal({
    title:"Yakin ingin menghapus user ini?",
    text:"Data yang dihapus tidak bisa dikembalikan!",
    icon:"warning",
    buttons:true,
    dangerMode:true
  }).then((willDelete)=>{
    if(willDelete){
      window.location="tb-customers.php?delete_id="+id;
    }
  });
}

function approveReset(id){
  swal({
    title:"Approve Reset Password?",
    text:"Password user akan diaktifkan dan bisa login dengan password baru!",
    icon:"warning",
    buttons:true
  }).then((willApprove)=>{
    if(willApprove){
      window.location="tb-customers.php?approve_reset="+id;
    }
  });
}

<?php if($deleted): ?> swal("Sukses!","User berhasil dihapus.","success"); <?php endif; ?>
<?php if($approved): ?> swal("Sukses!","<?= $_SESSION['reset_info'] ?>","success"); <?php unset($_SESSION['reset_info']); endif; ?>
</script>

<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
