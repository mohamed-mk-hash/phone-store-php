<?php 
include('../../middleware/adminMiddlware.php');

if(!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Une connexion est requise pour accéder au tableau de bord administrateur";
    header('Location: ../../main/login.php');
    exit();
}

include('includes/header.php');

$type = isset($_GET['type']) ? $_GET['type'] : null;

if (!$type) {
    echo "Erreur : Aucun type spécifié !";
    exit;
}

$categories = getAll("categories WHERE name = '$type'");
?>

<style>
    .selected-items {
        margin-top: 20px;
    }
    .selected-item, .color-item {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .remove-btn {
        background-color: red;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }

    .color-box {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1px solid #000;
    }
    
    .error-message {
        color: red;
        font-size: 0.8em;
        margin-top: 5px;
        display: none;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Ajouter des accessoires</h4>
                </div>
                <div class="card-body">
                    <form action="code.php" method="POST" enctype="multipart/form-data" id="addAccessoryForm">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="categorySelect">Catégorie</label>
                                <select required name="category_id" class="form-control mb-2" readonly>
                                    <option selected disabled>Sélectionner une catégorie</option>
                                    <?php 
                                    if (mysqli_num_rows($categories) > 0) {
                                        foreach ($categories as $item) { ?>
                                            <option value="<?= $item['id'] ?>" selected><?= $item['name'] ?></option>
                                        <?php }
                                    } else {
                                        echo "<option disabled>Aucune catégorie disponible</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Nom</label>
                                <input required placeholder="Entrez le nom du produit" type="text" name="name" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Marque</label>
                                <input required placeholder="Entrez la marque du produit" type="text" name="brand" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Quantité</label>
                                <input required placeholder="Entrez la quantité" type="number" name="qty" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Prix</label>
                                <input required placeholder="Entrez le prix d'origine" type="number" name="price" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Puissance (W)</label>
                                <input required placeholder="Entrez la puissance" type="number" name="power" class="form-control mb-2">
                            </div>

                            <div class="col-md-12">
                                <label>Description</label>
                                <textarea required rows="3" name="description" class="form-control mb-2"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label>Largeur</label>
                                <input required placeholder="Entrez la largeur" type="number" name="width" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Hauteur</label>
                                <input required placeholder="Entrez la hauteur" type="number" name="height" class="form-control mb-2">
                            </div>

                            <div class="row color-selection py-3">
                                <div class="col-md-4">
                                    <label>Choisissez une couleur</label>
                                    <input type="color" id="colorPicker" class="form-control" value="#000000"><br>
                                </div> 
                                <div class="col-md-4 py-3">
                                    <button type="button" class="btn btn-primary" onclick="addColor()">Ajouter couleur</button>
                                </div>
                                <div id="colorError" class="error-message" style="margin-bottom:20px; margin-top: -10px;" >Vous ne pouvez sélectionner que 3 couleurs maximum.</div>

                                <div class="col-md-12 selected-items" style="margin-top: -10px">
                                    <h5>Couleurs sélectionnées:</h5>
                                    <ul id="selected-colors-list"></ul>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label>Téléverser des images</label>
                                <input required type="file" name="images[]" multiple class="form-control mb-2" id="imageUpload" onchange="validateImageSelection()">
                                <div id="imageError" class="error-message">Vous ne pouvez sélectionner que 3 images maximum.</div>
                            </div>

                            <input type="hidden" value="<?= $_SERVER['REQUEST_URI'] ?>" name="redirect_to">
                            <div class="col-md-12 py-4">
                                <button class="btn btn-primary" type="submit" name="add_accesoir-btn">Enregistrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var selectedColors = [];

function validateImageSelection() {
    var imageInput = document.getElementById('imageUpload');
    var errorElement = document.getElementById('imageError');
    
    if (imageInput.files.length > 3) {
        errorElement.style.display = 'block';
        // Clear the selected files
        imageInput.value = '';
    } else {
        errorElement.style.display = 'none';
    }
}

function addColor() {
    var colorError = document.getElementById('colorError');
    
    if (selectedColors.length >= 3) {
        colorError.style.display = 'block';
        return;
    } else {
        colorError.style.display = 'none';
    }

    var color = document.getElementById('colorPicker').value;

    if (selectedColors.includes(color)) {
        alert("Cette couleur est déjà sélectionnée.");
        return;
    }

    selectedColors.push(color);

    var listItem = document.createElement('li');
    listItem.classList.add('selected-item');
    listItem.innerHTML = `<div class="color-box" style="background: ${color}; width: 40px; height: 40px; display: inline-block;"></div> 
                          <button class="remove-btn" onclick="removeColor(this, '${color}')">Supprimer</button>`;

    document.getElementById('selected-colors-list').appendChild(listItem);
}

function removeColor(button, color) {
    selectedColors = selectedColors.filter(c => c !== color);
    button.parentElement.remove();
    document.getElementById('colorError').style.display = 'none';
}

document.getElementById('addAccessoryForm').addEventListener('submit', function(event) {
    // Validate images again in case client-side validation was bypassed
    var imageInput = document.getElementById('imageUpload');
    if (imageInput.files.length > 3) {
        document.getElementById('imageError').style.display = 'block';
        event.preventDefault();
        return;
    }
    
    // Validate at least one color is selected
    if (selectedColors.length === 0) {
        alert("Veuillez ajouter au moins une couleur.");
        event.preventDefault();
        return;
    }

    // Add hidden input for colors
    if (selectedColors.length > 0) {
        var colorInput = document.createElement('input');
        colorInput.type = 'hidden';
        colorInput.name = 'selected_colors';
        colorInput.value = JSON.stringify(selectedColors);
        this.appendChild(colorInput);
    }
});
</script>

<?php include('includes/footer.php'); ?>