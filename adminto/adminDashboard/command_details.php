<?php
include("../../middleware/adminMiddlware.php");

if (isset($_GET['date'])) {
    $date = $_GET['date'];

    if (isset($_GET['action']) && $_GET['action'] == 'activate_all') {
        $activationResult = activateAllProductsByDate($date);

        if ($activationResult) {
            redirect("command_details.php?date=" . urlencode($date), "Tous les produits de cette date ont été activés avec succès");
        } else {
            redirect("command_details.php?date=" . urlencode($date), "Échec de l'activation des produits");
        }
    }

    $userProducts = getUserProductsByDate($date);

    if (!$userProducts || mysqli_num_rows($userProducts) == 0) {
        echo "Aucun produit trouvé pour cette date !";
        exit;
    }

        $allActivated = true;
    while ($row = mysqli_fetch_assoc($userProducts)) {
        if ($row['Acive'] == 0) {
            $allActivated = false;
            break;
        }
    }

        mysqli_data_seek($userProducts, 0);
} else {
    echo "Date non fournie !";
    exit;
}

include('includes/header.php');
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Produits du <?= date('d/m/Y H\h', strtotime($date)) ?></h4> 
                </div>
                <div class="card-body">
    <div style="overflow-x: auto; max-width: 100%;">
        <table class="table table-bordered table-striped" style="width: auto; min-width: 600px; text-align: center;">
            <thead>
                <tr>
                    <th style="width: 120px;">Nom du produit</th>
                    <th style="width: 80px;">Couleur</th>
                    <th style="width: 100px;">RAM/ROM</th>
                    <th style="width: 80px;">Quantité</th>
                    <th style="width: 140px;">Méthode de paiement</th>
                    <th style="width: 100px;">Prix total</th>
                    <th style="width: 120px;">Image</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $totalPrice = 0;                     $shippingPrice = 0;                     
                    while ($row = mysqli_fetch_assoc($userProducts)): 
                        $totalPrice += $row['total_price'];                         if ($shippingPrice == 0) {
                            $shippingPrice = $row['shipping_price'];                         }
                ?>
                    <tr>
                        <td style="font-size: 14px;"><?= wordwrap(htmlspecialchars($row['name']), 28, "<br>"); ?></td>
                        <td>
                            <?php if (!empty($row['color'])): ?>
                                <div style="background-color: <?= htmlspecialchars($row['color']) ?>; width: 30px; height: 30px; border-radius: 4px;"></div>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 14px;">
                        <?= !empty($row['ram']) && !empty($row['rom']) 
    ? htmlspecialchars($row['ram']) . " / " . htmlspecialchars($row['rom']) 
    : nl2br(htmlspecialchars(wordwrap("ce produit n'a ni ram ni rom", 20, "\n"))); ?>
                        </td>
                        <td style="font-size: 14px;"><?= htmlspecialchars($row['quantity']); ?></td>
                        <td style="font-size: 14px;"><?= htmlspecialchars($row['payment_methode']); ?></td>
                        <td style="font-size: 14px;"><?= number_format($row['total_price'], 2); ?> DZD</td>
                        <td>
                            <img src="../../uploads/images/product/<?= $row['image'] ?>" style="width: 80px; height: 80px;">
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>


    <div class="mt-3">
        <strong>Prix de livraison:</strong> <?= number_format($shippingPrice, 2); ?> DZD
    </div>


    <div class="mt-3 mb-3">
        <strong>Total à payer:</strong> <?= number_format($totalPrice + $shippingPrice, 2); ?> DZD
    </div>
     
    <a href="commands.php" class="btn btn-primary">Retour aux commandes</a>

    <?php if ($allActivated): ?>
        <button class="btn btn-secondary" disabled  >Déjà activé</button>
    <?php else: ?>
        <a href="command_details.php?date=<?= urlencode($date) ?>&action=activate_all" class="btn btn-secondary">Valider tout</a>
    <?php endif; ?>
    </div>
</div>



            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
