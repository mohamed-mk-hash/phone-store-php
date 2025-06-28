<?php

$page = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'],"/")+1) ;

?>
  

  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main" style="overflow-y: hidden;">      <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/material-dashboard/pages/dashboard " target="_blank">
        <span class="ms-1 font-weight-bold text-white">Tableau de bord Admin</span>
      </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto max-height-vh-100" id="sidenav-collapse-main">
      <ul class="navbar-nav">
      <li class="nav-item">
    <a class="nav-link text-white <?= $page == "main_page.php" ? 'active bg-gradient-primary' : ''; ?>" href="main_page.php">
        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">home</i> 
        </div>
        <span class="nav-link-text ms-1">Accueil</span>
    </a>
</li>
        <li class="nav-item">
          <a class="nav-link text-white <?= $page == "category.php"? 'active bg-gradient-primary':''; ?>" href="category.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">category</i>
            </div>
            <span class="nav-link-text ms-1">Catégories</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link text-white <?= $page == "products.php"? 'active bg-gradient-primary':''; ?>" href="products.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center ">
              <i class="material-icons opacity-10">inventory</i>
            </div>
            <span class="nav-link-text ms-1">Produits</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white <?= $page == "produit_promotion.php"? 'active bg-gradient-primary':''; ?>" href="produit_promotion.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center ">
              <i class="material-icons opacity-10">discount</i>
            </div>
            <span class="nav-link-text ms-1">promotion</span>
          </a>
        </li>
        
        
        <li class="nav-item">
    <a class="nav-link text-white <?= $page == "services.php" ? 'active bg-gradient-primary' : ''; ?>" href="services.php">
        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">build</i> 
        </div>
        <span class="nav-link-text ms-1">Reparation</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link text-white <?= $page == "shipping.php" ? 'active bg-gradient-primary' : ''; ?>" href="shipping.php">
        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">local_shipping</i>
        </div>
        <span class="nav-link-text ms-1">Livraison</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link text-white <?= $page == "commands.php" ? 'active bg-gradient-primary' : ''; ?>" href="commands.php">
        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">shopping_cart</i>
        </div>
        <span class="nav-link-text ms-1">Commandes</span>
    </a>
</li>

<li class="nav-item">
          <a class="nav-link text-white <?= $page == "admins.php"? 'active bg-gradient-primary':''; ?>" href="admins.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center ">
              <i class="material-icons opacity-10">person</i>
            </div>
            <span class="nav-link-text ms-1">Clients</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white <?= $page == "commentaire.php"? 'active bg-gradient-primary':''; ?>" href="commentaire.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center ">
              <i class="material-icons opacity-10">comment</i>
            </div>
            <span class="nav-link-text ms-1">Commentaire</span>
          </a>
        </li>
        </li>
      </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
      <div class="mx-3">
        <a class="btn bg-gradient-primary mt-4 w-100" href="../main/logout.php">
          Déconnexion</a>
      </div>
    </div>
  </aside>
  