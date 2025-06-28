<?php 
session_start(); 

include("../middleware/adminMiddlware.php");
$message = isset($_SESSION['login_message']) ? $_SESSION['login_message'] : '';
$register_message = isset($_SESSION['register_message']) ? $_SESSION['register_message'] : '';
unset($_SESSION['login_message'], $_SESSION['register_message']);

$cart_count = 0;

if (isset($_SESSION['cart'])) {
    $cart_count = count($_SESSION['cart']); 
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

if (isset($_SESSION['username'])) {

  header("Location: accounts.php");
  exit();
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
    <link rel="stylesheet" href="assets/css/stylee.css" />
    <title>Ecommerce Website</title>
  </head>
  <body>
    <!--=============== HEADER ===============-->
    <header class="header">
    <nav class="nav container">
        <a href="index.php" class="nav__logo">
            <img src="assets/img/coin_mobile_logo.jpg" alt="" class="nav__logo-img">
        </a>
        <div class="nav__menu" id="nav-menu">
            <div class="nav__menu-top">
                <a href="index.php" class="nav__menu-logo">
                    <img src="assets/img/coin_mobile_logo.jpg" alt="">
                </a>

                <div class="nav__close" id="nav-close">
                    <i class="fi fi-rs-cross-small"></i>
                </div>
            </div>
            <ul class="nav__list">
                <li class="nav__item">
                    <a href="index.php" class="nav__link ">Accueil</a>
                </li>

                <li class="nav__item">
                    <a href="shop.php" class="nav__link">Boutique</a>
                </li>

                <li class="nav__item">
                    <a href="Reparation.php" class="nav__link">Réparation</a>
                </li>

                <li class="nav__item">
                    <!-- Check if the user is logged in -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="accounts.php" class="nav__link active-link">Mon Compte</a>
                    <?php else: ?>
                        <a href="login-register.php" class="nav__link active-link">Mon Compte</a>
                    <?php endif; ?>
                </li>

                <li class="nav__item">
                    <a href="contact.php" class="nav__link">Contact</a>
                </li>
            </ul>

           
        </div>

        <div class="header__user-action">
        <a href="wishlist/wishlist.php" class="header__action-btn">
    <img src="assets/img/icon-heart.svg" alt="">
    <span class="count"><?= $wishlistCount ?></span> <!-- Displaying the wishlist count -->
</a>
            <a href="cart/cart.php" class="header__action-btn">
    <img src="assets/img/icon-cart.svg" alt="Cart Icon">
    <span class="count"><?= $cart_count ?></span>  <!-- Display the total count here -->
</a>

            <div class="header__action-btn nav__toggle" id="nav-toggle">
                <img src="assets/img/menu-burger.svg" alt="">
            </div>
        </div>
    </nav>
</header>

    <!--=============== MAIN ===============-->
    <main class="main">
      <!--=============== BREADCRUMB ===============-->
    
      <!--=============== LOGIN-REGISTER ===============-->
      <section class="login-register section--lg">
        <div class="login-register__container grid">
          <div class="login">
            <h3 class="section__title">Se connecter</h3>

            <form class="form grid" method="POST" action="usselogSign.php">
              <input name="email" type="email" placeholder="Your Email" class="form__input"/>
              <input name="password" type="password" placeholder="Your Password" class="form__input"/>
              <?php if (!empty($message)): ?>
    <div class="error-message" style="color: red; margin: 10px 0;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
              <div class="form__btn">
                <button type="submit" class="btn" name="log">Se connecter</button>
              </div>
            </form>
          </div>

          <div class="register">
            <h3 class="section__title">Créer un compte</h3>

            <form class="form grid" method="POST" action="usselogSign.php">
    <input name="username" required type="text" placeholder="Username" class="form__input"/>
    <input name="email" required type="email" placeholder="Your Email" class="form__input"/>
    <input name="password" required type="password" placeholder="Your Password" class="form__input"/>
    <input name="cpasswrd" required type="password" placeholder="Confirm Password" class="form__input"/>
    
    <?php if (!empty($register_message)): ?>
    <div class="error-message" style="color: red; margin: 10px 0;">
        <?php echo $register_message; ?>
    </div>
<?php endif; ?>

    <div class="form__btn">
        <button type="submit" class="btn" name="sign">Créer un compte</button>
    </div>
</form>
          </div>
        </div>
      </section>

      <!--=============== NEWSLETTER ===============-->
      <section class="newsletter section home__newsletter">
    <div class="newsletter__container grid">
        <h3 class="newsletter__title flex">
            <img src="assets/img/icon-email.svg" alt="" class="newsletter__icon">
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
          <img src="assets/img/coin_mobile_logo.jpg" alt="" class="footer__logo-img">
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
            <a href="https://web.facebook.com/profile.php/?id=100071013924049&_rdc=1&_rdr#"><img src="assets/img/icon-facebook.svg" alt="" 
              class="footer__social-icon"></a>
            <a href="https://www.instagram.com/linenshome.lingedemaison/"><img src="assets/img/icon-instagram.svg" 
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
                            <a href="accounts.php" class="footer__link">Mon Compte</a>
                        <?php else: ?>
                            <a href="login-register.php" class="footer__link">Mon Compte</a>
                        <?php endif; ?></li>
          <li><a href="cart/cart.php" class="footer__link">Voir le panier</a></li>
          <li><a href="wishlist/wishlist.php" class="footer__link">Ma liste de souhaits</a></li>
          <li><a href="contact.php" class="footer__link">Aider</a></li>
        
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
  </body>
</html>
