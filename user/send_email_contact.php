<?php
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = htmlspecialchars($_POST["firstName"]);
    $lastName = htmlspecialchars($_POST["lastName"]);
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $number = htmlspecialchars($_POST["number"]);
    $message = htmlspecialchars($_POST["message"]);

    if (!$email) {
        echo json_encode(["success" => false, "message" => "Email invalide."]);
        exit;
    }

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'aminemokhtari028@gmail.com';
        $mail->Password = 'nssisuinqrccsgod';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('aminemokhtari028@gmail.com', 'Coin Mobile');
        $mail->addAddress("aminemokhtari028@gmail.com", "Admin");

        $mail->isHTML(true);
        $mail->Subject = "Nouveau message de $firstName $lastName";
        $mail->Body = "
            <h3>Vous avez reçu un nouveau message :</h3>
            <p><strong>Nom :</strong> $firstName $lastName</p>
            <p><strong>Email :</strong> $email</p>
            <p><strong>Téléphone :</strong> $number</p>
            <p><strong>Message :</strong></p>
            <p>$message</p>
        ";

        if ($mail->send()) {
            echo json_encode(["success" => true, "message" => "Message envoyé avec succès."]);
        } else {
            echo json_encode(["success" => false, "message" => "Erreur lors de l'envoi."]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Erreur: " . $mail->ErrorInfo]);
    }
}
?>
