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
                <h4>Éléments Hero</h4>
                <a href="add-heroElement.php" class="btn btn-primary float-end">Ajouter un élément Hero</a>
            </div>
            <div class="card-body" id="hero_table">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                        
                            <th>Titre</th>
                            <th>Sous-titre</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Modifier</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                       
                        $query = "SELECT * FROM hero";

                      
                        if (!empty($search)) {
                            $query .= " WHERE title LIKE '%$search%' OR subtitle LIKE '%$search%'";
                        }

                      
                        $hero = mysqli_query($con, $query);

                        if (mysqli_num_rows($hero) > 0) {
                            foreach ($hero as $item) {
                                ?>
                                <tr>
                              
                                    <td><?= wordwrap($item['title'], 21, "<br>") ?></td>
                                    <td><?= wordwrap($item['subtitle'], 21, "<br>") ?></td>
                                    <td><?= wordwrap($item['description'], 28, "<br>") ?></td>
                                    <td>
                                        <img src="../../uploads/images/hero/<?= $item['image'] ?>" width="170px" height="100" alt="">
                                    </td>
                                    <td>
                                        <a href="edit-hero.php?id=<?= $item['id']; ?>" class="btn btn-primary">Modifier</a>
                                    </td>
                                    <td>
                                    <button class="btn btn-danger delete_hero_btn" type="button" value="<?= $item['id']; ?>">Supprimer</button>

                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>Aucun enregistrement trouvé</td></tr>";
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