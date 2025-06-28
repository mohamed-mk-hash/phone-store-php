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
              $hero = getById("hero",$id);

              if(mysqli_num_rows($hero)> 0){
                $data = mysqli_fetch_array($hero);
         
        ?>
        <div class="card">
            <div class="card-header">
                <h4>Modifier le héros</h4>
                <a href="hero.php" class="btn btn-primary float-end">Retour</a>
            </div>
            <div class="card-body">
                <form action="code.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="hero_id" value="<?= $data['id'] ?>" >
                <label for="">Titre</label>
                <input value="<?= $data['title'] ?>" placeholder="Entrez le titre du héros" type="text" name="title" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <input type="hidden" name="hero_id" value="<?= $data['id'] ?>" >
                <label for="">Sous-titre</label>
                <input value="<?= $data['subtitle'] ?>" placeholder="Entrez le sous-titre du héros" type="text" name="subtitle" class="form-control">
                    </div>
                    <div class="col-md-12">
                <label for="">Description</label>
                <textarea  rows="3" type="text" name="description" class="form-control"><?= $data['description'] ?></textarea>
                    </div>
                    <div class="col-md-12">
                <label for="">Téléverser une image</label>
                <input type="file" name="image" class="form-control" >
               
                <input type="hidden" name="old_image" value="<?= $data['image'] ?>" >
                <label for="mb-0">Image actuelle :</label>
                <img src="../../uploads/images/hero/<?= $data['image'] ?>" height="100px" width="100px" alt="">
                    </div>
                    <div class="col-md-12 py-4">
                        <button class="btn btn-primary" name="Update_hero-btn">Mettre à jour</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <?php 
              }
              else{
                echo "Héros non trouvé";
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