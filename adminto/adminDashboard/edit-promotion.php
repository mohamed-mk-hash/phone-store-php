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
              $reparation = getById("promotions", $id); 

              if(mysqli_num_rows($reparation) > 0){
                $data = mysqli_fetch_array($reparation);
        ?>
        <div class="card">
            <div class="card-header">
                <h4>Modifier le service</h4>
                <a href="produit_promotion.php" class="btn btn-primary float-end">Retour</a>
            </div>
            <div class="card-body">
                <form action="code.php" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" name="promotion_id" value="<?= $data['id'] ?>">
                            <label for="">Nom</label>
                            <input value="<?= $data['name'] ?>" placeholder="Entrez le nom du promotionn" type="text" name="name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="">le percentage</label>
                            <input value="<?= $data['percent'] ?>" placeholder="Entrez la percentage de la promotion" type="text" name="percent" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label for="">date de fin de promotion</label>
                            <input value="<?= $data['date_fin'] ?>" placeholder="Entrez la date de fin de promotion" type="date" name="date_fin" class="form-control">
                        </div>
                        <div class="col-md-12 py-4">
                            <button class="btn btn-primary" name="update_promotion_btn">Mettre à jour</button>
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