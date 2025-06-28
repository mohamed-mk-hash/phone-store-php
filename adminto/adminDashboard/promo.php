<?php 
include('../../middleware/adminMiddlware.php');

if(!isset($_SESSION['auth'])) {
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
                <h4>Éléments publicité</h4>
                <a href="add-publicité.php" class="btn btn-primary float-end">Ajouter une publicité</a>
            </div>
            <div class="card-body" id="promo_table">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $promo = getAll("promo");

                        if (mysqli_num_rows($promo) > 0) {
                            foreach ($promo as $item) {
                                ?>
                                <tr>
                                    <td>
                                        <img src="../../uploads/images/hero/<?= $item['image'] ?>" width="500px" height="200" alt="">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger delete_promo_btn" type="button" value="<?= $item['id']; ?>">Supprimer</button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='2' class='text-center'>Aucun enregistrement trouvé</td></tr>";
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