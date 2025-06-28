<?php

include('../../middleware/adminMiddlware.php');

if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Une connexion est requise pour accéder au tableau de bord administrateur";
    header('Location: ../main/login.php');
    exit();
}

if (isset($_POST['delete_shipping_btn'])) {
    $shipping_id = mysqli_real_escape_string($con, $_POST['id']); 

    $delete_query = "DELETE FROM shipping WHERE id='$shipping_id'";
    $delete_query_run = mysqli_query($con, $delete_query);

    if ($delete_query_run) {
        $_SESSION['message'] = "Lieu de livraison supprimé avec succès";
    } else {
        $_SESSION['message'] = "Échec de la suppression du lieu de livraison";
    }
    header('Location: shipping.php'); 
    exit();
}

include('includes/header.php');


$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-1">
                    <h4>Livraison</h4>
                    <a href="add-shipping.php" class="btn btn-primary float-end">Ajouter un lieu de livraison</a>
                </div>
                <div class="card-body" id="shipping_table">
                   
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              
                                <th>Lieu</th>
                                <th>Prix</th>
                                <th>Modifier</th>
                                <th>Supprimer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                           
                            $query = "SELECT * FROM shipping";

                        
                            if (!empty($search)) {
                                $query .= " WHERE place LIKE '%$search%' OR price LIKE '%$search%'";
                            }

               
                            $shipping = mysqli_query($con, $query);

                            if (mysqli_num_rows($shipping) > 0) {
                                foreach ($shipping as $item) {   
                                    ?>
                                    <tr>
                              
                                        <td><?= $item['place'] ?></td>
                                        <td><?= $item['price'] ?></td>
                                        <td>
                                            <a href="edit-shipping.php?id=<?= $item['id']; ?>" class="btn btn-primary">Modifier</a>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger delete_shipping_btn" type="button" value="<?= $item['id']; ?>">Supprimer</button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='5'>Aucun lieu de livraison trouvé</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>