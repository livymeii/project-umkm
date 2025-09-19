<?php
session_start();
include "db.php";

$message = "";
$password_ready = "";

// --- Handle Reset Request ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = mysqli_real_escape_string($conn, trim($_POST['identifier']));

    $q = mysqli_query($conn, "SELECT id, username, password_status, reset_request, new_password 
                              FROM users 
                              WHERE username='$identifier' OR email='$identifier' LIMIT 1");

    if (mysqli_num_rows($q) === 0) {
        $message = "Akun tidak ditemukan. Pastikan username/email benar.";
    } else {
        $user = mysqli_fetch_assoc($q);

        if ($user['reset_request'] == 1 && $user['password_status']=='pending') {
            $message = "Permintaan reset password sudah dikirim sebelumnya. Tunggu persetujuan admin.";
        } 
        elseif ($user['password_status'] == 'reset' && !empty($user['new_password'])) {
            $password_ready = "Password baru kamu: <strong>".htmlspecialchars($user['new_password'])."</strong>";
            $message = "Admin telah menyetujui reset password. Gunakan password baru untuk login.";
        } 
        else {
            $new_password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

            mysqli_query($conn, "UPDATE users 
                                 SET reset_request=1, new_password='$new_password', password_status='pending'
                                 WHERE id='{$user['id']}'");

            $message = "Permintaan reset password sudah dikirim ke admin. Silakan tunggu persetujuan.";
        }
    }
}
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Forgot Password - Gifaattire</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
body{font-family:Poppins, sans-serif; background:#f5f5f5; padding:40px;}
.box{max-width:560px;margin:auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.06);}
input{width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:6px;}
button{background:#800020;color:#fff;padding:10px 15px;border:0;border-radius:6px;cursor:pointer;}
.msg{margin:10px 0;padding:10px;border-radius:6px;}
.info{background:#fff4e5;color:#6a4b00;}
.success{background:#e5f7ff;color:#007bbd;}
</style>
</head>
<body>
<div class="box">
  <h2>Lupa Password</h2>
  <form method="post">
    <label>Username atau Email</label>
    <input type="text" name="identifier" placeholder="Masukkan username atau email" required>
    <button type="submit">Reset Password</button>
  </form>

  <?php if(!empty($message)): ?>
  <div class="msg info"><?= $message ?></div>
  <?php endif; ?>

  <?php if(!empty($password_ready)): ?>
  <div class="msg success">
    <?= $password_ready ?>
    <div style="margin-top:10px;">
      <a href="../login.php"><button type="button">Login Sekarang</button></a>
    </div>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
