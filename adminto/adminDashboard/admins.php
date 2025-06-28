<?php 
include('../../middleware/adminMiddlware.php');

if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Une connexion est requise pour accéder au tableau de bord administrateur";
    header('Location: ../main/login.php');
    exit();
}

include('includes/header.php');

$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
?>

<div class="container">
    <div class="row">
       <div class="col-md-12">
        <div class="card">
            <div class="card-header py-1">
                <h4>Clients</h4>
            </div>
            <div class="card-body" id="client_table">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $query = "SELECT * FROM users";
                        if (!empty($search)) {
                            $query .= " WHERE username LIKE '%$search%' OR email LIKE '%$search%'";
                        }
                        $client = mysqli_query($con, $query);
                        if (mysqli_num_rows($client) > 0) {
                            foreach ($client as $item) {
                                $buttonText = ($item['status'] == 1) ? "Débloquer" : "Bloquer";
                                $buttonClass = ($item['status'] == 1) ? "btn-success" : "btn-danger";
                                ?>
                                <tr>
                                    <td><?= $item['username'] ?></td>
                                    <td><?= $item['email'] ?></td>
                                    <td>
                                        <button class="btn <?= $buttonClass ?> delete_client_btn" type="button" value="<?= $item['id']; ?>"><?= $buttonText ?></button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>Aucun enregistrement trouvé</td></tr>";
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