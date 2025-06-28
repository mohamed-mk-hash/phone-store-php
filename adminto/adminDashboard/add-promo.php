<?php 
include('../../middleware/adminMiddlware.php');

if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Une connexion est requise pour accéder au tableau de bord administrateur";
    header('Location: ../main/login.php');
    exit();
}

include('includes/header.php');

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

?>

<div class="container">
    <div class="row">
       <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Ajouter un promo pour ce produit</h4>
            </div>
            <div class="card-body">
                <form action="code.php" method="POST">
                    <div class="row">
                        <input type="hidden" name="product_id" value="<?= $product_id; ?>">
                        
                        <div class="col-md-12">
                            <label for="">Pourcentage</label>
                            <input placeholder="Percent" type="number" name="percent" min="10" max="90" class="form-control">
                        </div>
                        
                        <div class="col-md-12">
                            <label for="">Date de fin</label>
                            <input placeholder="Date de fin" type="date" name="date" id="dateInput" class="form-control" required>
                        </div>
                        
                        <div class="col-md-12 py-4">
                            <button class="btn btn-primary" name="add_promo-btn">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
       </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let dateInput = document.getElementById("dateInput");
        let tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1); 
        let minDate = tomorrow.toISOString().split("T")[0]; 
        dateInput.setAttribute("min", minDate);
    });
</script>

<?php include('includes/footer.php'); ?>
