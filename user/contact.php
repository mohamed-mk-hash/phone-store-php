<?php

include("../middleware/adminMiddlware.php");

session_start();





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
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
     <!--=============== CSS ===============-->
     <link rel="stylesheet" href="assets/css/stylee.css" />
 <style>

.inputBox input[type="submit"].loading {
    font-size: 14px; 
}

 </style>

    <title>Ecommerce Website</title>
  </head>
  <body>
    <!--=============== HEADER ===============-->
    <header class="header">
        <nav class="nav">
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
                        <a href="Reparation.php" class="nav__link ">Réparation</a>
                    </li>

                    <li class="nav__item">
                        <!-- Vérifiez si l'utilisateur est connecté -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="accounts.php" class="nav__link">Mon Compte</a>
                        <?php else: ?>
                            <a href="login-register.php" class="nav__link">Mon Compte</a>
                        <?php endif; ?>
                    </li>

                    <li class="nav__item">
                        <a href="contact.php" class="nav__link active-link">Contact</a>
                    </li>
                </ul>
            </div>

            <div class="header__user-action">
                <a href="wishlist/wishlist.php" class="header__action-btn">
                    <img src="assets/img/icon-heart.svg" alt="">
                    <span class="count"><?= $wishlistCount ?></span> <!-- Affichage du nombre de souhaits -->
                </a>

                <a href="cart/cart.php" class="header__action-btn">
                    <img src="assets/img/icon-cart.svg" alt="Icône Panier">
                    <span class="count"><?= $cart_count ?></span> <!-- Affichage du total ici -->
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
    
      <!--=============== contact ===============-->
      <section class="contact">
          <div class="bkcolor">
            <div class="contactUs">
            <div class="title">
              <h2>Contactez-nous</h2>
              </div>
              <div class="box">
                <div class="contact form">
                <form id="contactForm" method="POST">
  <div class="formBox">
    <h3>Envoyer un message</h3>
    <div class="row50">
      <input type="hidden" name="access_key" value="04145e6d-ce8d-4885-80cd-516587ed4bee">
      <div class="inputBox">
        <span>Prénom</span>
        <input type="text" placeholder="Ex:Jean" name="firstName" required>
      </div>
      <div class="inputBox">
        <span>Nom</span>
        <input type="text" placeholder="Ex:Dupont" name="lastName" required>
      </div>
    </div>
    <div class="row50">
      <div class="inputBox">
        <span>Email</span>
        <input type="email" placeholder="Ex:JeanDupont@email.com" name="email" required>
      </div>
      <div class="inputBox">
        <span>Téléphone</span>
        <input type="text" placeholder="Ex:0551239279" name="number" required>
      </div>
    </div>
    <div class="row100">
      <div class="inputBox">
        <span>Message</span>
        <textarea placeholder="Écrivez votre message ici" name="message" required></textarea>
      </div>
    </div>
    <div class="row100">
      <div class="inputBox">
        <input type="submit" value="Envoyer">
      </div>
    </div>
  </div>
</form>

<div id="formResponse"></div>

<script>
  document.getElementById("contactForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector("input[type='submit']");

    submitButton.value = "Envoi en cours...";
    submitButton.classList.add("loading"); 
    submitButton.disabled = true;

    try {
        const response = await fetch("send_email_contact.php", {
            method: "POST",
            body: formData,
        });

        const result = await response.json();

        if (result.success) {
            document.getElementById("formResponse").innerText = "Votre message a été envoyé avec succès!";
            form.reset();
        } else {
            document.getElementById("formResponse").innerText = "Erreur: " + result.message;
        }
    } catch (error) {
        document.getElementById("formResponse").innerText = "Une erreur réseau s'est produite.";
    }

    submitButton.value = "Envoyer";
    submitButton.classList.remove("loading"); 
    submitButton.disabled = false;
});

</script>


                </div>
                <div class="contact info">
                <h3>Informations de contact</h3>
                <div class="infoBox">
                  <div>
                    <span><ion-icon name="location"></ion-icon></span>
                    <p>Rue Arab Si Ahmed N° 03 Birkhadem<br>ALGER</p>
                  </div>
                  <div>
                    <span><ion-icon name="mail"></ion-icon></span>
                    <a href="mailto:CoinMobile@gmail.com">CoinMobile@gmail.com</a>
                  </div>
                  <div>
                    <span><ion-icon name="call"></ion-icon></span>
                    <a href="tel:+0549939880">+0779 58 52 70</a>
                  </div>
                  <!-- Liens réseaux sociaux -->
                  <ul class="sci">
                    <li><a href="https://web.facebook.com/profile.php/?id=100071013924049&_rdc=1&_rdr#" class="icon"><ion-icon name="logo-facebook"></ion-icon></a></li>
                    <li><a href="https://www.instagram.com/linenshome.lingedemaison/" class="icon"><ion-icon name="logo-instagram"></ion-icon></a></li>
                  </ul>
                </div>
                </div>
                <div class="contact map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d716.2803671150748!2d3.0504185304100995!3d36.715953098266986!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x128fad00247719b5%3A0x50437ae171d7036e!2sFatah%20oppo!5e1!3m2!1sar!2sdz!4v1740256532552!5m2!1sar!2sdz" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
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
  <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>

<?php  if(isset($_SESSION['message'])) { ?>
<script>
   alertify.set('notifier','position', 'top-right');
   alertify.success('<?= $_SESSION['message']; ?>').set({
        backgroundColor: 'black',      
           color: 'white',   
            });

   <?php  
  unset($_SESSION['message']);
  } ?>
</script>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    

  </body>
</html>
