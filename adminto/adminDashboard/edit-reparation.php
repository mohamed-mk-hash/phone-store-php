<?php 
include('../../middleware/adminMiddlware.php');

if(!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Une connexion est requise pour accéder au tableau de bord administrateur";
    header('Location: ../main/login.php');
    exit();
}

include('includes/header.php');
?>

<div class="container">
    <div class="row">
       <div class="col-md-12">
        <?php
           if(isset($_GET['id'])){
              $id = $_GET['id'];
              $reparation = getById("reparation", $id); 

              if(mysqli_num_rows($reparation) > 0){
                $data = mysqli_fetch_array($reparation);
        ?>
        <div class="card">
            <div class="card-header">
                <h4>Modifier le service</h4>
                <a href="services.php" class="btn btn-primary float-end">Retour</a>
            </div>
            <div class="card-body">
                <form action="code.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" name="reparation_id" value="<?= $data['id'] ?>">
                            <label for="">Nom</label>
                            <input value="<?= $data['title'] ?>" placeholder="Entrez le nom du service" type="text" name="title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="">Classe de l'icône</label>
                            <input value="<?= $data['icon'] ?>" placeholder="Entrez la classe de l'icône (ex : fi-rs-shield)" type="text" name="icon" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label for="">Description</label>
                            <textarea placeholder="Entrez la description du service" name="description" class="form-control" rows="3"><?= $data['description'] ?></textarea>
                        </div>
                        <div class="col-md-12 py-4">
                            <button class="btn btn-primary" name="update_service_btn">Mettre à jour</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php 
              } else {
                echo "Service non trouvé";
              }
           } else {
              echo 'ID manquant dans l\'URL';
           }
        ?>
       </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>