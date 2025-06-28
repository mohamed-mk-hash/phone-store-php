<?php
session_start();
include("../../middleware/adminMiddlware.php");




if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header("Location: cart.php");
    exit();
}


$cartData = isset($_POST['cart_data']) ? json_decode($_POST['cart_data'], true) : [];

if (isset($_SESSION['cart'])) {
  $cartData = $_SESSION['cart'];
} else {
  $cartData = [];  
}

$totalPrice = 0;
foreach ($cartData as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />

<script type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js">
</script>

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
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
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
      <section class="breadcrumb">
        <ul class="breadcrumb__list container">
         <li><a href="index.php" class="breadcrumb__link">Home</a></li>
         <li><span class="breadcrumb__link">></span></li>
         <li><span class="breadcrumb__link">Cart</span></li>
         <li><span class="breadcrumb__link">></span></li>
         <li><span class="breadcrumb__link">Checkout</span></li>
        </ul>
      </section>

      <!--=============== CHECKOUT ===============-->
      <section class="checkout section--lg">
    <div class="checkout__container grid">
        <div class="checkout__group">
            <h3 class="section__title">Détails de Facturation</h3>
            <form action="command.php" method="POST" class="form grid" id="checkoutForm">
                <input type="text" name="name" placeholder="Nom" class="form__input" required>
                <input type="text" name="address" placeholder="Adresse" class="form__input" required>
                <select id="city-select" name="city" class="form__input" required>
                    <option value="" disabled selected>Sélectionner la Ville de Livraison</option>
                    <?php
                    $shippingPlaces = getAll('shipping');
                    if (mysqli_num_rows($shippingPlaces) > 0) {
                        foreach ($shippingPlaces as $place) {
                            ?>
                            <option value="<?= $place['id']; ?>" data-price="<?= $place['price']; ?>">
                                <?= $place['place']; ?>
                            </option>
                            <?php
                        }
                    } else {
                        echo "<option value=''>Aucune ville disponible</option>";
                    }
                    ?>
                </select>
                <input type="text" name="phone" placeholder="Téléphone" class="form__input" required>
                <input type="email" name="email" placeholder="Email" class="form__input" required>

                <h3 class="checkout__title">Informations supplémentaires</h3>
                <textarea name="order_note" placeholder="Note de commande" cols="30" rows="10" class="form__input textarea"></textarea>

            
                <input type="hidden" name="cart_data" value="<?= isset($_SESSION['cart']) ? htmlspecialchars(json_encode($_SESSION['cart'])) : '' ?>">
                <input type="hidden" name="user_name" id="userName">
    <input type="hidden" name="user_email" id="userEmail">

                <input type="hidden" name="payment_method" id="paymentMethodInput" value="">
                <input type="hidden" name="payment_page" id="paymentPage" value="">

                <button type="submit" name="place_order_btn" class="btn flex btn--md" style="width: 205px;" id="placeOrderBtn">Passer la commande</button>
                <div id="paymentErrorMessage" style="color: red; margin-top: 10px; display: none;"></div>
            </form>
        </div>

        <div class="checkout__group">
            <h3 class="section__title">Totaux du Panier</h3>
            <table class="order__table">
                <tr>
                    <th colspan="2">Produits</th>
                    <th>Total</th>
                </tr>

                <?php if (!empty($cartData)): ?>
                    <?php foreach ($cartData as $item): ?>
                        <tr>
                            <td><img src="../<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="order__img"></td>
                            <td>
                                <h3 class="table__title"><?= htmlspecialchars($item['name']) ?></h3>
                                <p class="table__quantity">X <?= htmlspecialchars($item['quantity']) ?></p>
                            </td>
                            <td><span class="table__price"><?= number_format($item['price'] * $item['quantity'], 2) ?> DZD</span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <tr>
                    <td><span class="order__subtitle">Sous-total</span></td>
                    <td colspan="2"><span class="table__price" id="cart-total"><?= $totalPrice ?> DZD</span></td>
                </tr>
                <tr>
                    <td><span class="order__subtitle">livraison</span></td>
                    <td colspan="2"><span class="table__price" id="shipping-price">Choisissez un lieu d'livraison</span></td>
                </tr>
                <tr>
                    <td><span class="order__subtitle">Total</span></td>
                    <td colspan="2"><span class="order__grand-total" id="final-total"></span></td>
                </tr>
            </table>

            <div class="payment">
                <div class="dropdown">
                    <form class="form grid">
                        <select id="dropdown" name="dropdown" class="dropdown-select" onchange="changePaymentMethod()">
                            <option value="" disabled selected>Choisissez le mode de paiement</option>
                            <option value="card">Card</option>
                            <option value="cash">Espèces (Paiement à la livraison)</option>
                        </select>
                    </form>
                </div>

                <div class="pamentainer" id="edahabiaForm" style="display: none;">
                    <div style="text-align: center; color: green; font-weight: bold; margin-top: 20px;">
                        Lorsque vous cliquez sur "Passer la commande", vous serez redirigé vers une page pour remplir les informations de votre card et finaliser le paiement.
                    </div>
                </div>

                <div class="pamentainer" id="cashForm" style="display: none;">
                    <div style="text-align: center; color: green; font-weight: bold; margin-top: 20px;">
                        Vous avez sélectionné le paiement en espèces à la livraison. Votre commande sera traitée une fois que vous confirmerez le paiement à la livraison.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>






      <!--=============== NEWSLETTER ===============-->
      <section class="newsletter section home__newsletter">
    <div class="newsletter__container grid">
        <h3 class="newsletter__title flex">
            <img src="../assets/img/icon-email.svg" alt="" class="newsletter__icon">
            Sign up to NewsLetter
        </h3>

        <?php 
      if(!$userId){
        ?>
        <p class="newsletter__description" style="text-align:center" >
                Subscribe and get a message in your email every time a new product is added
            </p>
            <form action="login-register.php" method="POST" class="newsletter__form">
                <input type="text" placeholder="Enter your email" name="email" class="newsletter__input" />
                <button type="submit" name="newsletter" class="newsletter__btn">Subscribe</button>
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
    <button type="submit" name="newsletter" class="newsletter__btn">Subscribe</button>
</form>

        <?php
        } else { 
        ?>
            <p class="newsletter__description" style="margin-left: 100px">
            Vous êtes déjà inscrit
            </p>
            <form action="../adminto/adminDashboard/code.php" method="POST" class="newsletter__form">
    <input type="hidden" value="<?= $userId ?>" name="user_id">
    <input type="hidden" value="<?= $_SERVER['REQUEST_URI'] ?>" name="redirect_to">
    <button type="submit" name="newsletter_unsigned" class="newsletter__btn" style="height:50px; width: 150px; margin-left:200px">
        Unsigned
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
  
   <!--=============== MAIN JS ===============-->
   <script src="assets/js/main.js"></script>
   <script src="assets/ShippingPlace.js"></script>
   <script src="../assets/js/cartpayment.js"></script>




    <script>
    function changePaymentMethod() {
        const paymentMethod = document.getElementById('dropdown').value;
        const paymentMethodInput = document.getElementById('paymentMethodInput');
        const edahabiaForm = document.getElementById('edahabiaForm');
        const cashForm = document.getElementById('cashForm');

        paymentMethodInput.value = paymentMethod;

        if (paymentMethod === 'card') {
            edahabiaForm.style.display = 'block';
            cashForm.style.display = 'none';
        } else if (paymentMethod === 'cash') {
            edahabiaForm.style.display = 'none';
            cashForm.style.display = 'block';
        }
    }
    </script>


<script>
document.getElementById('checkoutForm').addEventListener('submit', function(event) {
    const paymentMethod = document.getElementById('paymentMethodInput').value;
    const errorMessage = document.getElementById('paymentErrorMessage');

    if (!paymentMethod) {
        event.preventDefault(); 
        errorMessage.textContent = 'Veuillez choisir un mode de paiement avant de passer la commande.';
        errorMessage.style.display = 'block';
    }
});
</script>


<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<?php  if(isset($_SESSION['message'])) { ?>
<script>
   alertify.set('notifier','position', 'top-right');
   alertify.success('<?= $_SESSION['message']; ?>');

   <?php  
  unset($_SESSION['message']);
  } ?>
</script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    
 
    <script type="text/javascript">
        (function(){
            emailjs.init("Kl4b5y2jMs1k4qS8w"); 
        })();
    </script>
   
  </body>
  <style>
select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background: url('path/to/custom-arrow.svg') no-repeat right center;
  background-size: 12px; 
  padding-right: 30px; 
}

  </style>
</html>
