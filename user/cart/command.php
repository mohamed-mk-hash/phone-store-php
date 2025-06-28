<?php
session_start();
include("../../middleware/adminMiddlware.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../../config.php';

if (isset($_POST['place_order_btn'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $cityId = $_POST['city'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $cartData = isset($_POST['cart_data']) ? json_decode($_POST['cart_data'], true) : [];
    $userId = $_SESSION['user_id'];
    $paymentMethod = $_POST['payment_method'];

    if (!$name || !$address || !$cityId || !$phone || !$email || !$cartData) {
        die("All fields are required.");
    }

    $cityQuery = "SELECT place, price FROM shipping WHERE id = ?";
    $stmt = $con->prepare($cityQuery);
    $stmt->bind_param("i", $cityId);
    $stmt->execute();
    $cityResult = $stmt->get_result();
    if (!$cityResult || $cityResult->num_rows == 0) {
        die("Invalid city selected.");
    }
    $cityRow = $cityResult->fetch_assoc();
    $cityName = $cityRow['place'];
    $shippingPrice = $cityRow['price'];

    $totalOrderPrice = 0;

    $invoiceDetails = "<h3>Facture pour votre commande</h3>";
    $invoiceDetails .= "<p>Bonjour <strong>$name</strong>,</p>";
    $invoiceDetails .= "<p>Merci pour votre commande auprès de Coin Mobile ! Vous trouverez ci-dessous les détails :</p>";
    $invoiceDetails .= "<table border='1' cellspacing='0' cellpadding='5' width='100%'>";
    $invoiceDetails .= "<tr>
                            <th>Nom du produit</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Sous-total</th>
                        </tr>";

    foreach ($cartData as $item) {
        $product_id = $item['id'];
        $product_name = $item['name'];
        $quantity = $item['quantity'];
        $unitPrice = $item['price'];
        $itemTotalPrice = $unitPrice * $quantity;
        $totalOrderPrice += $itemTotalPrice;

        $color = isset($item['color']) ? $item['color'] : null;
        $ram = isset($item['ram']) ? $item['ram'] : null;
        $rom = isset($item['rom']) ? $item['rom'] : null;

        $orderQuery = "INSERT INTO command (product_id, address, user_id, city, phone, email, quantity, total_price, color, ram, rom, payment_methode)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($orderQuery);
        $stmt->bind_param("isisssisssss", $product_id, $address, $userId, $cityName, $phone, $email, $quantity, $itemTotalPrice, $color, $ram, $rom, $paymentMethod);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $orderId = $stmt->insert_id;
            $_SESSION['order_id'] = $orderId;

            // ✅ Update stock
            $updateQtyQuery = "UPDATE products SET qty = qty - ? WHERE id = ?";
            $qtyStmt = $con->prepare($updateQtyQuery);
            $qtyStmt->bind_param("ii", $quantity, $product_id);
            $qtyStmt->execute();
            $qtyStmt->close();

            // ✅ Set status to "paye" if cash
            if ($paymentMethod === 'cash') {
                $statusQuery = "UPDATE command SET status = 'paye' WHERE id = ?";
                $statusStmt = $con->prepare($statusQuery);
                $statusStmt->bind_param("i", $orderId);
                $statusStmt->execute();
                $statusStmt->close();
            }
        } else {
            die("Erreur lors de l'enregistrement de la commande : " . $stmt->error);
        }

        $invoiceDetails .= "<tr>
                                <td>$product_name</td>
                                <td align='center'>$quantity</td>
                                <td align='right'>" . number_format($unitPrice, 2) . " DZD</td>
                                <td align='right'>" . number_format($itemTotalPrice, 2) . " DZD</td>
                            </tr>";
    }

    $totalWithShipping = $totalOrderPrice + $shippingPrice;

    $invoiceDetails .= "<tr>
                            <td colspan='3' align='right'><strong>Frais de livraison ($cityName)</strong></td>
                            <td align='right'><strong>" . number_format($shippingPrice, 2) . " DZD</strong></td>
                        </tr>";
    $invoiceDetails .= "<tr>
                            <td colspan='3' align='right'><strong>Total à payer</strong></td>
                            <td align='right'><strong>" . number_format($totalWithShipping, 2) . " DZD</strong></td>
                        </tr>";
    $invoiceDetails .= "</table>";
    $invoiceDetails .= "<p>Salutations,<br><strong>Coin Mobile</strong></p>";

    // ✅ Send invoice if cash
    if ($paymentMethod === 'cash') {
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
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = "Facture pour votre commande";
            $mail->Body = $invoiceDetails;
            $mail->send();
        } catch (Exception $e) {
            die("Erreur lors de l'envoi de l'e-mail : " . $mail->ErrorInfo);
        }
    }

    if ($paymentMethod === 'card') {
        $_SESSION['cart'] = $cartData;
        header("Location: ../../cartpayment.php?message=Payment Redirected");
        exit();
    } elseif ($paymentMethod === 'cash') {
        unset($_SESSION['cart']);
        redirect("../index.php", "Commande passée avec succès !");
        exit();
    }
}
?>
