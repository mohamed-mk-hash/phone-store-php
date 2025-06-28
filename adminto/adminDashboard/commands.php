<?php
include("../../middleware/adminMiddlware.php");

include('includes/header.php');

?>


<div class="container">
    <div class="row">
       <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Commandes</h4>
            </div>
            <div class="card-body">
    <table class="table table-bordered table-striped table-sm" style="font-size: 14px; text-align: center;">
        <thead>
            <tr>
               <th style="width: 12%;">Nom</th>
               <th style="width: 10%;">Téléphone</th>
               <th style="width: 15%;">Email</th>
               <th style="width: 10%;">Ville</th>
               <th style="width: 20%;">Adresse</th>
               <th style="width: 8%;">Qté</th>
               <th style="width: 10%;">Date</th>
               <th style="width: 8%;">Détails</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $command = getUsersProductCount();

            if (mysqli_num_rows($command) > 0) {
                while ($row = mysqli_fetch_assoc($command)) {
            ?>
            <tr>
                <td><?= $row['username'] ?></td>
                <td>0<?= $row['phone'] ?></td>
                <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;">
                    <?= $row['email'] ?>
                </td>
                <td><?= $row['city'] ?></td>
                <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                    <?= $row['address'] ?>
                </td>
                <td><?= $row['product_count'] ?></td>
                <td><?= date('d/m H\h', strtotime($row['created_at'])) ?></td> <!-- Shortened Date -->
                <td>
                    <a href="command_details.php?date=<?= $row['created_at']; ?>" class="btn btn-sm btn-primary">Voir les details</a>                               
                </td>
            </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='8'>Aucune commande</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</div>
</div>
</div>
</div>