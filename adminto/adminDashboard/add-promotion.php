<?php 
include('../../middleware/adminMiddlware.php');

if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Une connexion est requise pour accéder au tableau de bord administrateur";
    header('Location: ../main/login.php');
    exit();
}

include('includes/header.php');
$products = getAll("products");


$old_title = isset($_SESSION['preserve_inputs']['title']) ? $_SESSION['preserve_inputs']['title'] : '';
$old_percent = isset($_SESSION['preserve_inputs']['percentage']) ? $_SESSION['preserve_inputs']['percentage'] : '';
$old_date = isset($_SESSION['preserve_inputs']['date_fin']) ? $_SESSION['preserve_inputs']['date_fin'] : '';
$old_selected = isset($_SESSION['preserve_inputs']['selected_products']) ? $_SESSION['preserve_inputs']['selected_products'] : '[]';
unset($_SESSION['preserve_inputs']);
?>

<style>
    .suggestions {
        border: 1px solid #ddd;
        max-height: 150px;
        overflow-y: auto;
        position: absolute;
        background: white;
        width: 100%;
        z-index: 1000;
    }
    .suggestions div {
        padding: 10px;
        cursor: pointer;
    }
    .suggestions div:hover {
        background: #f1f1f1;
    }
    .selected-items {
        margin-top: 20px;
    }
    .selected-item {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
    }
    .remove-btn {
        background-color: red;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        border-radius: 3px;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Ajouter une promotion</h4>
                </div>
                <div class="card-body">

                   <?php if (isset($_SESSION['promo_error'])): ?>
    <div class="custom-alert" id="promoAlert">
        <button class="close-btn" onclick="document.getElementById('promoAlert').remove()">×</button>
        <?php  echo '<div class="alert alert-danger">'.$_SESSION['promo_error'].'</div>'; ?>
    </div>
    <?php unset($_SESSION['promo_error']); ?>
<?php endif; ?>

                    <form id="addPromotionForm" action="code.php" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="title">Nom de la promotion</label>
                                <input placeholder="Entrez le nom de la promotion" type="text" name="title" id="title" class="form-control" value="<?= $old_title ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="percentage">Pourcentage de réduction</label>
                                <input placeholder="Entrez le pourcentage de réduction" type="number" name="percentage" id="percentage" class="form-control" value="<?= $old_percent ?>">
                            </div>
                            <div class="col-md-12">
                                <label for="date_fin">Date de fin de promotion</label>
                                <input placeholder="Entrez la date de fin" type="date" name="date_fin" id="date_fin" class="form-control" value="<?= $old_date ?>">
                            </div>
                            <div class="col-md-12">
                                <label for="productSearch">Rechercher un produit</label>
                                <input type="text" id="productSearch" class="form-control" placeholder="Tapez pour rechercher...">
                                <div id="suggestions" class="suggestions"></div>
                            </div>
                            <div class="col-md-12 py-3">
                                <button type="button" class="btn btn-primary" onclick="addSelectedProduct()">Ajouter Produit</button>
                            </div>
                            <div class="col-md-12 selected-items">
                                <h5>Produits sélectionnés:</h5>
                                <ul id="selected-products"></ul>
                            </div>
                            <div class="col-md-12 py-4">
                                <button class="btn btn-primary" name="add_promotion_btn">Enregistrer</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var products = [
        <?php
        if (mysqli_num_rows($products) > 0) {
            while ($product = mysqli_fetch_assoc($products)) {
                echo "{ id: '{$product['id']}', name: '".addslashes($product['name'])."' },";
            }
        }
        ?>
    ];

    var selectedProducts = [];

    document.getElementById('productSearch').addEventListener('input', function() {
        let query = this.value.toLowerCase();
        let suggestions = document.getElementById('suggestions');
        suggestions.innerHTML = '';

        if (query.length > 0) {
            let filteredProducts = products.filter(product => product.name.toLowerCase().includes(query));
            filteredProducts.forEach(product => {
                let div = document.createElement('div');
                div.textContent = product.name;
                div.onclick = function() {
                    document.getElementById('productSearch').value = product.name;
                    document.getElementById('productSearch').dataset.id = product.id;
                    suggestions.innerHTML = '';
                };
                suggestions.appendChild(div);
            });
        }
    });

    function addSelectedProduct() {
        let searchInput = document.getElementById('productSearch');
        let productId = searchInput.dataset.id;
        let productName = searchInput.value;

        if (!productId || selectedProducts.includes(productId)) {
            return;
        }

        selectedProducts.push(productId);

        let listItem = document.createElement('li');
        listItem.classList.add('selected-item');
        listItem.innerHTML = `${productName} <button class='remove-btn' onclick='removeProduct(${productId}, this)'>Supprimer</button>`;

        document.getElementById('selected-products').appendChild(listItem);
        searchInput.value = '';
        searchInput.dataset.id = '';
    }

    function removeProduct(id, button) {
        selectedProducts = selectedProducts.filter(pid => pid != id);
        button.parentElement.remove();
    }

    document.getElementById('addPromotionForm').addEventListener('submit', function(event) {
        if (selectedProducts.length > 0) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_products';
            input.value = JSON.stringify(selectedProducts);
            this.appendChild(input);
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        var dateInput = document.getElementById("date_fin");
        var today = new Date().toISOString().split("T")[0];
        dateInput.setAttribute("min", today);

        const oldSelected = <?= $old_selected ?>;
        oldSelected.forEach(id => {
            const product = products.find(p => p.id == id);
            if (product) {
                selectedProducts.push(product.id);
                const listItem = document.createElement('li');
                listItem.classList.add('selected-item');
                listItem.innerHTML = `${product.name} <button class='remove-btn' onclick='removeProduct(${product.id}, this)'>Supprimer</button>`;
                document.getElementById('selected-products').appendChild(listItem);
            }
        });
    });
</script>

<?php include('includes/footer.php'); ?>
