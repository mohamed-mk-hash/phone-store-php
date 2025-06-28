<?php

include("../adminto/main/config/functions/myfunctions.php");


if (session_status() == PHP_SESSION_NONE) {
  session_start();
}


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


$promoyes = "
SELECT promotions.id AS promo_id, promotions.name AS promo_name, promotions.percent AS percent_promo, promotions.date_fin, 
       products.id AS product_id, products.name AS product_name, 
       COALESCE(
           (SELECT MAX(price) FROM ram_rom WHERE ram_rom.product_id = products.id), 
           products.price
       ) AS price,
       (SELECT image FROM product_images WHERE product_id = products.id LIMIT 1) AS image
FROM product_promo 
INNER JOIN promotions ON product_promo.promo_id = promotions.id 
INNER JOIN products ON product_promo.product_id = products.id 
ORDER BY promotions.id
";



$promos = mysqli_query($con, $promoyes);

$promoData = [];
while ($row = mysqli_fetch_assoc($promos)) {
  $promoData[$row['promo_id']]['name'] = $row['promo_name'];
  $promoData[$row['promo_id']]['percent_promo'] = $row['percent_promo']; 
  $promoData[$row['promo_id']]['date_fin'] = $row['date_fin']; 
  $promoData[$row['promo_id']]['products'][] = $row;
  
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!--=============== FLATICON ===============-->
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.5.1/uicons-regular-straight/css/uicons-regular-straight.css'>

    <!--=============== SWIPER CSS ===============-->
    <link
  rel="stylesheet"
  href="assets/css/swiper.css"
     />

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/herostyle.css">
 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
@media screen and (max-width: 768px) {
    .home-swiper .swiper-button-next,
    .home-swiper .swiper-button-prev {
        top: 20% ; 
    }
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
                        <a href="index.php" class="nav__link active-link">Accueil</a>
                    </li>

                    <li class="nav__item">
                        <a href="shop.php" class="nav__link">Boutique</a>
                    </li>

                    <li class="nav__item">
                        <a href="Reparation.php" class="nav__link">Réparation</a>
                    </li>

                    <li class="nav__item">
                     
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="accounts.php" class="nav__link">Mon Compte</a>
                        <?php else: ?>
                            <a href="login-register.php" class="nav__link">Mon Compte</a>
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
                    <span class="count"><?= $wishlistCount ?></span>
                </a>

                <a href="cart/cart.php" class="header__action-btn">
                    <img src="assets/img/icon-cart.svg" alt="Icône Panier">
                    <span class="count"><?= $cart_count ?></span>
                </a>

                <div class="header__action-btn nav__toggle" id="nav-toggle">
                    <img src="assets/img/menu-burger.svg" alt="">
                </div>
            </div>
        </nav>
    </header>

      
    <!--=============== MAIN ===============-->
    <div class="main">
      <!--=============== HOME ===============-->
      
      <section class="hero-section">
            <div class="section-content">
                <div class="hero-details">
                <h2 class="title">Meilleurs Smartphones</h2>
<h3 class="subtitle">Équipez-vous avec les dernières technologies mobiles !</h3>
<p class="description">Découvrez notre sélection de smartphones et accessoires de qualité pour une expérience connectée unique.</p>
                    <div class="buttons">
                        <a href="shop.php"><div class="button order-now">Boutique</div></a>
                        <a href="contact.php"><div class="button contact-us">Nous Contacter</div></a>
                    </div>
                </div>
                <div class="hero-image-wrapper">
                    <img src="assets/img/hero_img.jpg" alt="Hero" class="hero-image" style="width: 400px; " >
                </div>
            </div>
        </section>

      <!--=============== CATEGORIES ===============-->
      <section class="categories section">
    <h3 class="section__title"><span>Phone</span> Categories</h3>
    <div class="categories__container swiper">
        <div class="swiper-wrapper">
            <?php 
              $categories = getcategory("phone");
            if(mysqli_num_rows($categories) > 0) {
                foreach($categories as $category) { ?>
                   <a href="shop.php?category_id=<?= $category['id'] ?>" class="category__item swiper-slide">
                <img src="../uploads/images/category/<?= $category['image'] ?>" alt="" class="category__img">
             <h3 class="category__title"><?= $category['name'] ?></h3> 
</a>

                <?php }
            } else {
                echo "<p>No categories available.</p>";
            }
            ?>
        </div>
        <div class="swiper-button-next"><i class="fi fi-rs-angle-small-right"></i></div>
        <div class="swiper-button-prev"><i class="fi fi-rs-angle-small-left"></i></div>
    </div>
</section>


      <!--=============== PRODUCTS ===============-->

      <section class="showcase section">
    <h3 class="section__title_showcase"><span>Notre</span> Promotion</h3>
    <div class="showcase__container container grid">
        <?php 
        $currentDate = date('Y-m-d'); 
        
        foreach ($promoData as $promo): 
            if ($promo['date_fin'] < $currentDate) continue; 
        ?>
            <div class="showcase__wrapper">
                <h3 class="section__title"><?= $promo['name'] ?> Promotion/<?= $promo['percent_promo'] ?>%</h3>
                
                <?php 
                $totalProducts = count($promo['products']);
                $shownProducts = array_slice($promo['products'], 0, 3); 
                $hiddenProducts = array_slice($promo['products'], 3);
                ?>
                
                <?php foreach ($shownProducts as $product): ?>
                    <div class="showcase__item">
                        <a href="details.php?id=<?= $product['product_id'] ?>" class="showcase__img-box">
                            <img src="../uploads/images/product/<?= $product['image'] ?>" alt="" class="showcase__img">
                        </a>
                        <div class="showcase__content">
                            <a href="details.php?id=<?= $product['product_id'] ?>" class="showcase__title">
                                <h4 class="showcase__title"><?= wordwrap($product['product_name'], 20, "<br>") ?></h4>
                            </a>
                            <div class="showcase__price flex">
                                <span class="new__price"><?= number_format($product['price'] * (1 - $promo['percent_promo'] / 100)) ?> DZD</span>
                                <span class="old__price"><?= number_format($product['price']) ?> DZD</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="hidden-products" style="display: none;">
                    <?php foreach ($hiddenProducts as $product): ?>
                        <div class="showcase__item">
                            <a href="details.php?id=<?= $product['product_id'] ?>" class="showcase__img-box">
                                <img src="../uploads/images/product/<?= $product['image'] ?>" alt="" class="showcase__img">
                            </a>
                            <div class="showcase__content">
                                <a href="details.php?id=<?= $product['product_id'] ?>" class="showcase__title">
                                    <h4 class="showcase__title"><?= wordwrap($product['product_name'], 20, "<br>") ?></h4>
                                </a>
                                <div class="showcase__price flex">
                                    <span class="new__price"><?= number_format($product['price'] * (1 - $promo['percent_promo'] / 100)) ?> DZD</span>
                                    <span class="old__price"><?= number_format($product['price']) ?> DZD</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalProducts > 3): ?>
                    <button class="showcase__btn" onclick="showMoreProducts(this)">Plus des produits</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
    function showMoreProducts(button) {
        let hiddenProducts = button.previousElementSibling;
        if (hiddenProducts.style.display === "none") {
            hiddenProducts.style.display = "block";
            button.textContent = "Voir moins";
        } else {
            hiddenProducts.style.display = "none";
            button.textContent = "Plus des produits";
        }
    }
</script>


     
     <section class="categories section">
    <h3 class="section__title"><span>Accesoir</span> Categories</h3>
    <div class="accesoir__container swiper">
        <div class="swiper-wrapper">
            <?php 
              $categories = getcategory("accessoire");
            if(mysqli_num_rows($categories) > 0) {
                foreach($categories as $category) { ?>
                   <a href="shop.php?category_id=<?= $category['id'] ?>" class="category__item swiper-slide">
                <img src="../uploads/images/category/<?= $category['image'] ?>" alt="" class="category__img">
             <h3 class="category__title"><?= $category['name'] ?></h3> 
</a>

                <?php }
            } else {
                echo "<p>No categories available.</p>";
            }
            ?>
        </div>
        <div class="swiper-button-next"><i class="fi fi-rs-angle-small-right"></i></div>
        <div class="swiper-button-prev"><i class="fi fi-rs-angle-small-left"></i></div>
    </div>
</section>

      <section class="About" >
      <div class="containabout">
        <div class="content-about">
          <div class="about_title">
            <h1> <span>Service</span> Rèparation</h1>
          </div>
          <div class="about-content">
            <h3>Réparations de téléphones rapides et fiables</h3>
            <p>Nous comprenons l'importance de votre téléphone dans votre vie quotidienne. C'est pourquoi 
              nos techniciens experts proposent des services de réparation rapides et professionnels pour 
              toutes les grandes marques. Qu'il s'agisse d'un écran fissuré, d'un remplacement de batterie ou 
              d'un problème logiciel, nous sommes là pour vous. Profitez de prix abordables, de pièces de qualité 
              et d'un service auquel vous pouvez faire confiance.  </p>
              <div class="about_btn">
              <a href="Reparation.php"><button type="button" class="btn btn--sm">plus d'Informations</button> </a> 
              </div>
          </div>
        </div>
        <div class="image-section">
          <img src="assets/img/servie_reparation.jpg" alt="">
        </div>
      </div>
      </section>
      <!--=============== SHOWCASE ===============-->
   

      <!--=============== NEWSLETTER ===============-->
      <section class="newsletter section home__newsletter">
    <div class="newsletter__container grid">
        <h3 class="newsletter__title flex"  >
            <img src="assets/img/icon-email.svg" alt="" class="newsletter__icon">
            Inscrivez-vous à la newsletter
        </h3>

        <?php 
      if(!$userId){
        ?>
        <p class="newsletter__description">
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
    désabonnez
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
    <script src="assets/js/swiper.js"></script>
     <script src="assets/js/main.js" ></script>
     <script src="assets/js/phone_category.js" ></script>
     <script src="assets/js/main2.js" ></script>
    <!--=============== MAIN JS ===============-->

  

 
  

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
  var swiper = new Swiper('.home-swiper', {
    loop: true, 
    autoplay: {
      delay: 3000, 
      disableOnInteraction: false, 
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });
</script>

</script>





  </body>
</html>
