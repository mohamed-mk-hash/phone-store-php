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

            
                

             
                $colorsQuery = "SELECT * FROM product_colors WHERE product_id = '$id'";
                $colorsResult = mysqli_query($con, $colorsQuery);
                $colorsData = [];
                while ($row = mysqli_fetch_assoc($colorsResult)) {
                    $colorsData[] = $row['color'];
                }
        ?>

        <div class="card">
            <div class="card-header">
                <h4>Modifier l'accessoire</h4>
                <a href="accesoirproduct.php" class="btn btn-primary float-end">Retour</a>
            </div>
            <div class="card-body">
                <form id="editAccessoryForm" action="code.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Sélectionner une catégorie</label>
                            <select required name="category_id" class="form-control mb-2">
                                <option selected disabled>Sélectionner une catégorie</option>
                                <?php 
                                $categories = getAll("categories");
                                if(mysqli_num_rows($categories) > 0){
                                    foreach($categories as $item){
                                        echo "<option value='{$item['id']}' " . ($data['category_id'] == $item['id'] ? 'selected' : '') . ">{$item['name']}</option>";
                                    }
                                } else {
                                    echo "<option disabled>Aucune catégorie disponible</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <input type="hidden" name="accessory_id" value="<?= $data['id'] ?>">
                        <div class="col-md-6">
                            <label>Nom</label>
                            <input value="<?= $data['name'] ?>" required placeholder="Entrez le nom du produit" type="text" name="name" class="form-control mb-2">
                        </div>

                        <div class="col-md-6">
                            <label>Marque</label>
                            <input value="<?= $data['brand'] ?>" required placeholder="Entrez la marque du produit" type="text" name="brand" class="form-control mb-2">
                        </div>

                        <div class="col-md-6">
                            <label>Quantité</label>
                            <input value="<?= $data['qty'] ?>" required placeholder="Entrez la quantité" type="number" name="qty" class="form-control mb-2">
                        </div>

                        <div class="col-md-6">
                            <label>Prix</label>
                            <input value="<?= $data['price'] ?>" required placeholder="Entrez le prix" type="number" name="price" class="form-control mb-2">
                        </div>

                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea required rows="3" name="description" class="form-control mb-2"><?= $data['description'] ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label>Largeur</label>
                            <input value="<?= $data['width'] ?>" required placeholder="Entrez la largeur" type="number" name="width" class="form-control mb-2">
                        </div>

                        <div class="col-md-6">
                            <label>Hauteur</label>
                            <input value="<?= $data['height'] ?>" required placeholder="Entrez la hauteur" type="number" name="height" class="form-control mb-2">
                        </div>

                        <div class="row color-selection py-3">
                            <div class="col-md-4">
                                <label>Choisissez une couleur</label>
                                <input type="color" id="colorPicker" class="form-control" value="#000000"><br>
                            </div> 
                            <div class="col-md-4 py-3">
                                <button type="button" class="btn btn-primary" onclick="addColor()">Ajouter couleur</button>
                            </div>

                            <div class="col-md-12 selected-items" style="margin-top: -10px">
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
                         <input type="hidden" value="<?= $_SERVER['REQUEST_URI'] ?>" name="redirect_to">
                        <div class="col-md-12 py-4">
                            <button class="btn btn-primary" type="submit" name="update_accessory-btn">Mettre à jour</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
            } else {
                echo "Produit non trouvé pour l'ID donné";
            }
        } else {
            echo "ID manquant dans l'URL";
        }
        ?>
       </div>
    </div>
</div>

<script>
    var selectedColors = <?= json_encode($colorsData) ?>;

    function addColor() {
        if (selectedColors.length >= 3) {
            alert("Vous ne pouvez sélectionner que 3 couleurs maximum.");
            return;
        }

        var color = document.getElementById('colorPicker').value;
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
    }

    document.getElementById('editAccessoryForm').addEventListener('submit', function(event) {
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