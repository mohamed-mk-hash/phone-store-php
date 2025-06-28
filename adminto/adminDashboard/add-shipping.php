<?php
include('includes/header.php');

?>

<div class="container">
    <div class="row">
       <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Ajouter un nouveau lieu de livraison</h4>
            </div>
            <div class="card-body">
                <form action="code.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                <label for="">Lieu</label>
                <input required placeholder="Entrez le lieu de livraison" type="text" name="place" class="form-control">
                    </div>
                    <div class="col-md-12">
                <label for="">Prix</label>
                <input required placeholder="Entrez le prix de livraison" type="number" name="price" class="form-control">
                    </div>         
                    <div class="col-md-12 py-4">
                        <button class="btn btn-primary" name="add_shipping-btn">Enregistrer</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
       </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>