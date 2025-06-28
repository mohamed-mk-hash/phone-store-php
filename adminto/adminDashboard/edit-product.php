<?php 
include('../../middleware/adminMiddlware.php');

if(!isset($_SESSION['auth'])) {
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
       <?php
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $products = getByIdproduct("products", $id);
            if(mysqli_num_rows($products) > 0){
                $data = mysqli_fetch_array($products);

                $ramRomQuery = "SELECT * FROM ram_rom WHERE product_id = '$id'";
                $ramRomResult = mysqli_query($con, $ramRomQuery);
                $ramRomData = [];
                while ($row = mysqli_fetch_assoc($ramRomResult)) {
                    $ramRomData[] = $row;
                }

                $colorsQuery = "SELECT * FROM product_colors WHERE product_id = '$id'";
                $colorsResult = mysqli_query($con, $colorsQuery);
                $colorsData = [];
                while ($row = mysqli_fetch_assoc($colorsResult)) {
                    $colorsData[] = $row['color'];
                }
        ?>

        <div class="card">
            <div class="card-header">
                <h4>Modifier les produits</h4>
                <a href="products.php" class="btn btn-primary float-end">Retour</a>
            </div>
            <div class="card-body">
                <form id="editProductForm" action="code.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                <div class="col-md-6">
                <label for="" >Sélectionner une catégorie</label>
                <select required name="category_id" class="form-control mb-2" >
                  <option selected disabled>Sélectionner une catégorie</option>
                  <?php 
                  $categories = getcategory("phone");
                  if(mysqli_num_rows($categories) > 0){
                    foreach($categories as $item){
                  ?>
                 <option value="<?= $item['id'] ?>" <?= $data['category_id'] ==  $item['id']? 'selected':'' ?>><?= $item['name'] ?></option>
                  <?php 
                    }
                }
                else {
                    echo "Aucune catégorie disponible";
                }
                  ?>
                </select>
                    </div>
                    <input type="hidden" name="product_id" value="<?= $data['id'] ?>" >
                    <div class="col-md-6">
                <label class="">Nom</label>
                <input value="<?= $data['name'] ?>" required placeholder="Entrez le nom du produit" type="text" name="name" class="form-control md-2">
                    </div>

                    <div class="col-md-6">
                <label class="mb-0">Marque</label>
                <input value="<?= $data['brand'] ?>" required placeholder="Entrez la marque du produit" type="text" name="brand" class="form-control mb-2">
                    </div>

                    <div class="col-md-6">
                <label class="mb-0">Quantité</label>
                <input value="<?= $data['qty'] ?>" required placeholder="Entrez la quantité" type="number" name="qty" class="form-control mb-2">
                    </div>

                    <div class="col-md-12">
                <label class="mb-0">Description</label>
                <textarea value="" required rows="3" type="text" name="description" class="form-control mb-2"><?= $data['description'] ?> </textarea>
                    </div>

                    <div class="col-md-6">
                <label class="mb-0">Largeur</label>
                <input value="<?= $data['width'] ?>" required placeholder="Entrez la largeur du produit" type="number" name="width" class="form-control mb-2">
                    </div>

                    <div class="col-md-6">
                <label class="mb-0">Hauteur</label>
                <input value="<?= $data['height'] ?>" required placeholder="Entrez la hauteur du produit" type="number" name="height" class="form-control mb-2">
                    </div>

                    <div class="col-md-12">
                <label class="mb-0">Batterie</label>
                <input value="<?= $data['battery'] ?>" required placeholder="Entrez la batterie du téléphone" type="number" name="battery" class="form-control mb-2">
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
                        <div id="ramRomError" class="error-message">Vous ne pouvez sélectionner que 2 combinaisons RAM/ROM maximum.</div>
                        <ul id="selected-list">
                            <?php
                            foreach ($ramRomData as $item) {
                                echo "<li class='selected-item' data-ram='{$item['ram']}' data-rom='{$item['rom']}' data-price='{$item['price']}'>
                                        {$item['ram']} / {$item['rom']} - {$item['price']}DZD
                                        <button class='remove-btn' onclick='removeSelection(this)'>Supprimer</button>
                                      </li>";
                            }
                            ?>
                        </ul>
                    </div>

                    <div class="row color-selection py-3">
                        <div class="col-md-4">
                            <label>Choisissez une couleur</label>
                            <input type="color" id="colorPicker" class="form-control" value="#000000">
                        </div> 
                        <div class="col-md-4 py-3">
                            <button type="button" class="btn btn-primary" onclick="addColor()">Ajouter couleur</button>
                        </div>
                        <div id="colorError" class="error-message" style="margin-top:-10px; margin-bottom: 20px;">Vous ne pouvez sélectionner que 3 couleurs maximum.</div>

                        <div class="col-md-12 selected-items" style="margin-top: -10px" >
                            <h5>Couleurs sélectionnées:</h5>
                            <ul id="selected-colors">
                                <?php
                                foreach ($colorsData as $color) {
                                    echo "<li class='color-item' data-color='{$color}'>
                                            <div class='color-box' style='background: {$color};'></div>
                                            <button class='remove-btn' onclick='removeColor(this)'>Supprimer</button>
                                          </li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label>Téléverser des images</label>
                        <input type="hidden" name="old_images" value="<?= $data['image'] ?>" >
                        <input type="file" name="images[]" id="imageUpload" class="form-control mb-2" multiple onchange="validateImageSelection()">
                        <div id="imageError" class="error-message" style="margin-top:-10px; margin-bottom: 20px;" >Vous ne pouvez sélectionner que 3 images maximum.</div>

                        <label for="">Images actuelles :</label>
                        <div>
                            <?php
                            $imagesQuery = "SELECT * FROM product_images WHERE product_id = '$id'";
                            $images = mysqli_query($con, $imagesQuery);

                            while ($imageData = mysqli_fetch_assoc($images)) {
                                ?>
                                <img src="../../uploads/images/product/<?= $imageData['image'] ?>" height="100px" width="100px" alt="Image du produit">
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <input type="hidden" value="<?= $_SERVER['REQUEST_URI'] ?>" name="redirect_to" >

                    <div class="col-md-12 py-4">
                        <button class="btn btn-primary" type="submit" name="update_product-btn">Mettre à jour</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <?php
            }
            else {
                echo "Produit non trouvé pour l'ID donné";
            }
        }
        else {
            echo "ID manquant dans l'URL";
        }
        ?>
       </div>
    </div>
</div>

<script>
var selectedItems = <?= json_encode($ramRomData) ?>;
var selectedColors = <?= json_encode($colorsData) ?>;

function validateImageSelection() {
    var imageInput = document.getElementById('imageUpload');
    var errorElement = document.getElementById('imageError');
    
    if (imageInput.files.length > 3) {
        errorElement.style.display = 'block';
        imageInput.value = '';
    } else {
        errorElement.style.display = 'none';
    }
}

function addSelection() {
    var ramRomError = document.getElementById('ramRomError');
    
    if (selectedItems.length >= 2) {
        ramRomError.style.display = 'block';
        return;
    } else {
        ramRomError.style.display = 'none';
    }

    var ram = document.getElementById('ram').value;
    var rom = document.getElementById('rom').value;
    var price = document.getElementById('price').value;

    if (ram && rom && price) {
        // Check if combination already exists
        var exists = selectedItems.some(item => item.ram === ram && item.rom === rom);
        if (exists) {
            alert("Cette combinaison RAM/ROM existe déjà.");
            return;
        }
        
        selectedItems.push({ ram: ram, rom: rom, price: price });
        var listItem = document.createElement('li');
        listItem.classList.add('selected-item');
        listItem.setAttribute('data-ram', ram);
        listItem.setAttribute('data-rom', rom);
        listItem.setAttribute('data-price', price);
        listItem.textContent = `${ram} / ${rom} - ${price}DZD`;

        var removeBtn = document.createElement('button');
        removeBtn.classList.add('remove-btn');
        removeBtn.textContent = 'Supprimer';
        removeBtn.onclick = function() {
            var index = selectedItems.findIndex(item => item.ram === ram && item.rom === rom);
            if (index !== -1) selectedItems.splice(index, 1);
            listItem.remove();
            ramRomError.style.display = 'none';
        };

        listItem.appendChild(removeBtn);
        document.getElementById('selected-list').appendChild(listItem);
        
        // Clear price field
        document.getElementById('price').value = '';
    }
}

function removeSelection(button) {
    var listItem = button.parentElement;
    var ram = listItem.getAttribute('data-ram');
    var rom = listItem.getAttribute('data-rom');
    var price = listItem.getAttribute('data-price');

    selectedItems = selectedItems.filter(item => !(item.ram === ram && item.rom === rom && item.price === price));
    listItem.remove();
    document.getElementById('ramRomError').style.display = 'none';
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
    listItem.classList.add('color-item');
    listItem.setAttribute('data-color', color);
    listItem.innerHTML = `<div class="color-box" style="background: ${color};"></div> 
                          <button class="remove-btn" onclick="removeColor(this)">Supprimer</button>`;

    document.getElementById('selected-colors').appendChild(listItem);
}

function removeColor(button) {
    var listItem = button.parentElement;
    var color = listItem.getAttribute('data-color');
    selectedColors = selectedColors.filter(c => c !== color);
    listItem.remove();
    document.getElementById('colorError').style.display = 'none';
}

document.getElementById('editProductForm').addEventListener('submit', function(event) {
    // Validate images again in case client-side validation was bypassed
    var imageInput = document.getElementById('imageUpload');
    if (imageInput.files.length > 3) {
        document.getElementById('imageError').style.display = 'block';
        event.preventDefault();
        return;
    }
    
    // Validate at least one RAM/ROM combination
    if (selectedItems.length === 0) {
        document.getElementById('ramRomError').style.display = 'block';
        document.getElementById('ramRomError').textContent = "Veuillez ajouter au moins une combinaison RAM/ROM.";
        event.preventDefault();
        return;
    }
    
    // Validate at least one color
    if (selectedColors.length === 0) {
        document.getElementById('colorError').style.display = 'block';
        document.getElementById('colorError').textContent = "Veuillez ajouter au moins une couleur.";
        event.preventDefault();
        return;
    }

    // Add hidden inputs
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

// Initialize existing items count
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're already at max for RAM/ROM
    if (selectedItems.length >= 2) {
        document.getElementById('ramRomError').style.display = 'block';
    }
    
    // Check if we're already at max for colors
    if (selectedColors.length >= 3) {
        document.getElementById('colorError').style.display = 'block';
    }
});
</script>

<?php include('includes/footer.php'); ?>