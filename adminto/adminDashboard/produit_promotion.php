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
                <h4>Promotions</h4>
                <a href="add-promotion.php" class="btn btn-primary float-end">Ajouter un promo</a>
            </div>
            <div class="card-body" id="promotion_table">
                
                <table class="table table-bordered table-striped" id="promotion_table" >
                    <thead>
                        <tr>
                            
                            <th>Nom</th>
                            <th>percentage</th>
                            <th>date de fin de promotion</th>
                            <th>Modifier</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                      
                        $query = "SELECT * FROM promotions";

                        if (!empty($search)) {
                            $query .= " WHERE name LIKE '%$search%' ";
                        }

                    
                        $reparation = mysqli_query($con, $query);

                        if (mysqli_num_rows($reparation) > 0) {
                            foreach ($reparation as $item) {
                                ?>
                                <tr>
                                
                                    <td><?= $item['name'] ?></td>
                                    <td><?= wordwrap($item['percent'], 40, "<br>") ?></td>
                                    <td><?= htmlspecialchars($item['date_fin']) ?></td>
                                    <td>
                                        <a href="edit-promotion.php?id=<?= $item['id']; ?>" class="btn btn-primary">Modifier</a>
                                    </td>
                                    <td>
                                    <button class="btn btn-danger delete_promotion_btn" type="button" value="<?= $item['id']; ?>">Supprimer</button>

                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Aucun enregistrement trouvé</td></tr>";
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