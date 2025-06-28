<?php
session_start();
include('../middleware/adminMiddlware.php');
session_start();

if (isset($_POST['submit_review_btn'])) {
        $product_id = intval($_POST['product_id']);
    $user_id = mysqli_real_escape_string($con, trim($_POST['user_id']));
    $comments = mysqli_real_escape_string($con, trim($_POST['comments']));

        
                $insert_review_query = "INSERT INTO reviews (product_id, user_id, comments) 
                                VALUES ('$product_id', '$user_id', '$comments')";
        $insert_review_query_run = mysqli_query($con, $insert_review_query);

        if ($insert_review_query_run) {
            redirect("details.php?id=$product_id", "Avis ajouté avec succès.");
        } else {
            redirect("details.php?id=$product_id", "Une erreur s'est produite. Veuillez réessayer.");
        }
    }

?>
