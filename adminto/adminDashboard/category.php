<?php
include('../../middleware/adminMiddlware.php');

if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Une connexion est requise pour accéder au tableau de bord administrateur";
    header('Location: ../main/login.php');
    exit();
}

include('includes/header.php');


$filterType = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-1">
                    <h4>Catégories</h4>

                    <div class="dropdown float-start mt-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Filtrer les catégories
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="category.php">Toutes</a></li>
                            <li><a class="dropdown-item" href="category.php?type=phone">Téléphone</a></li>
                            <li><a class="dropdown-item" href="category.php?type=accessoire">Accessoire</a></li>
                        </ul>
                    </div>

                    <div class="dropdown float-end me-2">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="accessoryDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Ajouter une catégorie
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="accessoryDropdown">
                            <?php
                            $types = getAll("type");
                            if (mysqli_num_rows($types) > 0) {
                                foreach ($types as $type) {
                                    ?>
                                    <li><a class="dropdown-item"
                                            href="add-category.php?type=<?= $type['name'] ?>"><?= $type['name'] ?></a></li>
                                    <?php
                                }
                            } else {
                                echo "<li><a class='dropdown-item disabled'>Aucun type trouvé</a></li>";
                            }
                            ?>

                        </ul>
                    </div>
                </div>

                <div class="card-body" id="category_table">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>

                                <th>Nom</th>
                                <th>Image</th>
                                <th>Modifier</th>
                                <th>Supprimer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $query = "
                        SELECT categories.*, type.name AS type_name 
                        FROM categories 
                        INNER JOIN type ON categories.type_id = type.id
                        ";


                            $conditions = [];
                            if (!empty($filterType)) {
                                $conditions[] = "type.name = '$filterType'";
                            }
                            if (!empty($search)) {
                                $conditions[] = "categories.name LIKE '%$search%'";
                            }


                            if (count($conditions) > 0) {
                                $query .= " WHERE " . implode(" AND ", $conditions);
                            }


                            $category = mysqli_query($con, $query);

                            if (mysqli_num_rows($category) > 0) {
                                foreach ($category as $item) {
                                    ?>
                                    <tr>

                                        <td><?= $item['name'] ?></td>
                                        <td>
                                            <img src="../../uploads/images/category/<?= $item['image'] ?>" width="100px"
                                                height="100" alt="">
                                        </td>
                                        <td>
                                            <a href="edit-category.php?id=<?= $item['id']; ?>" class="btn btn-primary">Modifier</a>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger delete_category_btn" type="button"
                                                value="<?= $item['id']; ?>">Supprimer</button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Aucun enregistrement trouvé</td></tr>";
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