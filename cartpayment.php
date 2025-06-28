<?php

session_start();
include("middleware/adminMiddlware.php");
require "config.php"; 

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("Votre panier est vide.");
}

$cartData = $_SESSION['cart'];
$totalPrice = 0;

foreach ($cartData as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

$order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : null;
if (!$order_id) {
    die("Error: Order ID is missing.");
}


$queryCity = "SELECT city FROM command WHERE id = ?";
$stmtCity = $con->prepare($queryCity);
$stmtCity->bind_param("i", $order_id);
$stmtCity->execute();
$resultCity = $stmtCity->get_result();
$rowCity = $resultCity->fetch_assoc();
$city = $rowCity['city'];
$stmtCity->close();


$queryShipping = "SELECT price FROM shipping WHERE place = ?";
$stmtShipping = $con->prepare($queryShipping);
$stmtShipping->bind_param("s", $city);
$stmtShipping->execute();
$resultShipping = $stmtShipping->get_result();
$rowShipping = $resultShipping->fetch_assoc();
$shippingCost = $rowShipping ? $rowShipping['price'] : 0; 
$stmtShipping->close();

$totalAmount = $totalPrice + $shippingCost;


$updateOrderQuery = "UPDATE command SET status = 'en attente' WHERE id = ?";
$stmt = $con->prepare($updateOrderQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->close();

try {
    $checkout = $chargily_pay->checkouts()->create([
        "metadata" => [
            "order_id" => $order_id,
        ],
        "locale" => "fr",
        "amount" => $totalAmount,
        "chargily_pay_fees_allocation" => "customer",
        "currency" => "dzd",

        "success_url" => "http://localhost/ecommerce__website/user/cart/sucess_url.php?order_id=$order_id",
        "failure_url" => "http://localhost/ecommerce__website/user/cart/cancle_handler.php?order_id=$order_id",
        "webhook_endpoint" => "http://localhost/ecommerce__website/user/cart/webhook.php",
    ]);

    header("Location: " . $checkout->getUrl());
    exit();
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}

?>
