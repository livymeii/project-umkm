<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['cart'])) {

    // Update quantity
    if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $index => $qty) {
            $qty = intval($qty);
            if ($qty <= 0) {
                unset($_SESSION['cart'][$index]);
            } else {
                $_SESSION['cart'][$index]['quantity'] = $qty;
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']); // rapihin index
    }

    // Remove item (opsional via POST)
    if (isset($_POST['remove']) && is_numeric($_POST['remove'])) {
        $remove_index = intval($_POST['remove']);
        unset($_SESSION['cart'][$remove_index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

header("Location: ../cart.php");
exit;
