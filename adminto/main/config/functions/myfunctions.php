<?php 

include(__DIR__ . "/../dbcon.php"); 



function getAll($table){

    global $con;
    $query = "SELECT * FROM $table";
    return $query_run = mysqli_query($con, $query);

}

function getcategory($type_name) {
    global $con;
    $query = "
        SELECT categories.*, type.name AS type_name 
        FROM categories 
        INNER JOIN type ON categories.type_id = type.id
        WHERE type.name = '$type_name'
    ";
    return $query_run = mysqli_query($con, $query);
}





function getPopularProductsByCategory($table) {
    global $con;
    $query = "SELECT p.*, c.name AS category_name 
              FROM $table p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.popular = 1";  
    return mysqli_query($con, $query);
}



function getById($table, $id) {
    global $con;
    
    
    $table = mysqli_real_escape_string($con, $table);
    $id = mysqli_real_escape_string($con, $id);

    
    $query = "SELECT * FROM `$table` WHERE id = '$id'";

    return mysqli_query($con, $query);
}


function getByIdproduct($table, $id){
    global $con;
    $query = "
        SELECT products.*, product_images.image
        FROM $table
        LEFT JOIN product_images ON product_images.product_id = products.id
        WHERE products.id = '$id'
    ";
    return mysqli_query($con, $query);
}


function redirect($url, $message) {
    $_SESSION['message'] = $message; 
    header('Location: ' . $url); 
  
}


function getpbyc($table) {
    global $con;
    $query = "SELECT p.*, c.name AS category_name 
              FROM $table p 
              LEFT JOIN categories c ON p.category_id = c.id";
    return mysqli_query($con, $query);
}

function getProductsByCategory($category_id) {
    global $con;
    $query = "SELECT p.*, c.name AS category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.category_id = " . intval($category_id); 
    
    return mysqli_query($con, $query);
}






function getNameActive($table, $name){
    global $con;
    $query = "SELECT * FROM $table WHERE id='$name' LIMIT 1";
    return mysqli_query($con, $query);
}


function getAllProducts() {
    global $con;
    $query = "
        SELECT 
            p.*, 
            c.name AS category_name,
            COALESCE(MIN(rr.price), p.price) AS min_price 
        FROM 
            products p 
        LEFT JOIN 
            categories c ON p.category_id = c.id 
        LEFT JOIN 
            ram_rom rr ON p.id = rr.product_id 
        GROUP BY 
            p.id
    ";
    return mysqli_query($con, $query);
}



function getProductById($id) {
    global $con;
    $query_product = "SELECT p.*, c.name AS category_name 
                      FROM products p
                      LEFT JOIN categories c ON p.category_id = c.id
                      WHERE p.id = '$id'";
    $result_product = mysqli_query($con, $query_product);
    return $result_product; 
}




function getUsersProductCount() {
    global $con; 
    $query = "SELECT c.user_id, u.username, c.created_at, c.phone, c.email, c.city, c.address, COUNT(*) AS product_count
    FROM command c
    JOIN users u ON c.user_id = u.id
    WHERE c.status = 'paye'
    GROUP BY c.created_at;";  
    return mysqli_query($con, $query);  
}


function getUserProductsByDate($date) {
    global $con;
    
    $query = "SELECT c.product_id, p.name, c.quantity, c.color, c.ram, c.rom, c.total_price, c.Acive, c.payment_methode, pi.image, s.price AS shipping_price 
          FROM command c
          JOIN products p ON c.product_id = p.id
          LEFT JOIN product_images pi ON c.product_id = pi.product_id
          LEFT JOIN shipping s ON c.city = s.place  
          WHERE c.created_at = '$date'
          GROUP BY c.product_id"; 

    return mysqli_query($con, $query);
}



function activateAllProductsByDate($date) {
    global $con;
    
    $query = "UPDATE command SET Acive = 1 WHERE created_at = '$date' AND Acive = 0";
    
    return mysqli_query($con, $query);
}



function countRows($table) {
    global $con;
    $query = "SELECT COUNT(*) as total FROM $table";
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    } else {
        return 0;
    }
}












?>