<?php
session_start();

if (!isset($_GET['index'])) {
    die("Item tidak ditemukan.");
}

$index = intval($_GET['index']);

if (isset($_SESSION['wishlist'][$index])) {
    unset($_SESSION['wishlist'][$index]);
    $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // rapihin index array
}

header("Location: ../wishlist.php");
exit;
session_start();

if (isset($_GET['index']) && is_numeric($_GET['index'])) {
    $index = intval($_GET['index']);
    if (isset($_SESSION['wishlist'][$index])) {
        unset($_SESSION['wishlist'][$index]);
        $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
    }
}

header("Location: ../wishlist.php");
exit();
