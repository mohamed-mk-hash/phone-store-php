<?php
session_start();
include("../../middleware/adminMiddlware.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id']; 
    $product_id = $_POST['product_id'];
    $image = $_POST['product_image'];
    

    $selling_price = preg_replace('/[^0-9]/', '', $_POST['product_price']); 
    $selling_price = intval($selling_price); 


    $check_query_product = "SELECT id FROM products WHERE id = ? LIMIT 1";
    $stmt_product = $con->prepare($check_query_product);
    $stmt_product->bind_param('i', $product_id);
    $stmt_product->execute();
    $stmt_product->store_result();
    
    if ($stmt_product->num_rows === 0) {
        redirect($_SERVER['HTTP_REFERER'], 'Product not found');
    }

    $check_query = "SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?";
    $check_stmt = $con->prepare($check_query);
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        redirect($_SERVER['HTTP_REFERER'], 'Ce produit est déjà dans votre liste de souhaits');
    } else {
        $query = "INSERT INTO wishlist (user_id, product_id, image, selling_price) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("iisi", $user_id, $product_id, $image, $selling_price);

        if ($stmt->execute()) {
            redirect("wishlist.php", "Produit ajouté à votre liste de souhaits avec succès !");
        } else {
            redirect($_SERVER['HTTP_REFERER'], 'Impossible dajouter le produit à la liste de souhaits');
        }

        $stmt->close();
    }

    $check_stmt->close();
    $stmt_product->close();
}

$con->close();
?>
