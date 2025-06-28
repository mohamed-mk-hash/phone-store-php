<?php
session_start();
include "../../middleware/adminMiddlware.php"; 


$userId = $_SESSION['user_id'];

$query = "INSERT INTO payment (user_id) VALUES (?)";
$stmt = mysqli_prepare($con, $query);

if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
    if (mysqli_stmt_execute($stmt)) {
                redirect("../cart/checkout.php", "card informations saved sucefully");
        exit();     } else {
                echo "Error: Unable to insert payment record.";
    }
    mysqli_stmt_close($stmt);
} else {
        echo "Error: Database query failed.";
}

mysqli_close($con);
?>
