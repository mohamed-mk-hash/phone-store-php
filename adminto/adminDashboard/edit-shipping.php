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
              $shippping = getById("shipping",$id);

              if(mysqli_num_rows($shippping)> 0){
                $data = mysqli_fetch_array($shippping);
         
        ?>
        <div class="card">
            <div class="card-header">
                <h4>Modifier la livraison</h4>
                <a href="shipping.php" class="btn btn-primary float-end">Retour</a>
            </div>
            <div class="card-body">
                <form action="code.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="shippping_id" value="<?= $data['id'] ?>" >
                <label for="">Lieu</label>
                <input value="<?= $data['place'] ?>" placeholder="Entrez le lieu de livraison" type="text" name="name" class="form-control">
                    </div>
                    <div class="col-md-12">
                <label for="">Prix</label>
                <textarea  rows="3" type="text" name="price" class="form-control"><?= $data['price'] ?></textarea>
                    </div>
                
                    <div class="col-md-12 py-4">
                        <button class="btn btn-primary" name="Update_shipping-btn">Mettre à jour</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <?php 
              }
              else{
                echo "Catégorie non trouvée";
              }
        }
         else {
            echo'ID manquant dans l\'URL';
         }
        ?>
       </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>