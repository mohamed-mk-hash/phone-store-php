<?php 
include('../../middleware/adminMiddlware.php');

if(!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Une connexion est requise pour accéder au tableau de bord administrateur";
    header('Location: ../main/login.php');
    exit();
}

include('includes/header.php');

$type = isset($_GET['type']) ? $_GET['type'] : 'default'; 

?>

<div class="container">
    <div class="row">
       <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Ajouter une catégorie</h4>
                
            </div>
            <div class="card-body">
                <form action="code.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                <label for="">Nom</label>
                <input placeholder="Entrez le nom de la catégorie" type="text" name="name" class="form-control">
                    </div>
                    
                    <div class="col-md-12">
                <label for="">Téléverser une image</label>
                <input placeholder="Entrez une image" type="file" name="image" class="form-control">
                    </div>
                    <input type="hidden" name="type" value="<?=$type;?>" >
                    <div class="col-md-12 py-4">
                        <button class="btn btn-primary" name="add_category-btn">Enregistrer</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
       </div>
    </div>
</div>
    <?php include('includes/footer.php'); ?>