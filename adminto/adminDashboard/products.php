<?php
include('../../middleware/adminMiddlware.php');

if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Une connexion est requise pour accéder au tableau de bord administrateur";
    header('Location: ../main/login.php');
    exit();
}

include('includes/header.php');

$category_filter = "SELECT categories.* 
FROM categories 
INNER JOIN type ON categories.type_id = type.id 
WHERE type.name = 'accessoire';";
$category_filter_result = mysqli_query($con, $category_filter);

$filterType = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-1">
                    <h4>Produits</h4>

                    <div class="dropdown float-start mt-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Filtrer les produits
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="products.php">Tous</a></li>
                            <?php
                            $product_types = getAll("type");
                            if (mysqli_num_rows($product_types) > 0) {
                                foreach ($product_types as $type) {
                                    echo "<li><a class='dropdown-item' href='products.php?type=" . $type['name'] . "'>" . ucfirst($type['name']) . "</a></li>";
                                }
                            }
                            ?>
                        </ul>
                    </div>

                    <a href="add-product.php" class="btn btn-primary float-end">Ajouter un téléphone</a>
                    <div class="dropdown float-end me-3">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="accessoryDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Ajouter un accessoire
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="accessoryDropdown">
                            <?php
                            if (mysqli_num_rows($category_filter_result) > 0) {
                                foreach ($category_filter_result as $opp) {
                                    $href = $opp['name'] === 'Power Bank' || $opp['name'] === 'chargeur'
                                        ? "add_charger_power_bank.php?type=" . urlencode($opp['name'])
                                        : "add-otheraccesoir.php?type=" . urlencode($opp['name']);
                                    ?>
                                    <li><a class="dropdown-item" href="<?= $href; ?>"><?= $opp["name"] ?></a></li>
                                <?php }
                            } ?>
                        </ul>
                    </div>
                </div>
               <div class="card-body" id="product_table">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prix</th>
                <th>Image</th>
                <th>Modifier</th>
                <th>Promotion</th>
                <th>Supprimer</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "
                SELECT products.*, 
                       type.name AS type_name, 
                       categories.name AS category_name, 
                       (SELECT image FROM product_images WHERE product_id = products.id LIMIT 1) AS image,
                       COALESCE((SELECT price FROM ram_rom WHERE product_id = products.id LIMIT 1), products.price) AS price,
                       promotions.id AS promo_id,
                       promotions.name AS promo_name,
                       promotions.percent AS promo_percent,
                       promotions.date_fin AS promo_date
                FROM products
                INNER JOIN type ON products.type_id = type.id
                INNER JOIN categories ON products.category_id = categories.id
                LEFT JOIN product_promo ON products.id = product_promo.product_id
                LEFT JOIN promotions ON product_promo.promo_id = promotions.id
                WHERE 1
            ";

            if (!empty($filterType)) {
                $query .= " AND type.name = '$filterType'";
            }

            if (!empty($search)) {
                $query .= " AND products.name LIKE '%$search%'";
            }

            $product = mysqli_query($con, $query);

            if (mysqli_num_rows($product) > 0) {
                foreach ($product as $item) {
                    ?>
                    <tr>
                        <td><?= wordwrap($item['name'], 40, "<br>") ?></td>
                        <td><?= $item['price'] ?> DZD</td>
                        <td>
                            <img src="../../uploads/images/product/<?= $item['image'] ?>" width="150px" height="150px" alt="">
                        </td>
                        <td>
                            <?php
                            $battery_accessories = ['écouteurs sans fil', 'chargeur', 'Power Bank'];
                            if ($item['type_name'] == 'phone') {
                                $edit_href = "edit-product.php";
                            } elseif (in_array($item['category_name'], $battery_accessories)) {
                                $edit_href = "edit-chargeur-powerbank.php";
                            } else {
                                $edit_href = "editother-accesoir.php";
                            }
                            ?>
                            <a href="<?= $edit_href ?>?id=<?= $item['id']; ?>" class="btn btn-primary">Modifier</a>
                        </td>

                       
                        <td>
                            <?php if ($item['promo_name']) { ?>
                                <div>
                                    <strong><?= $item['promo_name'] ?></strong>
                                    /<?= $item['promo_percent'] ?>% <br>
                                    date de fin : <br> <?= $item['promo_date'] ?>
                                </div>
                                <form method="POST" action="code.php">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="remove_promo_btn" class="btn btn-danger mt-2">Supprimer</button>
                                </form>
                            <?php } else { ?>
                                <form method="POST" action="code.php">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <select name="promo_id" class="form-control" required    >
                                        <option value="" class="small-text">Sélectionner une promo</option>
                                        <?php
                                        $promoQuery = "SELECT * FROM promotions";
                                        $promos = mysqli_query($con, $promoQuery);
                                        while ($promo = mysqli_fetch_assoc($promos)) {
                                            echo '<option value="'.$promo['id'].'">'.$promo['name'].' - '.$promo['percent'].'%</option>';
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" name="add_promo_product_btn" class="btn btn-success mt-2">Enregistrer</button>
                                </form>
                            <?php } ?>
                        </td>

                        <td>
                            <button class="btn btn-danger delete_product_btn" type="button" value="<?= $item['id']; ?>">Supprimer</button>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='6'>Aucun enregistrement trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

            </div>
        </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>

<script>
   $('.delete_promotion_btn').click(function (e) {
    e.preventDefault();
    var promoId = $(this).val();
    var button = $(this);

    swal({
        title: "Êtes-vous sûr?",
        text: "Voulez-vous vraiment supprimer cette promotion?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                method: "POST",
                url: "code.php",
                data: {
                    'id': promoId,
                    'delete_promotion_btn': true,
                },
                success: function (response) {
                    if (response == 200) {
                        swal("Succès!", "Promotion supprimée avec succès.", "success");

                        
                        var promoContainer = button.closest("td");
                        promoContainer.html('<a href="add-promo.php?id=' + promoId + '" class="btn btn-secondary">Ajouter un promo</a>');

                    } else {
                        swal("Erreur!", "Quelque chose s'est mal passé.", "error");
                    }
                }
            });
        }
    });
});


</script>