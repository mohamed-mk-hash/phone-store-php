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
              $category = getById("categories",$id);

              if(mysqli_num_rows($category)> 0){
                $data = mysqli_fetch_array($category);
         
        ?>
        <div class="card">
            <div class="card-header">
                <h4>Modifier la catégorie</h4>
                <a href="category.php" class="btn btn-primary float-end">Retour</a>
            </div>
            <div class="card-body">
                <form action="code.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="category_id" value="<?= $data['id'] ?>" >
                <label for="">Nom</label>
                <input value="<?= $data['name'] ?>" placeholder="Entrez le nom de la catégorie" type="text" name="name" class="form-control">
                    </div>
                    
                    <div class="col-md-12">
                <label for="">Téléverser une image</label>
                <input type="file" name="image" class="form-control" >
                <label for="">Image actuelle :        </label>
                <input type="hidden" name="old_image" value="<?= $data['image'] ?>" >
                <label for="mb-0">Image actuelle</label>
                <img src="../../uploads/images/category/<?= $data['image'] ?>" height="100px" width="100px" alt="">
                    </div>
                    <div class="col-md-12 py-4">
                        <button class="btn btn-primary" name="Update_category-btn">Mettre à jour</button>
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