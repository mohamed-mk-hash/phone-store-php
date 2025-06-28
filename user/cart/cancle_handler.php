<?php
session_start();
include("../../middleware/adminMiddlware.php");
require "../../config.php";

if (!isset($_GET['order_id'])) {
    die("Invalid request");
}

$order_id = $_GET['order_id'];


$orderQuery = "UPDATE command SET status = 'annuler' WHERE id = ?";
$stmt = $con->prepare($orderQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->close();

redirect("checkout.php","Paiement annulé");
exit();
?>
