<?php
include('../../middleware/adminMiddlware.php');

if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Une connexion est requise pour accéder au tableau de bord administrateur";
    header('Location: ../main/login.php');
    exit();
}

include('includes/header.php');
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
</style>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Ajouter des produits</h4>
                </div>
                <div class="card-body">
    <form id="addProductForm" action="code.php" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <label>Sélectionner une catégorie</label>
                <select required name="category_id" class="form-control mb-2">
                    <option value="" selected>Sélectionner une catégorie</option>
                    <?php
                    $query = "SELECT c.* FROM categories c JOIN type t ON c.type_id = t.id WHERE t.name = 'phone'";
                    $categories = mysqli_query($con, $query);
                    if (mysqli_num_rows($categories) > 0) {
                        foreach ($categories as $item) {
                            $selected = (isset($_SESSION['form_data']['category_id']) && $_SESSION['form_data']['category_id'] == $item['id']) ? 'selected' : '';
                            echo "<option value='{$item['id']}' $selected>{$item['name']}</option>";
                        }
                    } else {
                        echo "<option value=''>Aucune catégorie disponible</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6">
                <label>Nom</label>
                <input required placeholder="Entrez le nom du téléphone" type="text" name="name" class="form-control mb-2" value="<?php echo isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label>Marque</label>
                <input required placeholder="Entrez la marque du téléphone" type="text" name="brand" class="form-control mb-2" value="<?php echo isset($_SESSION['form_data']['brand']) ? htmlspecialchars($_SESSION['form_data']['brand']) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label>Quantité</label>
                <input required placeholder="Entrez la quantité" type="number" name="qty" class="form-control mb-2" value="<?php echo isset($_SESSION['form_data']['qty']) ? htmlspecialchars($_SESSION['form_data']['qty']) : ''; ?>">
            </div>
            <div class="col-md-12">
                <label>Description</label>
                <textarea required rows="3" name="description" class="form-control mb-2"><?php echo isset($_SESSION['form_data']['description']) ? htmlspecialchars($_SESSION['form_data']['description']) : ''; ?></textarea>
            </div>
            <div class="col-md-6">
                <label>Largeur</label>
                <input required placeholder="Entrez la largeur" type="number" name="width" class="form-control mb-2" value="<?php echo isset($_SESSION['form_data']['width']) ? htmlspecialchars($_SESSION['form_data']['width']) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label>Hauteur</label>
                <input required placeholder="Entrez la hauteur" type="number" name="height" class="form-control mb-2" value="<?php echo isset($_SESSION['form_data']['height']) ? htmlspecialchars($_SESSION['form_data']['height']) : ''; ?>">
            </div>
            <div class="col-md-12">
                <label>Batterie</label>
                <input required placeholder="Entrez la batterie" type="number" name="battery" class="form-control mb-2" value="<?php echo isset($_SESSION['form_data']['battery']) ? htmlspecialchars($_SESSION['form_data']['battery']) : ''; ?>">
            </div>
            
            <div class="col-md-4">
                <label>RAM</label>
                <select id="ram" class="form-control">
                    <option value="4GB">4GB</option>
                    <option value="6GB">6GB</option>
                    <option value="8GB">8GB</option>
                    <option value="12GB">12GB</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>ROM</label>
                <select id="rom" class="form-control">
                    <option value="32GB">32GB</option>
                    <option value="64GB">64GB</option>
                    <option value="128GB">128GB</option>
                    <option value="256GB">256GB</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Prix</label>
                <input id="price" type="number" class="form-control">
            </div>
            <div class="col-md-12 py-3">
                <button type="button" class="btn btn-primary" onclick="addSelection()">Ajouter sélection</button>
            </div>
            <div class="col-md-12 selected-items" style="margin-top: -10px" >
                <h5>Choix sélectionnés:</h5>
                <ul id="selected-list"></ul>
            </div>
            <div class="row color-selection py-3">
            <div class="col-md-4">
                <label>Choisissez une couleur</label>
                <input type="color" id="colorPicker" class="form-control" value="#000000"><br>
            </div> 
            <div class="col-md-4 py-3">
                <button type="button" class="btn btn-primary" onclick="addColor()">Ajouter couleur</button>
            </div>

            <div class="col-md-12 selected-items"  style="margin-top: -10px" >
                <h5>Couleurs sélectionnées:</h5>
                <ul id="selected-colors"></ul>
            </div>
        </div>
        <div class="col-md-12">
                <label>Téléverser des images</label>
                <input required type="file" name="images[]" multiple class="form-control mb-2" id="imageUpload" onchange="validateImageSelection()">
                <small id="imageError" class="text-danger" style="display: none;">Vous ne pouvez sélectionner que 3 images maximum.</small>
            </div>
            <input type="hidden" value="<?= $_SERVER['REQUEST_URI'] ?>" name="redirect_to" >
            <div class="col-md-12 py-4">
                <button class="btn btn-primary" type="submit" name="add_product-btn">Enregistrer</button>
            </div>
        </div>
    </form>
</div>
            </div>
        </div>
    </div>
</div>


<script>
var selectedItems = [];
var selectedColors = [];

// Function to validate image selection
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

function addSelection() {
    if (selectedItems.length >= 2) {
        alert("Vous ne pouvez ajouter que 2 combinaisons RAM/ROM maximum.");
        return;
    }

    var ram = document.getElementById('ram').value;
    var rom = document.getElementById('rom').value;
    var price = document.getElementById('price').value;

    if (ram && rom && price) {
        var exists = selectedItems.some(item => item.ram === ram && item.rom === rom);
        if (exists) {
            alert("Cette combinaison RAM/ROM existe déjà.");
            return;
        }
      
        selectedItems.push({ ram: ram, rom: rom, price: price });

        var listItem = document.createElement('li');
        listItem.classList.add('selected-item');
        listItem.textContent = `${ram} / ${rom} - ${price}DZD`;

        var removeBtn = document.createElement('button');
        removeBtn.classList.add('remove-btn');
        removeBtn.textContent = 'Supprimer';
        removeBtn.onclick = function () {
            var index = selectedItems.findIndex(item => item.ram === ram && item.rom === rom);
            if (index !== -1) selectedItems.splice(index, 1);
            listItem.remove();
        };

        listItem.appendChild(removeBtn);
        document.getElementById('selected-list').appendChild(listItem);

        document.getElementById('price').value = '';
    }
}

function addColor() {
    if (selectedColors.length >= 3) {
        alert("Vous ne pouvez sélectionner que 3 couleurs maximum.");
        return;
    }

    var color = document.getElementById('colorPicker').value;

    if (selectedColors.includes(color)) {
        alert("Cette couleur est déjà sélectionnée.");
        return;
    }

    selectedColors.push(color);

    var listItem = document.createElement('li');
    listItem.classList.add('color-item');
    listItem.setAttribute('data-color', color); 
    listItem.innerHTML = `<div class="color-box" style="background: ${color}; width: 40px; height: 40px; display: inline-block;"></div> 
                          <button class="remove-btn" onclick="removeColor(this, '${color}')">Supprimer</button>`;

    document.getElementById('selected-colors').appendChild(listItem);
}

function removeColor(button, color) {
    selectedColors = selectedColors.filter(c => c !== color);
    button.parentElement.remove();
}

document.getElementById('addProductForm').addEventListener('submit', function (event) {
    if (selectedItems.length === 0) {
        alert("Veuillez ajouter au moins une combinaison RAM/ROM.");
        event.preventDefault();
        return;
    }
    
    if (selectedColors.length === 0) {
        alert("Veuillez ajouter au moins une couleur.");
        event.preventDefault();
        return;
    }
    
    // Server-side will handle the final validation, but we can check again here
    var imageInput = document.getElementById('imageUpload');
    if (imageInput.files.length > 3) {
        document.getElementById('imageError').style.display = 'block';
        event.preventDefault();
        return;
    }

    if (selectedItems.length > 0) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ram_rom_pairs';
        input.value = JSON.stringify(selectedItems);
        this.appendChild(input);
    }

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
<?php include('includes/footer.php'); ?>