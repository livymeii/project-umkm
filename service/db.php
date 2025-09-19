<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "login_register_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
