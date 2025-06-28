<?php 
include('../../middleware/adminMiddlware.php');

if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Une connexion est requise pour accéder au tableau de bord administrateur";
    header('Location: ../main/login.php');
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
                <h4>commentaire</h4>
            </div>
            <div class="card-body" id="review_table">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nom de utilisateur</th>
                            <th>commentaire</th>
                            <th>nom de produit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $query = "SELECT reviews.*, users.username, products.name AS product_name 
                        FROM reviews 
                        JOIN users ON reviews.user_id = users.id
                        JOIN products ON reviews.product_id = products.id";
              
                        if (!empty($search)) {
                            $query .= " WHERE username LIKE '%$search%' OR email LIKE '%$search%'";
                        }
                        $client = mysqli_query($con, $query);
                        if (mysqli_num_rows($client) > 0) {
                            foreach ($client as $item) {
                              
                                ?>
                                <tr>
                                    <td><?= $item['username'] ?></td>
                                    <td><?= $item['comments'] ?></td>
                                    <td><?= $item['product_name'] ?></td>
                                    <td>
                                    <button class="btn btn-danger delete_commentaire_btn" type="button" value="<?= $item['id']; ?>">Supprimer</button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                    <tr><td colspan='4' class='text-center'>Aucun enregistrement trouvé</td></tr>
                            <?php
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