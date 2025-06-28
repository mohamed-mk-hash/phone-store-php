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
        <div class="card">
            <div class="card-header">
                <h4>Ajouter un nouvel élément Hero</h4>
            </div>
            <div class="card-body">
                <form action="code.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                <label for="">Titre</label>
                <input placeholder="Entrez le titre" type="text" name="title" class="form-control">
                    </div>
                    <div class="col-md-12">
                <label for="">Sous-titre</label>
                <input placeholder="Entrez le sous-titre" type="text" name="subtitle" class="form-control">
                    </div>
                    <div class="col-md-12">
                <label for="">Description</label>
                <textarea rows="3" type="text" name="description" class="form-control"> </textarea>
                    </div>
                    <div class="col-md-12">
                <label for="">Lien</label>
                <textarea rows="3" type="text" name="link" class="form-control"> </textarea>
                    </div>
                    <div class="col-md-12">
                <label for="">Téléverser une image</label>
                <input placeholder="Entrez une image" type="file" name="image" class="form-control">
                    </div>
                    <div class="col-md-12 py-4">
                        <button class="btn btn-primary" name="add_hero-btn">Enregistrer</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
       </div>
    </div>
</div>
    <?php include('includes/footer.php'); ?>