<?php
session_start();
include "db.php"; // koneksi database

header('Content-Type: application/json');

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'No product ID']);
    exit;
}

$id = (int)$_POST['id'];

// Inisialisasi wishlist
if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];

// Toggle wishlist
$added = false;
if (isset($_SESSION['wishlist'][$id])) {
    unset($_SESSION['wishlist'][$id]);
} else {
    $_SESSION['wishlist'][$id] = true;
    $added = true;
}

// Hitung jumlah wishlist
$wishlistCount = count($_SESSION['wishlist']);

echo json_encode([
    'success' => true,
    'added' => $added,
    'count' => $wishlistCount
]);
exit;
