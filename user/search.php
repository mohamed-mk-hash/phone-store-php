<?php

include("../adminto/main/config/functions/myfunctions.php");

if (isset($_GET['category_id'])) {
        $category_id = intval($_GET['category_id']);
  
        $products = getProductsByCategory($category_id);   
        $category_query = "SELECT name FROM categories WHERE id = $category_id";
    $category_result = mysqli_query($con, $category_query);
    if ($category_result && mysqli_num_rows($category_result) > 0) {
      $category = mysqli_fetch_assoc($category_result);
      $category_name = htmlspecialchars($category['name']);
    } else {
      $category_name = "Unknown";     }
  } else {
        $products = getAllProducts();
  
    $category_name = "All Products";   }
  
  $cart_count = 0;
  
  if (isset($_SESSION['cart'])) {
    $cart_count = count($_SESSION['cart']);   }
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
  
  
  $category_filter = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;
  
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

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

  <title>Ecommerce Website</title>
</head>
<body>
    
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
            <a href="index.php" class="nav__link">Accueil</a>
          </li>

          <li class="nav__item">
            <a href="shop.php" class="nav__link">Boutique</a>
          </li>
          
          <li class="nav__item">
            <a href="Reparation.php" class="nav__link">Rèparation</a>
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

        <div class="header__search">
        <form action="search.php" method="get">
            <input 
                type="text" 
                name="search" 
                placeholder="Search for items..." 
                class="form__input" 
                value="<?php echo htmlspecialchars($search_term); ?>" 
            />
            <button type="submit" class="search__btn">
                <img src="assets/img/search.png" alt="">
            </button>
        </form>
    </div>

      </div>

      <div class="header__user-action">
        <a href="wishlist/wishlist.php" class="header__action-btn">
          <img src="assets/img/icon-heart.svg" alt="">
          <span class="count"><?= $wishlistCount ?></span> 
        </a>
        <a href="cart/cart.php" class="header__action-btn">
          <img src="assets/img/icon-cart.svg" alt="Cart Icon">
          <span class="count"><?= $cart_count ?></span> 
        </a>

        <div class="header__action-btn nav__toggle" id="nav-toggle">
          <img src="assets/img/menu-burger.svg" alt="">
        </div>
      </div>
    </nav>
  </header>


   <!--=============== MAIN ===============-->
   <main class="main">

   <section class="breadcrumb">
      <ul class="breadcrumb__list ">
        <li><a href="index.php" class="breadcrumb__link">Home</a></li>
        <li><span class="breadcrumb__link">></span></li>
        <li><span class="breadcrumb__link">Shop</span></li>
        <li><span class="breadcrumb__link">></span></li>
        <li><span class="breadcrumb__link"><?= htmlspecialchars($category_name) ?></span></li>
      </ul>
    </section>

    <section class="products">
    <div class="products__container grid">
      
        <div class="products__group">

          
          <div class="filters">
            <div class="filters-header">Filters</div>

         
            <div class="filter-item">
              <input type="checkbox" id="all-products" class="all-products-checkbox" <?php echo ($category_filter == 0) ? 'checked' : ''; ?>>
              <label for="all-products" class="all_product">Tous Les Produits</label>
            </div>

         
            <div class="category-label">Sort By Phone Category</div>
            <?php
            $category = getcategory("phone");
            if (mysqli_num_rows($category) > 0) {
              while ($item = mysqli_fetch_assoc($category)) {
                $isChecked = ($category_filter == $item['id']) ? 'checked' : '';                 ?>
                <div class="filter-item">
                  <input type="checkbox" id="category-<?= $item['id'] ?>" class="category-checkbox" <?= $isChecked ?>>
                  <label for="category-<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></label>
                </div>
                <?php
              }
            } else {
              echo "<p>No categories available.</p>";
            }
            ?>
            <div class="category-label">Sort By Accesoir Category</div>
            <?php
            $category = getcategory("accessoire");
            if (mysqli_num_rows($category) > 0) {
              while ($item = mysqli_fetch_assoc($category)) {
                $isChecked = ($category_filter == $item['id']) ? 'checked' : '';                 ?>
                <div class="filter-item">
                  <input type="checkbox" id="category-<?= $item['id'] ?>" class="category-checkbox" <?= $isChecked ?>>
                  <label for="category-<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></label>
                </div>
                <?php
              }
            } else {
              echo "<p>No categories available.</p>";
            }
            ?>
           
            <label for="price-range" class="price-label">Sort by Price</label>
            <div class="price-range-wrapper">
              <input type="range" id="min-price" name="min-price" min="0" max="4200" step="50" value="0">
            </div>
            <div class="price-range-display">
              <span id="min-price-display">$0</span> - <span id="max-price-display">$4200+</span>
            </div>
            <button id="go-button" class="price_button">Go</button>
          </div>

        </div>



    
        <div class="products__group">
    <?php
    $category_filter = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
    $search_term = isset($_GET['search']) ? mysqli_real_escape_string($con, trim($_GET['search'])) : '';
    $min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;

    $items_per_page = 9;
    $current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($current_page - 1) * $items_per_page;

    $product_conditions = [];
    if ($category_filter > 0) {
        $product_conditions[] = "p.category_id = $category_filter";
    }
    if ($min_price > 0) {
        $product_conditions[] = "p.selling_price >= $min_price";
    }
    if ($search_term) {
        $product_conditions[] = "p.name LIKE '%$search_term%'";
    }
    $product_where = $product_conditions ? " WHERE " . implode(" AND ", $product_conditions) : "";

    $products_query = "SELECT p.id, p.price, p.name, c.name AS category_name 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       $product_where 
                       ORDER BY p.id 
                       LIMIT $offset, $items_per_page";
    $all_items = mysqli_query($con, $products_query);

    if (!$all_items) {
        die('Query Error: ' . mysqli_error($con));
    }

    $total_items_query = "SELECT COUNT(*) AS total FROM products p LEFT JOIN categories c ON p.category_id = c.id $product_where";
    $total_items_result = mysqli_query($con, $total_items_query);
    $total_items = mysqli_fetch_assoc($total_items_result)['total'] ?? 0;
    $total_pages = ceil($total_items / $items_per_page);

    echo '<p class="total__products">Nous avons trouvé <span>' . $total_items . '</span> articles pour vous</p>';
    echo '<div class="products__container grid">';
    if ($all_items && mysqli_num_rows($all_items) > 0) {
        while ($item = mysqli_fetch_assoc($all_items)) {
            
            // Fetch images
            $imagesQuery = "SELECT image FROM product_images WHERE product_id = " . $item['id'];
            $imagesResult = mysqli_query($con, $imagesQuery);
            $images = [];

            while ($imageRow = mysqli_fetch_assoc($imagesResult)) {
                $images[] = $imageRow['image'];
            }

            $defaultImage = !empty($images) ? '../uploads/images/product/' . trim($images[0]) : 'default-image.jpg';
            $hoverImage = isset($images[1]) ? '../uploads/images/product/' . trim($images[1]) : $defaultImage;

            $priceQuery = "SELECT price FROM ram_rom WHERE product_id = " . $item['id'];
            $priceResult = mysqli_query($con, $priceQuery);
            $priceData = mysqli_fetch_assoc($priceResult);
            $price = $priceData ? $priceData['price'] : $item['price'];

            $promoQuery = "
    SELECT promotions.name, promotions.percent, promotions.date_fin 
    FROM product_promo 
    INNER JOIN promotions ON product_promo.promo_id = promotions.id 
    WHERE product_promo.product_id = " . $item['id'];
            $promoResult = mysqli_query($con, $promoQuery);
            $promoData = mysqli_fetch_assoc($promoResult);

            $newPrice = $price;
            $oldPrice = '';
            if ($promoData && !empty($promoData['percent'])) {
                $percent = $promoData['percent'];
                $newPrice = $price * (1 - $percent / 100);
                $oldPrice = number_format($price, 0, '', ' ') . ' DZD';
            }
            $newPriceFormatted = number_format($newPrice, 0, '', ' ') . ' DZD';

            echo '
            <div class="product__item">
                <div class="product__banner">
                    <a href="details.php?id=' . $item['id'] . '" class="product__image">
                        <img src="' . $defaultImage . '" alt="Product Image 1" class="product__img default">
                        <img src="' . $hoverImage . '" alt="Product Image 2" class="product__img hover">
                    </a>
                    <div class="product__actions">
                        <a href="details.php?id=' . $item['id'] . '" class="action__btn" aria-label="Quick View">
                            <i class="fi fi-rs-eye"></i>
                        </a>
                        <a href="#" class="action__btn" aria-label="Add To Wishlist">
                            <i class="fi fi-rs-heart"></i>
                        </a>
                    </div>
                </div>
                <div class="product__content">
                    <span class="product__category">' . htmlspecialchars($item['category_name']) . '</span>
                    <a href="details.php?id=' . $item['id'] . '">
                        <h3 class="product__title">' . htmlspecialchars($item['name']) . '</h3>
                    </a>
                    <div class="product__price flex">
                        <span class="new__price">' . $newPriceFormatted . '</span>';
            if (!empty($oldPrice)) {
                echo '<span class="old__price">' . $oldPrice . '</span>';
            }
            echo '    </div>
                </div>
            </div>';
        }
    } else {
        echo '<p>Aucun produit trouvé.</p>';
    }
    echo '</div>';
    ?>

    <div class="pagination">
        <?php
        for ($i = 1; $i <= $total_pages; $i++) {
            $active_class = ($i == $current_page) ? 'active' : '';
            echo '<a href="?page=' . $i . '&category_id=' . $category_filter . '&min_price=' . $min_price . '&search=' . urlencode($search_term) . '" class="pagination-link ' . $active_class . '">' . $i . '</a>';
        }
        ?>
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
        <p class="newslettter__description" style="text-align:center" >
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
            <p class="newslettter__description" style="text-align:center" >
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
            <p class="newslettter__description" style="margin-left: 100px">
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
      <p class="copyright">&copy; 2023 Amine, Tous droits réservés</p>
      
    </div>
  </footer>

  <!--=============== SWIPER JS ===============-->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <!--=============== MAIN JS ===============-->
  <script src="assets/js/main.js"></script>

  <script src="assets/js/categoryfiltter.js"></script>

</body>
</html>