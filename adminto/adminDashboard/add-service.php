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
                <h4>Ajouter un service</h4>
            </div>
            <div class="card-body">
                <form action="code.php" method="POST">
                <div class="row">
                    <div class="col-md-12">
                <label for="">Nom</label>
                <input placeholder="Entrez le nom du service" type="text" name="title" class="form-control">
                    </div>
                    <div class="col-md-12">
                <label for="">Description</label>
                <textarea rows="3" type="text" name="description" class="form-control"> </textarea>
                    </div>
                    <div class="col-md-12">
                <label for="">Nom de l'icône</label>
                <input placeholder="Entrez le nom de l'icône" type="text" name="icon" class="form-control">
                    </div>
                    <div class="col-md-12 py-4">
                        <button class="btn btn-primary" name="add_service-btn">Enregistrer</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
       </div>
    </div>
</div>
    <?php include('includes/footer.php'); ?>