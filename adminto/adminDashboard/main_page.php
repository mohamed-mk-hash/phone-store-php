<?php
include('../../middleware/adminMiddlware.php');

if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Une connexion est requise pour accéder au tableau de bord administrateur";
    header('Location: ../main/login.php');
    exit();
}

include('includes/header.php');



$total_products = countRows('products');
$total_categories = countRows('categories');
$total_clients = countRows('users');
$total_orders = countRows('command');
?>

<div class="container-fluid">
    <div class="row">
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            <h5 class="card-title">Produits</h5>
                            <p class="card-text fs-4"><?= $total_products ?></p>
                            <a href="products.php" class="text-white">Voir les détails <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            <h5 class="card-title">Catégories</h5>
                            <p class="card-text fs-4"><?= $total_categories ?></p>
                            <a href="category.php" class="text-white">Voir les détails <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card bg-warning text-white shadow">
                        <div class="card-body">
                            <h5 class="card-title">Clients</h5>
                            <p class="card-text fs-4"><?= $total_clients ?></p>
                            <a href="admins.php" class="text-white">Voir les détails <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card bg-danger text-white shadow">
                        <div class="card-body">
                            <h5 class="card-title">Commandes</h5>
                            <p class="card-text fs-4"><?= $total_orders ?></p>
                            <a href="commands.php" class="text-white">Voir les détails <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">Statistiques récentes</h5>
                        </div>
                        <div class="card-body">
                            <p>Graphiques ou autres informations peuvent être ajoutés ici.</p>
                            <canvas id="myChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Produits', 'Catégories', 'Clients', 'Commandes'],
            datasets: [{
                label: 'Statistiques',
                data: [<?= $total_products ?>, <?= $total_categories ?>, <?= $total_clients ?>, <?= $total_orders ?>],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.5)',
                    'rgba(40, 167, 69, 0.5)',
                    'rgba(255, 193, 7, 0.5)',
                    'rgba(220, 53, 69, 0.5)'
                ],
                borderColor: [
                    'rgba(0, 123, 255, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include('includes/footer.php'); ?>