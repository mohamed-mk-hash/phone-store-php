<?php
include("../../middleware/adminMiddlware.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];

    // Debugging output
    echo "User ID: $user_id, Product ID: $product_id"; // Check values

    // Remove the product from the wishlist
    $query = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("is", $user_id, $product_id);

    if ($stmt->execute()) {
        // Redirect on success
        header("Location: wishlist.php");
        exit; // Make sure to exit after header redirection
    } else {
        // Error handling
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$con->close();
?>
