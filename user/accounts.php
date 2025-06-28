<?php
session_start();
include("../adminto/main/config/functions/myfunctions.php");

$cart_count = 0;

if (isset($_SESSION['cart'])) {
  $cart_count = count($_SESSION['cart']);
}

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
  $username = $_SESSION['username'];
} else {

  header("Location: login-register.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT 
        c.created_at, 
        c.Acive, 
        c.city,
        c.address,
        SUM(c.total_price) AS total_price, 
        MAX(s.price) AS shipping_price 
    FROM 
        command c
    JOIN 
        shipping s 
    ON 
        c.city = s.place
    WHERE 
        c.user_id = ? 
        AND c.status = 'paye' 
    GROUP BY 
        c.created_at 
    ORDER BY 
        c.created_at DESC";


$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $row['total_price'] += $row['shipping_price'];
        $rows[] = $row;
    }
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
  <link rel="stylesheet" href="assets/css/swiper.css" />

  <!--=============== CSS ===============-->
  <link rel="stylesheet" href="assets/css/stylee.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />



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

            <?php if (isset($user_id)): ?>
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


    <!--=============== ACCOUNTS ===============-->
    <section class="accounts__container section--lg">
      <div class="account__tabs">
        <p class="account__tab active-tab" data-target="#dashboard">
          <i class="fi fi-rs-settings-sliders"></i> Tableau de bord
        </p>

        <p class="account__tab" data-target="#orders">
          <i class="fi fi-rs-shopping-bag"></i> Commandes
        </p>

        <p class="account__tab" data-target="#Update__Profile">
          <i class="fi fi-rs-user"></i> Mettre à jour le nom 
        </p>

        <p class="account__tab" data-target="#Update__Password">
          <i class="fi fi-rs-user"></i> Mettre à jour le mot de passe
        </p>

        <p class="account__tab" data-target="#address">
          <i class="fi fi-rs-marker"></i> Mon adresse
        </p>

        <p class="account__tab">
          <a href="logout.php" style="color: black;" >
            <i class="fi fi-rs-exit"></i>   Déconnexion
          </a>
        </p>

      </div>

      <div class="tabs__content">
        <div class="tab__content active-tab" content id="dashboard">
          <h3 class="tab__header">Bonjour <?php echo htmlspecialchars($username); ?></h3>

          <div class="tab__body">
            <p class="tab__description">
              Depuis votre tableau de bord, vous pouvez facilement consulter et visualiser vos
              commandes récentes, gérer vos adresses de livraison et de facturation,
              et modifier votre mot de passe ainsi que les détails de votre compte.
            </p>
          </div>
        </div>

        <div class="tab__content" content id="orders">
          <h3 class="tab__header">Vos Commandes</h3>

          <div class="tab__body">
    <table class="placed__order-table">
        <tr>
            <th>Date</th>
            <th>Statut</th>
            <th>Total</th>
            <th>Facture</th>
        </tr>

        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= date("j F, Y H:i:s", strtotime($row['created_at'])) ?></td>
                    <td><?= ($row['Acive'] == 1) ? "Terminée" : "En cours" ?></td>
                    <td><?= number_format($row['total_price'], 2) ?> DZD</td>
                    <td>
                        <a href="generate_invoice.php?created_at=<?= urlencode($row['created_at']) ?>" class="btn btn-primary">
                            Voir les détails
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center;">Aucune commande trouvée.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>


        </div>

        <div class="tab__content" content id="Update__Profile">
          <h3 class="tab__header">Mettre à jour le nom </h3>

          <div class="tab__body">
            <form action="usselogSign.php" class="form grid" method="POST">
              <input type="text" name="username" placeholder="le nouveua Nom d'utilisateur" class="form__input">
              <div class="form__btn">
                <button name="cUsername" class="btn btn--md">Enregistrer</button>
              </div>
            </form>
          </div>
        </div>

        <div class="tab__content" content id="Update__Password">
          <h3 class="tab__header">Mettre à jour le mot de passe</h3>

          <div class="tab__body">
            <form action="usselogSign.php" class="form grid" method="POST">
              <input type="password" name="current_password" placeholder="Mot de passe actuel" class="form__input">
              <input type="password" name="new_password" placeholder="Le nouveau mot de passe" class="form__input">
             
              <div class="form__btn">
                <button name="cPassword" class="btn btn--md">Enregistrer</button>
              </div>
            </form>

          </div>
        </div>

        <div class="tab__content" content id="address">
          <h3 class="tab__header">Adresse de livraison</h3>

          <div class="tab__body">
            <?php if (!empty($rows)): ?>
              <?php
              $latest_row = $rows[0];
              foreach ($rows as $row) {
                if ($row['created_at'] > $latest_row['created_at']) {
                  $latest_row = $row;
                }
              }
              ?>
              <address class="address">
                <?= $latest_row['address'] ?>
              </address>
              <p class="city"><?= $latest_row['city'] ?></p>
            <?php else: ?>
              <tr>
                <td colspan="5" style="text-align: center;">Passez une commande d'abord</td>
              </tr>
            <?php endif; ?>
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
        if (!$userId) {
          ?>
          <p class="newsletter__description" style="text-align:center">
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
            <p class="newsletter__description" style="text-align:center">
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
              <button type="submit" name="newsletter_unsigned" class="newsletter__btn"
                style="height:50px; width: 150px; margin-left:200px">
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
            <a href="https://web.facebook.com/profile.php/?id=100071013924049&_rdc=1&_rdr#"><img
                src="assets/img/icon-facebook.svg" alt="" class="footer__social-icon"></a>
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
            <?php endif; ?>
          </li>
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

  <?php if (isset($_SESSION['message'])) { ?>
    <script>
      alertify.set('notifier', 'position', 'top-right');
      alertify.success('<?= $_SESSION['message']; ?>');

      <?php
      unset($_SESSION['message']);
  } ?>
  </script>
</body>

</html>