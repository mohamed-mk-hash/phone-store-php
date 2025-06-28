<?php 
session_start();
include(__DIR__ . "/../main/config/dbcon.php");
include(__DIR__ . "/../main/config/functions/myfunctions.php");


use PHPMailer\PHPMailer\PHPMailer;

require '../../user/phpmailer/src/Exception.php';
require '../../user/phpmailer/src/PHPMailer.php';
require '../../user/phpmailer/src/SMTP.php';



if(isset($_POST['add_shipping-btn'])){
    $price = $_POST['price'];  
    $place = $_POST['place'];

    $check_place_query = "SELECT place FROM shipping WHERE LOWER(place) = LOWER('$place')";
    $check_place_query_run = mysqli_query($con, $check_place_query);

    if(mysqli_num_rows($check_place_query_run) > 0) {
        $_SESSION['message'] = "Le lieu de livraison existe déjà.";
        header('Location: add-shipping.php');
        exit(); 
    } else {
        $cat_query = "INSERT INTO shipping (place, price) VALUES ('$place', '$price')";
        $cat_query_run = mysqli_query($con, $cat_query);
        
        if($cat_query_run){
            $_SESSION['message'] = "Lieu de livraison ajouté avec succès.";
            header('Location: shipping.php');
            exit(); 
        } else {
            $_SESSION['message'] = "Quelque chose s'est mal passé.";
            header('Location: add-shipping.php');
            exit(); 
        }
    }
}

else if (isset($_POST['delete_shipping_btn'])) {
    $shipping_id = mysqli_real_escape_string($con, $_POST['shipping_id']); 

    $delete_query = "DELETE FROM shipping WHERE id='$shipping_id'";
    $delete_query_run = mysqli_query($con, $delete_query);

    if ($delete_query_run) {
        echo 200; 
    } else {
        echo 500; 
    }
}

if (isset($_POST['Update_shipping-btn'])) {
    $shipping_id = mysqli_real_escape_string($con, $_POST['shippping_id']);
    $place = mysqli_real_escape_string($con, $_POST['name']);
    $price = mysqli_real_escape_string($con, $_POST['price']);

    $update_query = "UPDATE shipping SET place='$place', price='$price' WHERE id='$shipping_id'";

    $update_query_run = mysqli_query($con, $update_query);

    if ($update_query_run) {
        $_SESSION['message'] = "Détails de livraison mis à jour avec succès.";
        header('Location: shipping.php'); 
        exit();
    } else {
        $_SESSION['message'] = "Échec de la mise à jour des détails de livraison.";
        header('Location: edit-shipping.php?id=' . $shipping_id);
        exit();
    }
}

if (isset($_POST['add_category-btn'])) {
    $name = trim($_POST['name']);
    $image = $_FILES['image']['name'];
    $type = $_POST['type']; 

    $path = "../../uploads/images/category";

    // Check if name exists
    $check_name_query = "SELECT name FROM categories WHERE LOWER(name) = LOWER('$name')";
    $check_name_query_run = mysqli_query($con, $check_name_query);

    if (mysqli_num_rows($check_name_query_run) > 0) {
        $_SESSION['message'] = "La catégorie existe déjà.";
        header("Location: add-category.php?type=$type");
        exit();
    } 
    else {
        
        $type_query = "SELECT id FROM type WHERE name = '$type' LIMIT 1";
        $type_query_run = mysqli_query($con, $type_query);

        if (mysqli_num_rows($type_query_run) > 0) {
            $type_data = mysqli_fetch_assoc($type_query_run);
            $type_id = $type_data['id']; 

            // Insert category
            $cat_query = "INSERT INTO categories (name, image, type_id) VALUES ('$name', '$image', '$type_id')";
            $cat_query_run = mysqli_query($con, $cat_query);

            if ($cat_query_run) {
                // Move uploaded file
                move_uploaded_file($_FILES['image']['tmp_name'], $path . '/' . $image);
                
                // Redirect back to category page with filter
                $_SESSION['message'] = "Catégorie ajoutée avec succès.";
                header("Location: category.php?type=$type");
                exit();
            } 
            else {
                $_SESSION['message'] = "Quelque chose s'est mal passé.";
                header("Location: add-category.php?type=$type");
                exit();
            }
        }
        else {
            $_SESSION['message'] = "Type non trouvé.";
            header("Location: add-category.php?type=$type");
            exit();
        }
    }
}

else if(isset($_POST['Update_category-btn'])) {
    $category_id = $_POST['category_id'];
    $name = trim($_POST['name']);  
    $new_image = $_FILES['image']['name'];
    $old_image = $_POST['old_image'];

    if($new_image != ""){
        $update_filename = $new_image;
    }
    else {
       $update_filename = $old_image;
    }

    $path = "../../uploads/images/category"; 

    $update_query = "UPDATE categories SET name='$name', image='$update_filename'
    WHERE id='$category_id'";

    $update_query_run = mysqli_query($con, $update_query);

    if($update_query_run){
        if($_FILES['image']['name'] != ""){
            move_uploaded_file($_FILES["image"]["tmp_name"], $path.'/'.$new_image);
            if(file_exists($path.'/'.$old_image)){
                unlink($path.'/'.$old_image);
            }
        }
        redirect("category.php?id=$category_id", "Catégorie mise à jour avec succès.");
    }
    else {
        redirect("edit-category.php?id=$category_id", "Quelque chose s'est mal passé.");
    }
}

else if(isset($_POST['delete_category_btn'])) {
    $category_id = mysqli_real_escape_string($con, $_POST['category_id']);

    $category_query = "SELECT * FROM categories WHERE id='$category_id'";
    $category_query_run =  mysqli_query($con, $category_query);
    $category_data = mysqli_fetch_array($category_query_run);
    $image = $category_data['image'];

    $delete_query = "DELETE FROM categories WHERE id='$category_id'";
    $delete_query_run = mysqli_query($con, $delete_query);

    $path = "../../uploads/images/category";  

    if($delete_query_run){
        if(file_exists($path.'/'.$image)){
            unlink($path.'/'.$image);
        }
        echo 200;
    }
    else {
        echo 500;
    }
}

if (isset($_POST["add_product-btn"])) {
    $redirect_to = $_POST['redirect_to']; 
    $category_id = mysqli_real_escape_string($con, $_POST['category_id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $brand = mysqli_real_escape_string($con, $_POST['brand']);
    $qty = mysqli_real_escape_string($con, $_POST['qty']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $width = mysqli_real_escape_string($con, $_POST['width']);
    $height = mysqli_real_escape_string($con, $_POST['height']);
    $colors = isset($_POST['selected_colors']) ? json_decode($_POST['selected_colors'], true) : [];
    $ram_rom_pairs = isset($_POST['ram_rom_pairs']) ? json_decode($_POST['ram_rom_pairs'], true) : [];
    $battery = mysqli_real_escape_string($con, $_POST['battery']);

    if (empty($colors)) {
        $_SESSION['form_data'] = $_POST;
        redirect($redirect_to, "Veuillez sélectionner au moins une couleur.");
        exit(); 
    }

    if (empty($ram_rom_pairs)) {
        $_SESSION['form_data'] = $_POST;
        redirect($redirect_to, "Veuillez sélectionner au moins une ram/rom.");
        exit(); 
    }

    unset($_SESSION['form_data']);

    $type_query = "SELECT id FROM type WHERE name = 'phone' LIMIT 1";
    $type_query_run = mysqli_query($con, $type_query);
    $type_id = mysqli_fetch_assoc($type_query_run)['id'];

    $product_query = "INSERT INTO products (category_id, name, description, brand, qty, 
    width, height, battery, type_id) 
    VALUES ('$category_id', '$name', '$description', '$brand', '$qty',
    '$width', '$height', '$battery', '$type_id')";

    if (mysqli_query($con, $product_query)) {
        $product_id = mysqli_insert_id($con); 

        
        if (!empty($ram_rom_pairs)) {
            $ram_rom_stmt = $con->prepare("INSERT INTO ram_rom (product_id, ram, rom, price) VALUES (?, ?, ?, ?)");
            foreach ($ram_rom_pairs as $pair) {
                $ram_rom_stmt->bind_param("issd", $product_id, $pair['ram'], $pair['rom'], $pair['price']);
                if (!$ram_rom_stmt->execute()) {
                    echo "Error inserting into ram_rom table: " . $ram_rom_stmt->error;
                    exit();
                }
            }
            $ram_rom_stmt->close();
        }

        
        if (!empty($colors)) {
            $color_stmt = $con->prepare("INSERT INTO product_colors (product_id, color) VALUES (?, ?)");
            foreach ($colors as $color) {
                $color_stmt->bind_param("is", $product_id, $color);
                if (!$color_stmt->execute()) {
                    echo "Error inserting into product_colors table: " . $color_stmt->error;
                    exit();
                }
            }
            $color_stmt->close();
        }

        
        $path = "../../uploads/images/product";
        foreach ($_FILES['images']['name'] as $key => $image_name) {
            $image_tmp_name = $_FILES['images']['tmp_name'][$key];
            $image_name = mysqli_real_escape_string($con, $image_name);
            
            if (move_uploaded_file($image_tmp_name, $path . '/' . $image_name)) {
                $image_insert_query = "INSERT INTO product_images (product_id, image) VALUES ('$product_id', '$image_name')";
                mysqli_query($con, $image_insert_query);
            } else {
                redirect("add-product.php", "Échec du téléchargement de l'image.");
            }
        }
            
        }

        redirect("products.php", "Produit ajouté avec succès.");
    } else {
        redirect("add-product.php", "Quelque chose s'est mal passé : " . mysqli_error($con));
    }



if (isset($_POST['update_product-btn'])) {
   
    $product_id = $_POST['product_id'];
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $brand = mysqli_real_escape_string($con, $_POST['brand']);
    $qty = mysqli_real_escape_string($con, $_POST['qty']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $width = mysqli_real_escape_string($con, $_POST['width']);
    $height = mysqli_real_escape_string($con, $_POST['height']);
    $battery = mysqli_real_escape_string($con, $_POST['battery']);
    $category_id = mysqli_real_escape_string($con, $_POST['category_id']);

    
    $ram_rom_pairs = isset($_POST['ram_rom_pairs']) ? json_decode($_POST['ram_rom_pairs'], true) : [];
    $selected_colors = isset($_POST['selected_colors']) ? json_decode($_POST['selected_colors'], true) : [];


    $query = "UPDATE products SET 
              name='$name', brand='$brand', qty='$qty', description='$description', width='$width', 
              height='$height', battery='$battery', category_id='$category_id' 
              WHERE id='$product_id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        
        if (!empty($ram_rom_pairs)) {
            
            $delete_ram_rom_query = "DELETE FROM ram_rom WHERE product_id = '$product_id'";
            mysqli_query($con, $delete_ram_rom_query);

            
            $ram_rom_stmt = $con->prepare("INSERT INTO ram_rom (product_id, ram, rom, price) VALUES (?, ?, ?, ?)");
            foreach ($ram_rom_pairs as $pair) {
                $ram_rom_stmt->bind_param("issd", $product_id, $pair['ram'], $pair['rom'], $pair['price']);
                if (!$ram_rom_stmt->execute()) {
                    echo "Error inserting into ram_rom table: " . $ram_rom_stmt->error;
                    exit();
                }
            }
            $ram_rom_stmt->close();
        }

        
        if (!empty($selected_colors)) {
            
            $delete_colors_query = "DELETE FROM product_colors WHERE product_id = '$product_id'";
            mysqli_query($con, $delete_colors_query);

            
            $color_stmt = $con->prepare("INSERT INTO product_colors (product_id, color) VALUES (?, ?)");
            foreach ($selected_colors as $color) {
                $color_stmt->bind_param("is", $product_id, $color);
                if (!$color_stmt->execute()) {
                    echo "Error inserting into product_colors table: " . $color_stmt->error;
                    exit();
                }
            }
            $color_stmt->close();
        }

        
        $target_dir = "../../uploads/images/product/";

        if (isset($_FILES['images']['name'][0]) && !empty($_FILES['images']['name'][0])) {
            
            $delete_images_query = "SELECT image FROM product_images WHERE product_id = '$product_id'";
            $delete_images_result = mysqli_query($con, $delete_images_query);
            while ($row = mysqli_fetch_assoc($delete_images_result)) {
                $old_image_path = $target_dir . $row['image'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }

            
            $delete_query = "DELETE FROM product_images WHERE product_id = '$product_id'";
            mysqli_query($con, $delete_query);

            
            foreach ($_FILES['images']['name'] as $key => $val) {
                $image_name = $_FILES['images']['name'][$key];
                $image_tmp = $_FILES['images']['tmp_name'][$key];
                $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($image_ext, $allowed_ext)) {
                    $new_image_name = time() . '-' . $image_name;
                    $upload_path = $target_dir . $new_image_name;

                    if (move_uploaded_file($image_tmp, $upload_path)) {
                        $insert_image_query = "INSERT INTO product_images (product_id, image) VALUES ('$product_id', '$new_image_name')";
                        mysqli_query($con, $insert_image_query);
                    }
                }
            }
        }

        $_SESSION['message'] = "Produit mis à jour avec succès.";
        header("Location: products.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Échec de la mise à jour du produit : " . mysqli_error($con);
        header("Location: edit-product.php?id=$product_id");
        exit(0);
    }
}


else if(isset($_POST["delete_product_btn"])) {
    $product_id = mysqli_real_escape_string($con, $_POST['product_id']);

    
    $image_query = "SELECT image FROM product_images WHERE product_id = '$product_id'";
    $image_query_run = mysqli_query($con, $image_query);

    $path = "../../uploads/images/product";

    while ($row = mysqli_fetch_assoc($image_query_run)) {
        $imagePath = $path . '/' . $row['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    
    mysqli_query($con, "DELETE FROM product_images WHERE product_id = '$product_id'");

    
    mysqli_query($con, "DELETE FROM product_colors WHERE product_id = '$product_id'");

    
    mysqli_query($con, "DELETE FROM ram_rom WHERE product_id = '$product_id'");

    
    $delete_query = "DELETE FROM products WHERE id = '$product_id'";
    $delete_query_run = mysqli_query($con, $delete_query);

    if ($delete_query_run) {
        echo 200;  
    } else {
        echo 500;  
    }
}

else if(isset($_POST["add_promo-btn"])) {
    $product_id = mysqli_real_escape_string($con, $_POST['product_id']);
    $percent = mysqli_real_escape_string($con, $_POST['percent']);
    $date = mysqli_real_escape_string($con, $_POST['date']);

    $check_id_query = "SELECT product_id FROM product_promo WHERE product_id = '$product_id'";
    $check_id_query_run = mysqli_query($con, $check_id_query);

    if(mysqli_num_rows($check_id_query_run) > 0) {
        redirect("add-promo.php", "Le promo existe déjà.");
    } else {
        $promo_query = "INSERT INTO product_promo (product_id, date_fin, percent) VALUES ('$product_id', '$date', '$percent')";
        $promo_query_run = mysqli_query($con, $promo_query);

        if($promo_query_run){
           
            redirect("products.php", "Promo ajouté avec succès.");
        } else {
            redirect("add-promo.php", "Quelque chose s'est mal passé.");
        }
    }
   
}

else if (isset($_POST["add_accesoir-btn"])) {
    $redirect_to = $_POST['redirect_to'];  
    $category_id = mysqli_real_escape_string($con, $_POST['category_id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $brand = mysqli_real_escape_string($con, $_POST['brand']);
    $qty = mysqli_real_escape_string($con, $_POST['qty']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $width = mysqli_real_escape_string($con, $_POST['width']);
    $height = mysqli_real_escape_string($con, $_POST['height']);
    $power = isset($_POST['power']) ? mysqli_real_escape_string($con, $_POST['power']) : null;
    $price = mysqli_real_escape_string($con, $_POST['price']); 
    $selected_colors = isset($_POST['selected_colors']) ? json_decode($_POST['selected_colors'], true) : [];

    if (empty($selected_colors)) {
        redirect($redirect_to, "Veuillez sélectionner au moins une couleur.");
        exit(); 
    }



    
    $path = "../../uploads/images/product";
    $image_names = [];
    
    foreach ($_FILES['images']['name'] as $key => $image_name) {
        $image_name = mysqli_real_escape_string($con, $image_name);
        $image_tmp_name = $_FILES['images']['tmp_name'][$key];
        
        if (move_uploaded_file($image_tmp_name, $path . '/' . $image_name)) {
            $image_names[] = $image_name;
        } else {
            redirect("add-product.php", "Échec du téléchargement de l'image.");
        }
    }

    
    $type_query = "SELECT id FROM type WHERE name = 'accessoire' LIMIT 1";
    $type_query_run = mysqli_query($con, $type_query);
    $type_id = mysqli_fetch_assoc($type_query_run)['id'];

    $product_query = "INSERT INTO products (category_id, name, description, brand, qty, price,
    width, height, battery, type_id) 
    VALUES ('$category_id', '$name', '$description', '$brand', '$qty',
    '$price', '$width', '$height', " . ($power != null ? $power : "null") ." , '$type_id')";
    
    if (mysqli_query($con, $product_query)) {
        $product_id = mysqli_insert_id($con);


        
        if (!empty($selected_colors)) {
            $color_stmt = $con->prepare("INSERT INTO product_colors (product_id, color) VALUES (?, ?)");
            foreach ($selected_colors as $color) {
                $color_stmt->bind_param("is", $product_id, $color);
                if (!$color_stmt->execute()) {
                    echo "Error inserting into product_colors table: " . $color_stmt->error;
                    exit();
                }
            }
            $color_stmt->close();
        }

        
        foreach ($image_names as $image_name) {
            $image_query = "INSERT INTO product_images (product_id, image) VALUES ('$product_id', '$image_name')";
            mysqli_query($con, $image_query);
        }

    

      

        redirect("products.php", "Produit ajouté avec succès.");
    } else {
        redirect("add-product.php", "Quelque chose s'est mal passé : " . mysqli_error($con));
    }
}

if (isset($_POST['update_accessory-btn'])) {

    $accessory_id = $_POST['accessory_id'];
    $redirect_to = $_POST['redirect_to'];  
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $brand = mysqli_real_escape_string($con, $_POST['brand']);
    $qty = mysqli_real_escape_string($con, $_POST['qty']);
    $price = mysqli_real_escape_string($con, $_POST['price']); 
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $width = mysqli_real_escape_string($con, $_POST['width']);
    $power = isset($_POST['power']) ? mysqli_real_escape_string($con, $_POST['power']) : null;
    $height = mysqli_real_escape_string($con, $_POST['height']);
    $category_id = mysqli_real_escape_string($con, $_POST['category_id']);
    $selected_colors = isset($_POST['selected_colors']) ? json_decode($_POST['selected_colors'], true) : [];

    if (empty($selected_colors)) {
        redirect($redirect_to, "Veuillez sélectionner au moins une couleur.");
        exit(); 
    }
   
    $query = "UPDATE products SET 
              name='$name', brand='$brand', qty='$qty', price='$price', 
              description='$description', battery=" . ($power != null ? $power : "null") .", width='$width', height='$height', 
              category_id='$category_id'
              WHERE id='$accessory_id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {

     
        $delete_colors_query = "DELETE FROM product_colors WHERE product_id = '$accessory_id'";
        mysqli_query($con, $delete_colors_query);

     
        if (!empty($selected_colors)) {
            $color_stmt = $con->prepare("INSERT INTO product_colors (product_id, color) VALUES (?, ?)");
            foreach ($selected_colors as $color) {
                $color_stmt->bind_param("is", $accessory_id, $color);
                if (!$color_stmt->execute()) {
                    echo "Error inserting into product_colors table: " . $color_stmt->error;
                    exit();
                }
            }
            $color_stmt->close();
        }

        $target_dir = "../../uploads/images/product/";

        
        if (isset($_FILES['images']['name'][0]) && !empty($_FILES['images']['name'][0])) {
            
          
            $delete_images_query = "SELECT image FROM product_images WHERE product_id = '$accessory_id'";
            $delete_images_result = mysqli_query($con, $delete_images_query);
            while ($row = mysqli_fetch_assoc($delete_images_result)) {
                $old_image_path = $target_dir . $row['image'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }

            $delete_query = "DELETE FROM product_images WHERE product_id = '$accessory_id'";
            mysqli_query($con, $delete_query);

       
            foreach ($_FILES['images']['name'] as $key => $val) {
                $image_name = $_FILES['images']['name'][$key];
                $image_tmp = $_FILES['images']['tmp_name'][$key];
                $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($image_ext, $allowed_ext)) {
                    $new_image_name = time() . '-' . $image_name;
                    $upload_path = $target_dir . $new_image_name;

                    if (move_uploaded_file($image_tmp, $upload_path)) {
                        $insert_image_query = "INSERT INTO product_images (product_id, image) VALUES ('$accessory_id', '$new_image_name')";
                        mysqli_query($con, $insert_image_query);
                    }
                }
            }
        }

        $_SESSION['message'] = "Accessoire mis à jour avec succès.";
        header("Location: products.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Échec de la mise à jour de l'accessoire : " . mysqli_error($con);
        header("Location: edit-accessory.php?id=$accessory_id");
        exit(0);
    }
}




else if (isset($_POST["delete_accesoir_btn"])) {
    $accesoir_id = mysqli_real_escape_string($con, $_POST['accesoir_id']);

    
    $accesoir_query = "SELECT * FROM accesoirs WHERE id='$accesoir_id'";
    $accesoir_query_run = mysqli_query($con, $accesoir_query);

    if ($accesoir_data = mysqli_fetch_array($accesoir_query_run)) {
        
        $delete_images_query = "SELECT image FROM product_images WHERE product_id='$accesoir_id'";
        $delete_images_result = mysqli_query($con, $delete_images_query);

        $path = "../../uploads/images/product";

        while ($row = mysqli_fetch_assoc($delete_images_result)) {
            $image = $row['image'];
            $imagePath = $path . '/' . $image;
            if (file_exists($imagePath)) {
                unlink($imagePath); 
            }
        }

        
        $delete_images_query = "DELETE FROM product_images WHERE product_id='$accesoir_id'";
        mysqli_query($con, $delete_images_query);

        
        $delete_ram_rom_query = "DELETE FROM ram_rom WHERE product_id='$accesoir_id'";
        mysqli_query($con, $delete_ram_rom_query);

        
        $delete_colors_query = "DELETE FROM product_colors WHERE product_id='$accesoir_id'";
        mysqli_query($con, $delete_colors_query);

        
        $delete_query = "DELETE FROM accesoirs WHERE id='$accesoir_id'";
        $delete_query_run = mysqli_query($con, $delete_query);

        if ($delete_query_run) {
            echo 200; 
        } else {
            echo 500; 
        }
    } else {
        echo 404; 
    }
}

if (isset($_POST['update_service_btn'])) {
    mysqli_set_charset($con, "utf8mb4");

    $service_id = mysqli_real_escape_string($con, $_POST['reparation_id']);
    $title =  $_POST['title'];
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $icon = mysqli_real_escape_string($con, $_POST['icon']);

    $check_title_query = "SELECT * FROM reparation WHERE LOWER(title) = LOWER(?) AND id != ?";
    $stmt = mysqli_prepare($con, $check_title_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $title, $service_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['message'] = "Le nom du service existe déjà. Veuillez choisir un autre nom.";
            header('Location: edit-service.php?id=' . $service_id);
            exit();
        } else {
            $update_query = "UPDATE reparation SET title = ?, description = ?, icon = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($update_stmt, "sssi", $title, $description, $icon, $service_id);
            $update_query_run = mysqli_stmt_execute($update_stmt);

            if ($update_query_run) {
                redirect("services.php", "Service mis à jour avec succès.");
                exit();
            } else {
                $_SESSION['message'] = "Échec de la mise à jour du service.";
                redirect("edit-service.php?id=' . $service_id", "an error ");
                exit();
            }
        }
    } else {
        $_SESSION['message'] = "Échec de la préparation de la requête.";
        header('Location: edit-service.php?id=' . $service_id);
        exit();
    }
}

else if (isset($_POST["add_service-btn"])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $icon = $_POST['icon'];

    $check_title_query = "SELECT title FROM reparation WHERE LOWER(title) = LOWER(?)";
    $stmt = mysqli_prepare($con, $check_title_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $title);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            redirect("add-service.php", "Le service existe déjà.");
        } else {
            $service_insert_query = "INSERT INTO reparation (title, description, icon) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($con, $service_insert_query);
            mysqli_stmt_bind_param($insert_stmt, "sss", $title, $description, $icon);
            $service_insert_query_run = mysqli_stmt_execute($insert_stmt);

            if ($service_insert_query_run) {
                redirect("services.php", "Service ajouté avec succès.");
            } else {
                redirect("add-service.php", "Quelque chose s'est mal passé.");
            }
        }
    } else {
        redirect("add-service.php", "Échec de la requête.");
    }
}

else if (isset($_POST['delete_service_btn'])) {
    $service_id = mysqli_real_escape_string($con, $_POST['service_id']);

    $delete_query = "DELETE FROM reparation WHERE id='$service_id'";
    $delete_query_run = mysqli_query($con, $delete_query);

    if ($delete_query_run) {
        echo 200; 
    } else {
        echo 500; 
    }
} 



if (isset($_POST["newsletter"])) {
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $redirect_to = $_POST['redirect_to'];

    
    $otp_code = rand(100000, 999999);
    $_SESSION['otp'] = $otp_code;
    $_SESSION['email'] = $email;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['redirect_to'] = $redirect_to;


    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'aminemokhtari028@gmail.com'; 
        $mail->Password = 'nssisuinqrccsgod'; 
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('aminemokhtari028@gmail.com', 'Coin Mobile');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Votre Code OTP';
        $mail->Body = "<h3>Votre code de vérification est: <strong>$otp_code</strong></h3>";

        $mail->send();

    
        header("Location: ../../user/verify-otp.php");
        exit();
    } catch (Exception $e) {
        redirect($redirect_to, "Erreur lors de l'envoi de l'OTP : " . $mail->ErrorInfo);
    }
}

else if(isset($_POST["newsletter_unsigned"])){
    $user_id = $_POST['user_id'];
    $redirect_to = $_POST['redirect_to'];  

    $delete_news_query = "DELETE FROM newsletter WHERE user_id = '$user_id'";
    $delete_news_query_run = mysqli_query($con, $delete_news_query);

    if($delete_news_query_run){
        redirect($redirect_to, "Vous vous êtes désabonné avec succès.");
    } else {
        redirect($redirect_to, "Un problème est survenu.");
    } 
}


if (isset($_POST['delete_client_btn'])) {
    $clientId = $_POST['id'];

    $statusQuery = "SELECT status FROM users WHERE id = '$clientId'";
    $statusResult = mysqli_query($con, $statusQuery);
    $client = mysqli_fetch_assoc($statusResult);
    $currentStatus = $client['status'];

    $newStatus = ($currentStatus == 1) ? 0 : 1;

    $updateQuery = "UPDATE users SET status = $newStatus WHERE id = '$clientId'";
    $updateResult = mysqli_query($con, $updateQuery);

    if ($updateResult) {
        if ($newStatus == 1) {
            $deleteQuery = "DELETE FROM reviews WHERE user_id = '$clientId'";
            mysqli_query($con, $deleteQuery);
        }
        echo 200;
    } else {
        echo 500;
    }
}

else if (isset($_POST['delete_commentaire_btn'])) {
    $commentaire_id = mysqli_real_escape_string($con, $_POST['commentaire_id']);

        $delete_query = "DELETE FROM reviews WHERE id='$commentaire_id'";
        $delete_query_run = mysqli_query($con, $delete_query);

        if ($delete_query_run) {
            echo 200;  
        } else {
            echo 500;  
        }
    }


    else if (isset($_POST["add_promotion_btn"])) {
        $name = $_POST['title'];  
        $percent = $_POST['percentage'];  
        $date_fin = $_POST['date_fin'];  
        $selected_products = isset($_POST['selected_products']) ? json_decode($_POST['selected_products'], true) : [];
    
        // Check if the promotion name already exists
        $check_promo_query = "SELECT name FROM promotions WHERE LOWER(name) = LOWER(?)";
        $stmt = mysqli_prepare($con, $check_promo_query);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
    
            if (mysqli_num_rows($result) > 0) {
                redirect("add-promotion.php", "La promotion existe déjà.");
            } else {
                if (!empty($selected_products)) {
                    $placeholders = implode(',', array_fill(0, count($selected_products), '?'));
                    $check_products_query = "
                        SELECT pp.product_id, p.date_fin 
                        FROM product_promo pp 
                        JOIN promotions p ON pp.promo_id = p.id 
                        WHERE pp.product_id IN ($placeholders) AND p.date_fin >= CURDATE()";
                    
                    $stmt = mysqli_prepare($con, $check_products_query);
                    $types = str_repeat('i', count($selected_products));
                    mysqli_stmt_bind_param($stmt, $types, ...$selected_products);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
    
                    if (mysqli_num_rows($result) > 0) {
                        $existing_products = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $product_id = $row['product_id'];
                    
                         
                            $name_query = "SELECT name FROM products WHERE id = ?";
                            $name_stmt = mysqli_prepare($con, $name_query);
                            mysqli_stmt_bind_param($name_stmt, "i", $product_id);
                            mysqli_stmt_execute($name_stmt);
                            $name_result = mysqli_stmt_get_result($name_stmt);
                            $name_row = mysqli_fetch_assoc($name_result);
                    
                            if ($name_row) {
                                $existing_products[] = $name_row['name'];
                            } else {
                                $existing_products[] = "Produit ID: $product_id"; // fallback
                            }
                        }
                    
                        $_SESSION['promo_error'] = "Les produits suivants sont déjà en promotion active : <strong>" . implode(', ', $existing_products) . "</strong>";
                        $_SESSION['preserve_inputs'] = [
                            'title' => $name,
                            'percentage' => $percent,
                            'date_fin' => $date_fin,
                            'selected_products' => json_encode($selected_products)
                        ];
                        header("Location: add-promotion.php");
                        exit();
                    }
                    
                }
    
                // Insert the promotion
                $promo_insert_query = "INSERT INTO promotions (name, percent, date_fin) VALUES (?, ?, ?)";
                $insert_stmt = mysqli_prepare($con, $promo_insert_query);
                mysqli_stmt_bind_param($insert_stmt, "sis", $name, $percent, $date_fin);
                $promo_insert_query_run = mysqli_stmt_execute($insert_stmt);
    
                if ($promo_insert_query_run) {
                    $promo_id = mysqli_insert_id($con); 
    
                    $product_details = "";
                    if (!empty($selected_products)) {
                        foreach ($selected_products as $product_id) {
                            // Insert into product_promo
                            $product_promo_query = "INSERT INTO product_promo (promo_id, product_id) VALUES (?, ?)";
                            $product_stmt = mysqli_prepare($con, $product_promo_query);
                            mysqli_stmt_bind_param($product_stmt, "ii", $promo_id, $product_id);
                            mysqli_stmt_execute($product_stmt);
    
                            // Fetch product info
                            $product_query = "SELECT name, brand, price FROM products WHERE id = ?";
                            $product_stmt = mysqli_prepare($con, $product_query);
                            mysqli_stmt_bind_param($product_stmt, "i", $product_id);
                            mysqli_stmt_execute($product_stmt);
                            $product_result = mysqli_stmt_get_result($product_stmt);
                            $product = mysqli_fetch_assoc($product_result);
    
                            // Fetch price from ram_rom if available
                            $ram_rom_query = "SELECT price FROM ram_rom WHERE product_id = ? LIMIT 1";
                            $ram_rom_stmt = mysqli_prepare($con, $ram_rom_query);
                            mysqli_stmt_bind_param($ram_rom_stmt, "i", $product_id);
                            mysqli_stmt_execute($ram_rom_stmt);
                            $ram_rom_result = mysqli_stmt_get_result($ram_rom_stmt);
                            $ram_rom = mysqli_fetch_assoc($ram_rom_result);
    
                            $original_price = $ram_rom ? $ram_rom['price'] : $product['price'];
                            $discounted_price = $original_price - ($original_price * ($percent / 100));
    
                            $product_details .= "<li>
                                <strong>{$product['name']}</strong> - {$product['brand']} <br>
                                <del>{$original_price} DZD</del> → <strong>{$discounted_price} DZD</strong> (-{$percent}%)<br>
                                <a href='http://localhost/ecommerce__website/user/details.php?id={$product_id}'>Voir le produit</a>
                            </li>";
                        }
                    }
    
                    // Send promo to newsletter subscribers
                    $newsletter_query = "SELECT email FROM newsletter";
                    $newsletter_result = mysqli_query($con, $newsletter_query);
    
                    if (mysqli_num_rows($newsletter_result) > 0) {
                        while ($subscriber = mysqli_fetch_assoc($newsletter_result)) {
                            $mail = new PHPMailer(true);
                            try {
                                $mail->isSMTP();
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->Username = 'aminemokhtari028@gmail.com';
                                $mail->Password = 'nssisuinqrccsgod'; // Consider using an app password
                                $mail->SMTPSecure = 'ssl';
                                $mail->Port = 465;
    
                                $mail->setFrom('aminemokhtari028@gmail.com', 'Coin Mobile');
                                $mail->addAddress($subscriber['email']);
    
                                $mail->isHTML(true);
                                $mail->Subject = "Nouvelle promotion : $name - $percent% de reduction !";
                                $mail->Body = "
                                    <h1>Nouvelle promotion disponible sur Coin Mobile !</h1>
                                    <p><strong>Nom de la promotion :</strong> $name</p>
                                    <p><strong>Réduction :</strong> $percent% de remise</p>
                                    <p><strong>Date de fin :</strong> $date_fin</p>
                                    <p><strong>Produits concernés :</strong></p>
                                    <ul>$product_details</ul>
                                    <p>Profitez-en dès maintenant !</p>
                                    <p><a href='http://localhost/ecommerce__website/user/index.php'>Visitez notre boutique</a></p>
                                ";
    
                                $mail->send();
                            } catch (Exception $e) {
                                error_log("Email error: " . $mail->ErrorInfo);
                            }
                        }
                    }
    
                    redirect("produit_promotion.php", "Promotion ajoutée avec succès.");
                } else {
                    redirect("add-promotion.php", "Erreur lors de l'ajout de la promotion.");
                }
            }
        } else {
            redirect("add-promotion.php", "Échec de la requête.");
        }
    }
    
    
    

if (isset($_POST["add_promo_product_btn"])) {
    $product_id = $_POST['product_id'];
    $promo_id = $_POST['promo_id'];

        $insert_query = "INSERT INTO product_promo (product_id, promo_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($con, $insert_query);
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $promo_id);
        $query_run = mysqli_stmt_execute($stmt);

        if ($query_run) {
         
            $promo_query = "SELECT name, percent FROM promotions WHERE id = ?";
            $stmt = mysqli_prepare($con, $promo_query);
            mysqli_stmt_bind_param($stmt, "i", $promo_id);
            mysqli_stmt_execute($stmt);
            $promo_result = mysqli_stmt_get_result($stmt);
            $promo = mysqli_fetch_assoc($promo_result);

          
            $newsletter_query = "SELECT email FROM newsletter";
            $newsletter_result = mysqli_query($con, $newsletter_query);

            if (mysqli_num_rows($newsletter_result) > 0) {
                while ($subscriber = mysqli_fetch_assoc($newsletter_result)) {
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'aminemokhtari028@gmail.com'; 
                        $mail->Password = 'nssisuinqrccsgod'; 
                        $mail->SMTPSecure = 'ssl';
                        $mail->Port = 465;

                        $mail->setFrom('aminemokhtari028@gmail.com', 'Coin Mobile');
                        $mail->addAddress($subscriber['email']);

                        $mail->isHTML(true);
                        $mail->Subject = "Nouveau produit ajouté à la promotion : {$promo['name']}";
                        $mail->Body = "
                            <h1>Un nouveau produit est en promotion !</h1>
                            <p><strong>Nom de la promotion :</strong> {$promo['name']}</p>
                            <p><strong>Réduction :</strong> {$promo['percent']}% de remise</p>
                            <p>Profitez-en dès maintenant !</p>
                            <p><a href='http://localhost/ecommerce__website/user/index.php'>Visitez notre boutique</a></p>
                        ";

                        $mail->send();
                    } catch (Exception $e) {
                        error_log("Email error: " . $mail->ErrorInfo);
                    }
                }
            }

            redirect("products.php", "Produit ajouté à la promotion avec succès.");
        } else {
            redirect("products.php", "Erreur lors de l'ajout du produit à la promotion.");
        }
    }



    
    if (isset($_POST['update_promotion_btn'])) {
        mysqli_set_charset($con, "utf8mb4");
    
        $promotion_id = mysqli_real_escape_string($con, $_POST['promotion_id']);
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $percent = mysqli_real_escape_string($con, $_POST['percent']);
        $date_fin = mysqli_real_escape_string($con, $_POST['date_fin']);
    
  
        $check_query = "SELECT * FROM promotions WHERE LOWER(name) = LOWER(?) AND id != ?";
        $stmt = mysqli_prepare($con, $check_query);
        mysqli_stmt_bind_param($stmt, "si", $name, $promotion_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    
        if (mysqli_num_rows($result) > 0) {
            $_SESSION['message'] = "Le nom de la promotion existe déjà.";
            header("Location: edit-promotion.php?id=" . $promotion_id);
            exit();
        } else {
            $update_query = "UPDATE promotions SET name = ?, percent = ?, date_fin = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($update_stmt, "sisi", $name, $percent, $date_fin, $promotion_id);
            $update_run = mysqli_stmt_execute($update_stmt);
    
            if ($update_run) {
                redirect("produit_promotion.php", "Promotion mise à jour avec succès.");
                exit();
            } else {
                redirect("edit-promotion.php?id=" . $promotion_id, "Échec de la mise à jour.");
                exit();
            }
        }
    }

    if (isset($_POST['remove_promo_btn'])) {
        $product_id = $_POST['product_id'];
    
        if (!empty($product_id)) {
            $query = "DELETE FROM product_promo WHERE product_id = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            $query_run = mysqli_stmt_execute($stmt);
    
            if ($query_run) {
                redirect("products.php","Promotion supprimée avec succès");
                exit();
            } else {
                redirect("products.php","Erreur lors de la suppression de la promotion");
                exit();
            }
        }
    }
    else if (isset($_POST['delete_promotion_btn'])) {
        $promotion_id = mysqli_real_escape_string($con, $_POST['promotion_id']);

        $delete_product_promo_query = "DELETE FROM product_promo WHERE promo_id='$promotion_id'";
        $delete_product_promo_run = mysqli_query($con, $delete_product_promo_query);
        
        if ($delete_product_promo_run) {
          
            $delete_promotion_query = "DELETE FROM promotions WHERE id='$promotion_id'";
            $delete_promotion_run = mysqli_query($con, $delete_promotion_query);
        
            if ($delete_promotion_run) {
                echo 200;  
            } else {
                echo 500;  
            }
        } else {
            echo 500;
        }
    }
    
?>