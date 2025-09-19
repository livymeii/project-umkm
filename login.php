<?php
session_start();
include("service/db.php");

$message = "";

// --- REGISTER ---
if (isset($_POST['register'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $phone    = $_POST['phone'];
    $email    = $_POST['email'];
    $address  = $_POST['address'];
    // Simpan plain text password sesuai permintaan
    $password = $_POST['password'];
    $role     = "user";

    // Cek username sudah ada
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Username sudah digunakan, silakan pilih yang lain.";
    } else {
        mysqli_query($conn, "INSERT INTO users (fullname, username, phone, email, password, role, address) 
                             VALUES ('$fullname', '$username', '$phone', '$email', '$password', '$role', '$address')");
        $message = "Registrasi berhasil! Silakan login.";
    }
}

// --- LOGIN ---
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        // Bandingkan plain text password (sesuai permintaan)
        if ($password === $row['password']) {
            $_SESSION['is_login'] = true;
            $_SESSION['id']       = $row['id'];
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role'];

            if ($row['role'] === 'admin') {
                header("Location: dashboard/index.php");
                exit;
            } else {
                header("Location: index.php");
                exit;
            }
        } else {
            $message = "Login gagal, username atau password salah.";
        }
    } else {
        $message = "Login gagal, username atau password salah.";
    }
}

// --- Redirect kalau sudah login ---
if (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true) {
    if ($_SESSION['role'] === 'user') {
        header("Location: index.php");
        exit;
    } elseif ($_SESSION['role'] === 'admin') {
        header("Location: dashboard/index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login/Register - Gifaattire</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="css/login-register.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>
<div class="container" id="container">

    <!-- Form Registrasi -->
    <div class="form-container sign-up-container">
        <form action="" method="POST">
            <h1>Create Account</h1>
            <?php if ($message && isset($_POST['register'])): ?>
                <div style="color: green; text-align: center; margin-bottom: 10px;">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            <input type="text" name="fullname" placeholder="Fullname" required />
            <input type="text" name="username" placeholder="Username" required />
            <input type="text" name="phone" placeholder="Phone" required />
            <input type="email" name="email" placeholder="Email" required />
            <input type="text" name="address" placeholder="Address" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit" name="register">Sign Up</button>
        </form>
    </div>

    <!-- Form Login -->
    <div class="form-container sign-in-container">
        <form action="" method="POST">
            <h1>Sign In</h1>
            <?php if ($message && isset($_POST['login'])): ?>
                <div style="color: red; text-align: center; margin-bottom: 10px;">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            <input type="text" name="username" placeholder="Username" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit" name="login">Sign In</button>
            <!-- Tambahkan ini di bawah tombol Sign In di form login -->
<p style="text-align:center; margin-top:8px;">
  <a href="service/forgot-password.php" style="color:#800020; text-decoration:underline;">Forgot password?</a>
</p>

            <a href="index.php">Back to home</a>
        </form>
    </div>

    <!-- Overlay -->
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Welcome Back!</h1>
                <p>To keep connected with us please login with your personal info</p>
                <button class="ghost" id="signIn">Sign In</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Hello, Friend!</h1>
                <p>Enter your personal details and start journey with us</p>
                <button class="ghost" id="signUp">Sign Up</button>
            </div>
        </div>
    </div>

</div>
<script src="js/java.js"></script>
</body>
</html>
