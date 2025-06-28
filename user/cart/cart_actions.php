<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$product_price = $_POST['product_price']; 
$product_image = $_POST['product_image'];
$quantity = $_POST['quantity']; 
$selected_color = $_POST['selected_color']; 
$selected_ram = $_POST['selected_ram'];  
$selected_rom = $_POST['selected_rom'];  


if (empty($product_price)) {
    die("Error: Product price is missing.");
}   


$product_price = floatval(str_replace(' ', '', $product_price)); 


var_dump($product_price); 

$product = [
    'id' => $product_id,
    'name' => $product_name,
    'price' => $product_price, 
    'image' => $product_image,
    'quantity' => $quantity,     
    'color' => $selected_color,   
    'ram' => $selected_ram,     
    'rom' => $selected_rom,  
];

$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $product_id && $item['color'] == $selected_color && $item['ram'] == $selected_ram && $item['rom'] == $selected_rom) {
        $item['quantity'] += $quantity;
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = $product;
}

header('Location: cart.php');
exit();
?>