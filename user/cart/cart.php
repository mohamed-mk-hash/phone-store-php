<?php
session_start();


include("../../middleware/adminMiddlware.php");



if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $id => $quantity) {
        if ($quantity > 0) {
            $_SESSION['cart'][$id]['quantity'] = (int)$quantity; 
        } else {
           
            unset($_SESSION['cart'][$id]);
        }
    }
}


$totalPrice = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        
        $price = is_numeric($item['price']) ? (float) $item['price'] : 0;
        $quantity = is_numeric($item['quantity']) ? (int) $item['quantity'] : 1;

        $totalPrice += $price * $quantity;
    }
}

$userId = null;

if (isset($_SESSION['user_id'])) {
  $userId = $_SESSION['user_id'];


  $query = "SELECT COUNT(*) AS wishlist_count FROM wishlist WHERE user_id = ?";
  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $result = $stmt->get_result();
  

  $wishlistCount = $result->fetch_assoc()['wishlist_count'];


  $stmt->close();
} else {

  $wishlistCount = 0;
}


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!--=============== FLATICON ===============-->
    <link rel='stylesheet' 
    href='https://cdn-uicons.flaticon.com/2.5.1/uicons-regular-straight/css/uicons-regular-straight.css'>

    <!--=============== SWIPER CSS ===============-->
    <link
  rel="stylesheet"
  href="assets/css/swiper.css"
     />

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />


    <title>Ecommerce Website</title>
  </head>
  <body>
    <!--=============== HEADER ===============-->
    <header class="header">
    <nav class="nav ">
        <a href="../index.php" class="nav__logo">
            <img src="../assets/img/coin_mobile_logo.jpg" alt="" class="nav__logo-img">
        </a>
        <div class="nav__menu" id="nav-menu">
            <div class="nav__menu-top">
                <a href="../index.php" class="nav__menu-logo">
                    <img src="../assets/img/coin_mobile_logo.jpg" alt="">
                </a>

                <div class="nav__close" id="nav-close">
                    <i class="fi fi-rs-cross-small"></i>
                </div>
            </div>
            <ul class="nav__list">
                <li class="nav__item">
                    <a href="../index.php" class="nav__link ">Accueil</a>
                </li>

                <li class="nav__item">
                    <a href="../shop.php" class="nav__link">Boutique</a>
                </li>
                 
                <li class="nav__item">
                    <a href="../Reparation.php" class="nav__link">Rèparation</a>
                </li>
                <li class="nav__item">
                    <!-- Check if the user is logged in -->  <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="../accounts.php" class="nav__link">Mon Compte</a>
                    <?php else: ?>
                        <a href="../login-register.php" class="nav__link">Mon Compte</a>
                    <?php endif; ?>
                </li>

                <li class="nav__item">
                    <a href="../contact.php" class="nav__link">Contact</a>
                </li>
            </ul>

            
        </div>

        <div class="header__user-action">
        <a href="../wishlist/wishlist.php" class="header__action-btn">
    <img src="../assets/img/icon-heart.svg" alt="">
    <span class="count"><?= $wishlistCount ?></span> <!-- Displaying the wishlist count -->
</a>
    
            <div class="header__action-btn nav__toggle" id="nav-toggle">
                <img src="../assets/img/menu-burger.svg" alt="">
            </div>
        </div>
    </nav>
</header>

    <!--=============== MAIN ===============-->
    <main class="main">
      <!--=============== BREADCRUMB ===============-->
      

      <!--=============== CART ===============-->
      <section class="cart section--lg ">
    <div class="table__container">
        <form action="" method="POST">
            <table class="table">
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>couleur</th>
                    <th>RAM/ROM</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Retirer</th>
                </tr>

                <?php 
                $totalPrice = 0;
                if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                    <?php foreach ($_SESSION['cart'] as $id => $item): 
                      $productName = mysqli_real_escape_string($con, $item['name']);
                      $qtyQuery = "SELECT qty FROM products WHERE name = '$productName'";
                      $qtyResult = mysqli_query($con, $qtyQuery);
                      $qtyData = mysqli_fetch_assoc($qtyResult);
                      $maxQty = $qtyData ? (int) $qtyData['qty'] : 1;
                        $subtotal = $item['price'] * $item['quantity'];
                        $totalPrice += $subtotal;
                    ?>
                        <tr data-id="<?= $id ?>" data-price="<?= $item['price'] ?>">
                            <td data-label="Image"><img src="../<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="table__img"></td>
                            <td data-label="title"><h3 class="table__title"><?= htmlspecialchars($item['name']) ?></h3></td>
                            <td data-label="prix"><span class="table__price"><?= number_format($item['price'], 0, '.', '') ?> DZD</span></td>
                            <td data-label="couleur">
                                <?php if (!empty($item['color'])): ?>
                                  <div class="color__item" style="background-color: <?= htmlspecialchars($item['color']) ?>; width: 40px; height: 40px; border-radius: 4px; display: inline-block; margin: 0 auto; text-align: center;"></div>
                                <?php else: ?>
                                  <span>ce produit n'a pas de couleurs autres que la couleur dans l'image</span>
                                <?php endif; ?>
                            </td>
                            
                            <td data-label="ram/rom">
                                <?php if (!empty($item['ram']) && !empty($item['rom'])): ?>
                                    <span style="font-size: 15px"><?= htmlspecialchars($item['ram']) ?></span> /
                                    <span style="font-size: 15px"><?= htmlspecialchars($item['rom']) ?></span>
                                <?php else: ?>
                                    <span>ce produit n'a ni RAM ni ROM</span>
                                <?php endif; ?>
                            </td>

                            
                            <td data-label="quantite"><input type="number" name="quantity[<?= $id ?>]" value="<?= htmlspecialchars($item['quantity']) ?>" class="quantity" min="1" max="<?= $maxQty ?>"></td>
                            <td data-label="total"><span class="table__subtotal"><?= number_format($subtotal, 0, '.', '') ?> DZD</span></td>
                            
                            <td data-label="supprimer"><a href="remove_from_cart.php?id=<?= $item['id'] ?>" class="table__trash"><i class="fi fi-rs-trash"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Votre panier est vide.</td>
                    </tr>
                <?php endif; ?>
            </table>
           
            <div class="cart__actions">
              
                <a href="../shop.php" class="btn flex btn--md">
                    <i class="fi fi-rs-shuffle"></i> Continuer les achats
                </a>
            </div>
        </form>
    </div>

    <div class="divider">
        <i class="fi fi-rs-fingerprint"></i>
    </div>

    <div class="cart__group grid">
        <div>
            <div class="cart__shipping">
            </div>
        </div>

        <div class="cart__total">
            <h3 class="section__title">Totaux du panier</h3>
            <table class="cart__total-table">
                <tr>
                    <td><span class="cart__total-title">Totaux du panier</span></td>
                    <td><span class="cart__total-price" id="cart-total"><?= number_format($totalPrice, 0, '.', '') ?> DZD</span></td>
                </tr>
            </table>

            
            <form id="checkout-form" action="checkout.php" method="POST">
            <input type="hidden" name="cart_data" value="<?= isset($_SESSION['cart']) ? htmlspecialchars(json_encode($_SESSION['cart'])) : '' ?>">

                <input type="hidden" name="total_price" id="total-price-input" value="<?= $totalPrice ?>">
                <button type="submit" class="btn flex btn--md" id="checkout-btn">
                    <i class="fi fi-rs-box-alt"></i> Passer à la caisse
                </button>
            </form>

            <div id="error-message" style="color: red; margin-top: 10px;"></div>
      
        </div>
    </div>
</section>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const quantities = document.querySelectorAll(".quantity");
    const cartTotal = document.getElementById("cart-total");
    const totalPriceInput = document.getElementById("total-price-input");

    quantities.forEach(input => {
        input.addEventListener("input", function() {
            let row = this.closest("tr");
            let price = parseFloat(row.dataset.price);
            let quantity = parseInt(this.value);
            if (isNaN(quantity) || quantity < 1) quantity = 1;
            this.value = quantity;

            let subtotal = price * quantity;
            row.querySelector(".table__subtotal").textContent = subtotal.toLocaleString() + " DZD";

            updateTotal();
        });
    });

    function updateTotal() {
        let total = 0;
        document.querySelectorAll(".table__subtotal").forEach(subtotal => {
            total += parseFloat(subtotal.textContent.replace(/\D/g, ""));
        });
        cartTotal.textContent = total.toLocaleString() + " DZD";
        totalPriceInput.value = total;
    }
});
</script>



      <!--=============== NEWSLETTER ===============-->
      <section class="newsletter section home__newsletter">
    <div class="newsletter__container grid">
        <h3 class="newsletter__title flex">
            <img src="../assets/img/icon-email.svg" alt="" class="newsletter__icon">
            Inscrivez-vous à la newsletter
        </h3>

        <?php 
      if(!$userId){
        ?>
        <p class="newsletter__description" style="text-align:center" >
        Profitez d'une réduction exclusive en vous abonnant dès maintenant à notre newsletter.
            </p>
            <form action="login-register.php" method="POST" class="newsletter__form">
                <input type="text" placeholder="Enter your email" name="email" class="newsletter__input" />
                <button type="submit" name="newsletter" class="newsletter__btn">Abonnez</button>
            </form>
      <?php } else {
        $newsletter = getAll("newsletter");
        $is_subscribed = false;

      
        if (mysqli_num_rows($newsletter) > 0) {
            foreach ($newsletter as $news) {
                if ($userId == $news['user_id']) {
                    $is_subscribed = true;
                    break;
                }
            }
        }

        if (!$is_subscribed) { 
        ?>
            <p class="newsletter__description" style="text-align:center" >
            Profitez d'une réduction exclusive en vous abonnant dès maintenant à notre newsletter.
            </p>
            <form action="../adminto/adminDashboard/code.php" method="POST" class="newsletter__form">
    <input type="text" placeholder="Enter your email" name="email" class="newsletter__input" />
    <input type="hidden" value="<?= $userId ?>" name="user_id">
    <input type="hidden" value="<?= $_SERVER['REQUEST_URI'] ?>" name="redirect_to">
    <button type="submit" name="newsletter" class="newsletter__btn">Abonnez</button>
</form>

        <?php
        } else { 
        ?>
            <p class="newsletter__description" style="margin-left: 100px">
            Vous avez déjà été inscrit
            </p>
            <form action="../adminto/adminDashboard/code.php" method="POST" class="newsletter__form">
    <input type="hidden" value="<?= $userId ?>" name="user_id">
    <input type="hidden" value="<?= $_SERVER['REQUEST_URI'] ?>" name="redirect_to">
    <button type="submit" name="newsletter_unsigned" class="newsletter__btn" style="height:50px; width: 150px; margin-left:200px">
    Non signé
    </button>
</form>

        <?php 
        }
      }
        ?>
    </div>
</section>
    </main>

    <!--=============== FOOTER ===============-->
    <footer class="footer">
    <div class="footer grid">
      <div class="footer__content">
        <a href="index.php" class="footer__logo">
          <img src="../assets/img/coin_mobile_logo.jpg" alt="" class="footer__logo-img">
        </a>

        <p class="footer__description">
          <span>Adresse :</span> 88 rue Mezouar Abdelkader, Bir Khadem
        </p>

        <h4 class="footer__subtitle">Contact</h4>
        <p class="footer__description">
          <span>Téléphone :</span> +213 551239279 / +91 01 2345 6789
        </p>

        <p class="footer__description">
          <span>Temps de travail :</span> 10h00 - 18h00, Lun - Sam
        </p>

        <div class="footer__social">
          <h4 class="footer__subtitle">Suivez-moi</h4>

          <div class="footer__social-links flex">
            <a href="https://web.facebook.com/profile.php/?id=100071013924049&_rdc=1&_rdr#"><img src="../assets/img/icon-facebook.svg" alt="" 
              class="footer__social-icon"></a>
            <a href="https://www.instagram.com/linenshome.lingedemaison/"><img src="../assets/img/icon-instagram.svg" 
              class="footer__social-icon"></a>
          </div>
        </div>
      </div>

      <div class="footer__content">
        <h3 class="footer__title">Adresse</h3>
        <ul class="footer__links">
          <li><a href="" class="footer__link">À propos de nous</a></li>
          <li><a href="" class="footer__link">Informations de livraison</a></li>
          <li><a href="" class="footer__link">Politique de confidentialité</a></li>
          <li><a href="" class="footer__link">Conditions générales</a></li>
        </ul>
      </div>

      <div class="footer__content">
        <h3 class="footer__title">Mon compte</h3>
        <ul class="footer__links">
          <li> <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="../accounts.php" class="footer__link">Mon Compte</a>
                        <?php else: ?>
                            <a href="../login-register.php" class="footer__link">Mon Compte</a>
                        <?php endif; ?></li>
          <li><a href="../cart/cart.php" class="footer__link">Voir le panier</a></li>
          <li><a href="../wishlist/wishlist.php" class="footer__link">Ma liste de souhaits</a></li>
          <li><a href="../contact.php" class="footer__link">Aider</a></li>
        
        </ul>
      </div>
    </div>

    <div class="footer__bottom">
      <p class="copyright">&copy; 2025 Coin Mobile, Tous droits réservés</p>
      
    </div>
  </footer>

    <!--=============== SWIPER JS ===============-->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!--=============== SCROLL REVEAL ===============-->
    <script src="https://unpkg.com/scrollreveal"></script>

    <!--=============== MAIN JS ===============-->
    <script src="../assets/js/main.js"></script>
    <script src="assets/js.js"></script> 
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>

<?php  if(isset($_SESSION['message'])) { ?>
<script>
alertify.set('notifier','position', 'top-right');
alertify.success('<?= $_SESSION['message']; ?>');

<?php  
unset($_SESSION['message']);
} ?>
</script>
    <script>
        document.getElementById('checkout-form').addEventListener('submit', function(event) {
    var cart = <?= json_encode($_SESSION['cart']) ?>;
    var errorMessage = document.getElementById('error-message');
    
    if (!cart || Object.keys(cart).length === 0) {
        event.preventDefault();         errorMessage.textContent = 'Vous devez dabord remplir votre panier';
    }
});
    </script>

  </body>

  <style>
select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background: url('path/to/custom-arrow.svg') no-repeat right center;
  background-size: 12px; /* Size of the custom arrow */
  padding-right: 30px; /* Adjust based on the size of your custom arrow */
}

  </style>
</html>
