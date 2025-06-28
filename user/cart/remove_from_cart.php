<?php
session_start();

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

        foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
}

header('Location: cart.php');
exit();
?>
