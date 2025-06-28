<?php
session_start();
include("../../middleware/adminMiddlware.php");
require "../../config.php";
use PHPMailer\PHPMailer\PHPMailer;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

if (!isset($_GET['order_id'])) {
    die("Invalid request");
}

$order_id = $_GET['order_id'];


$query = "SELECT email, city FROM command WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$command = $result->fetch_assoc();
$stmt->close();

if (!$command) {
    die("Order not found.");
}

$email = $command['email'];
$city = $command['city'];

$query = "SELECT c.product_id, c.quantity, c.total_price, p.name AS product_name 
          FROM command c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
    $total_price += $row['total_price'];
}
$stmt->close();

foreach ($orders as $order) {
    $product_id = $order['product_id'];
    $quantity_ordered = $order['quantity'];

    $updateQtyQuery = "UPDATE products SET qty = qty - ? WHERE id = ?";
    $qtyStmt = $con->prepare($updateQtyQuery);
    $qtyStmt->bind_param("ii", $quantity_ordered, $product_id);
    $qtyStmt->execute();
    $qtyStmt->close();
}

$shippingQuery = "SELECT price FROM shipping WHERE place = ?";
$shippingStmt = $con->prepare($shippingQuery);
$shippingStmt->bind_param("s", $city);
$shippingStmt->execute();
$shippingResult = $shippingStmt->get_result();
$shippingRow = $shippingResult->fetch_assoc();
$shipping_price = $shippingRow['price'] ?? 0;
$shippingStmt->close();

$total_with_shipping = $total_price + $shipping_price;


$orderUpdateQuery = "UPDATE command SET status = 'paye' WHERE id = ?";
$stmt = $con->prepare($orderUpdateQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->close();


$invoiceHTML = "<h2>Facture de votre commande</h2>
    <p>Merci pour votre achat. Voici les détails de votre commande :</p>
    <table border='1' cellspacing='0' cellpadding='5' style='border-collapse: collapse; width: 100%;'>
        <tr>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix Unitaire</th>
            <th>Sous-total</th>
        </tr>";

foreach ($orders as $order) {
    $product_name = htmlspecialchars($order['product_name']);
    $quantity = $order['quantity'];
    $price_per_product = $order['total_price'] / max($quantity, 1);
    $subtotal = number_format($order['total_price'], 2) . " DZD";

    $invoiceHTML .= "
        <tr>
            <td>{$product_name}</td>
            <td>{$quantity}</td>
            <td>" . number_format($price_per_product, 2) . " DZD</td>
            <td>{$subtotal}</td>
        </tr>";
}

$invoiceHTML .= "
    <tr>
        <td colspan='3' style='text-align:right; font-weight:bold;'>Frais de livraison ($city):</td>
        <td>" . number_format($shipping_price, 2) . " DZD</td>
    </tr>
    <tr>
        <td colspan='3' style='text-align:right; font-weight:bold;'>Total à payer:</td>
        <td>" . number_format($total_with_shipping, 2) . " DZD</td>
    </tr>
</table>
<p>Merci d'avoir acheté chez <strong>Coin Mobile</strong> !</p>";


$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'aminemokhtari028@gmail.com';
    $mail->Password = 'nssisuinqrccsgod';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom('aminemokhtari028@gmail.com', 'Coin Mobile');
    $mail->addAddress($email, 'Client');
    $mail->isHTML(true);
    $mail->Subject = "Votre Facture - Coin Mobile";
    $mail->Body = $invoiceHTML;

    $mail->send();
} catch (Exception $e) {
    die("Erreur lors de l'envoi de l'email: " . $mail->ErrorInfo);
}

unset($_SESSION['cart']);
redirect("../index.php","Commande passée avec succès !");
exit();
?>
