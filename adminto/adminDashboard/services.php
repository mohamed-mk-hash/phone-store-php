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
                <h4>Services</h4>
                <a href="add-service.php" class="btn btn-primary float-end">Ajouter un service</a>
            </div>
            <div class="card-body" id="reparation_table">
                
                <table class="table table-bordered table-striped" id="service_table" >
                    <thead>
                        <tr>
                            
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Icône</th>
                            <th>Modifier</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                      
                        $query = "SELECT * FROM reparation";

                        if (!empty($search)) {
                            $query .= " WHERE title LIKE '%$search%' OR description LIKE '%$search%'";
                        }

                    
                        $reparation = mysqli_query($con, $query);

                        if (mysqli_num_rows($reparation) > 0) {
                            foreach ($reparation as $item) {
                                ?>
                                <tr>
                                
                                    <td><?= $item['title'] ?></td>
                                    <td><?= wordwrap($item['description'], 40, "<br>") ?></td>
                                    <td><i class="<?= $item['icon'] ?>"></i></td>
                                    <td>
                                        <a href="edit-reparation.php?id=<?= $item['id']; ?>" class="btn btn-primary">Modifier</a>
                                    </td>
                                    <td>
                                    <button class="btn btn-danger delete_reparation_btn" type="button" value="<?= $item['id']; ?>">Supprimer</button>

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