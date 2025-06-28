<?php
session_start();
require("fpdf186/fpdf.php");
include("../adminto/main/config/functions/myfunctions.php");



$created_at = $_GET['created_at'];
$user_id = $_SESSION['user_id'];

$query = "SELECT c.product_id, c.quantity, c.total_price, p.name AS product_name, c.city 
          FROM command c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ? AND c.created_at = ?";

$stmt = $con->prepare($query);
$stmt->bind_param("is", $user_id, $created_at);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
} else {
    die("Commande introuvable !");
}

$city = $orders[0]['city']; 
$shippingQuery = "SELECT price FROM shipping WHERE place = ?";
$shippingStmt = $con->prepare($shippingQuery);
$shippingStmt->bind_param("s", $city);
$shippingStmt->execute();
$shippingResult = $shippingStmt->get_result();
$shippingRow = $shippingResult->fetch_assoc();
$shipping_price = $shippingRow['price'] ?? 0;

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 20);

$pdf->SetFillColor(50, 50, 50); 
$pdf->SetTextColor(255, 255, 255); 
$pdf->Cell(190, 12, "FACTURE", 0, 1, 'C', true);
$pdf->Ln(5);
$pdf->SetTextColor(0, 0, 0); 

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(190, 10, "Date: " . date("j F, Y H:i:s", strtotime($created_at)), 0, 1, 'R');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(220, 220, 220); 
$pdf->Cell(80, 10, "Produit", 1, 0, 'C', true);
$pdf->Cell(30, 10, "Quantite", 1, 0, 'C', true);
$pdf->Cell(40, 10, "Prix", 1, 0, 'C', true);
$pdf->Cell(40, 10, "Sous-total", 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
$total_price = 0;

foreach ($orders as $order) {
    $quantity = $order['quantity'];
    $total_price_db = $order['total_price'];
    

    $price_per_product = $total_price_db / max($quantity, 1);
    
    $subtotal = $price_per_product * $quantity;
    
    $product_name = mb_convert_encoding($order['product_name'], 'ISO-8859-1', 'UTF-8');
    $productCellWidth = 80; 
    $priceCellWidth = 40;   
    $lineHeight = 8;

    $numLines = 0;
    $tempText = "";
    $words = explode(" ", $product_name);

    foreach ($words as $word) {
        if ($pdf->GetStringWidth($tempText . " " . $word) < $productCellWidth) {
            $tempText .= " " . $word;
        } else {
            $numLines++;
            $tempText = $word;
        }
    }
    if (!empty($tempText)) {
        $numLines++;
    }

    $cellHeight = $numLines * $lineHeight;
    
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $pdf->MultiCell($productCellWidth, $lineHeight, $product_name, 1, 'L');

    $pdf->SetXY($x + $productCellWidth, $y);
    $pdf->Cell(30, $cellHeight, $quantity, 1, 0, 'C');
    $pdf->Cell($priceCellWidth, $cellHeight, number_format($price_per_product, 2) . " DZD", 1, 0, 'C'); 
    $pdf->Cell(40, $cellHeight, number_format($subtotal, 2) . " DZD", 1, 1, 'C');

    $total_price += $subtotal;
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(150, 10, "Prix de livraison ($city)", 1, 0, 'C', true);
$pdf->Cell(40, 10, number_format($shipping_price, 2) . " DZD", 1, 1, 'C', true);

$total_with_shipping = $total_price + $shipping_price;
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(150, 10, "Total a payer", 1, 0, 'C', true);
$pdf->Cell(40, 10, number_format($total_with_shipping, 2) . " DZD", 1, 1, 'C', true);

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 12);
$pdf->MultiCell(190, 8, "Merci d'avoir achete chez Coin Mobile !\nPour toute assistance, contactez notre service client.", 0, 'C');

$pdf->Output("D", "Facture_" . date("Y-m-d_H-i-s", strtotime($created_at)) . ".pdf");

?>
