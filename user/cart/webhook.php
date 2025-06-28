<?php
include("../../middleware/adminMiddlware.php");
require "../config.php";

$webhookData = file_get_contents("php://input");
$data = json_decode($webhookData, true);


if (!isset($data['metadata']['payment_id'])) {
    http_response_code(400);
    die("Invalid webhook data.");
}

$payment_id = $data['metadata']['payment_id'];
$status = $data['status'] === "paid" ? "payer" : "annuler";


$query = "UPDATE payments SET status = ? WHERE payment_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ss", $status, $payment_id);
$stmt->execute();
$stmt->close();


$orderQuery = "UPDATE command SET status = ? WHERE id = (
    SELECT order_id FROM payments WHERE payment_id = ?
)";
$stmt = $con->prepare($orderQuery);
$stmt->bind_param("ss", $status, $payment_id);
$stmt->execute();
$stmt->close();

http_response_code(200);
echo json_encode(["message" => "Payment status updated"]);
?>
