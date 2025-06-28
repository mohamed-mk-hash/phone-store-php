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
                <h4>Ajouter une publicité</h4>
            </div>
            <div class="card-body">
            
                <form id="publicite-form" action="code.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="lien">Ajouter un lien</label>
                            <input required placeholder="Entrez une lien pour la pub" type="text" name="lien" id="lien" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label for="image">Téléverser une image</label>
                            <input required placeholder="Entrez une image" type="file" name="image" id="image" class="form-control">
                        </div>
                        <div class="col-md-12 py-4">
                            <button type="submit" class="btn btn-primary" name="add_publicite-btn">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
       </div>
    </div>
</div>



<?php include('includes/footer.php'); ?>