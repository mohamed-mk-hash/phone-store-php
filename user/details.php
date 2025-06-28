<?php
include("../adminto/main/config/functions/myfunctions.php");

session_start();
$cart_count = 0;

if (isset($_SESSION['cart'])) {
  $cart_count = count($_SESSION['cart']); 
}

if (isset($_SESSION['error_message'])) {
  echo "<div class='error'>{$_SESSION['error_message']}</div>";
  unset($_SESSION['error_message']); 
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ram = isset($_POST['ram']) ? $_POST['ram'] : null;
    $rom = isset($_POST['rom']) ? $_POST['rom'] : null;
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;

    if ($product_id === null) {
        echo json_encode(['error' => 'Product ID is missing']);
        
        exit;
    }

    
    $promoQuery = "
    SELECT promotions.name, promotions.percent, promotions.date_fin 
    FROM product_promo 
    INNER JOIN promotions ON product_promo.promo_id = promotions.id 
    WHERE product_promo.product_id = " . $product_id;


    $promoResult = mysqli_query($con, $promoQuery);
    $promoData = mysqli_fetch_assoc($promoResult);

    
    $hasRamRomQuery = "SELECT COUNT(*) as count FROM ram_rom WHERE product_id = '$product_id' AND ram IS NOT NULL AND rom IS NOT NULL";
    $hasRamRomResult = mysqli_query($con, $hasRamRomQuery);
    $hasRamRom = mysqli_fetch_assoc($hasRamRomResult)['count'] > 0;

    if ($hasRamRom && $ram !== null && $rom !== null) {
        
        $ramRomQuery = "SELECT price FROM ram_rom WHERE ram = '$ram' AND rom = '$rom' AND product_id = '$product_id'";
        $ramRomResult = mysqli_query($con, $ramRomQuery);
        if ($ramRomResult) {
            $ramRom = mysqli_fetch_assoc($ramRomResult);
            if ($ramRom && isset($ramRom['price'])) {
                $price = $ramRom['price'];
                $newPrice = $price;
                $oldPrice = '';

                
                if ($promoData && !empty($promoData['percent'])) {
                    $percent = $promoData['percent'];
                    $newPrice = $price * (1 - $percent / 100);
                    $oldPrice = number_format((float)$price, 0, '', ' ') . ' DZD';
                }

                $newPriceFormatted = number_format((float)$newPrice, 0, '', ' ') . ' DZD';

                
                echo json_encode([
                    'newPrice' => $newPriceFormatted,
                    'oldPrice' => $oldPrice
                ]);
            } else {
                echo json_encode(['error' => 'Price not available']);
            }
        } else {
            echo json_encode(['error' => 'Error fetching price']);
        }
    } else {
        
        $productQuery = "SELECT price FROM products WHERE id = '$product_id'";
        $productResult = mysqli_query($con, $productQuery);
        if ($productResult) {
            $product = mysqli_fetch_assoc($productResult);
            if ($product && isset($product['price'])) {
                $price = $product['price'];
                $newPrice = $price;
                $oldPrice = '';

                
                if ($promoData && !empty($promoData['percent'])) {
                    $percent = $promoData['percent'];
                    $newPrice = $price * (1 - $percent / 100);
                    $oldPrice = number_format((float)$price, 0, '', ' ') . ' DZD';
                }

                $newPriceFormatted = number_format((float)$newPrice, 0, '', ' ') . ' DZD';

                
                echo json_encode([
                    'newPrice' => $newPriceFormatted,
                    'oldPrice' => $oldPrice
                ]);
            } else {
                echo json_encode(['error' => 'Price not available']);
            }
        } else {
            echo json_encode(['error' => 'Error fetching price']);
        }
    }
    exit;
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />

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
                        <a href="index.php" class="nav__link">Accueil</a>
                    </li>

                    <li class="nav__item">
                        <a href="shop.php" class="nav__link">Boutique</a>
                    </li>

                    <li class="nav__item">
                        <a href="Reparation.php" class="nav__link">Réparation</a>
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
    <?php

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $productResult = getProductById($id); 
  if (mysqli_num_rows($productResult) > 0) {
      $product = mysqli_fetch_array($productResult);
      $imageQuery = "SELECT image FROM product_images WHERE product_id = '$id'";
      $imagesResult = mysqli_query($con, $imageQuery);
      $images = [];
      while ($imageRow = mysqli_fetch_array($imagesResult)) {
          $images[] = '../uploads/images/product/' . trim($imageRow['image']);
      }

      
      $hasRamRomQuery = "SELECT COUNT(*) as count FROM ram_rom WHERE product_id = '$id' AND ram IS NOT NULL AND rom IS NOT NULL";
      $hasRamRomResult = mysqli_query($con, $hasRamRomQuery);
      $hasRamRom = mysqli_fetch_assoc($hasRamRomResult)['count'] > 0;

      if ($hasRamRom) {
          
          $ramRomQuery = "SELECT ram, rom, price FROM ram_rom WHERE product_id = '$id' AND ram IS NOT NULL AND rom IS NOT NULL";
          $ramRomResult = mysqli_query($con, $ramRomQuery);
          $ramRom = [];
          while ($row = mysqli_fetch_array($ramRomResult)) {
              $ramRom[] = [
                  'ram' => $row['ram'],
                  'rom' => $row['rom'],
                  'price' => $row['price']
              ];
          }
      } else {
          
          $priceWithoutRamRomQuery = "SELECT price FROM products WHERE id = '$id'";
          $priceWithoutRamRomResult = mysqli_query($con, $priceWithoutRamRomQuery);
          $priceWithoutRamRom = mysqli_fetch_assoc($priceWithoutRamRomResult);
      }

      
      $colorQuery = "SELECT color FROM product_colors WHERE product_id = '$id'";
      $colorResult = mysqli_query($con, $colorQuery);
      $colors = [];
      while ($colorRow = mysqli_fetch_array($colorResult)) {
          $colors[] = $colorRow['color'];
      }

      $defaultImage = $images[0];
  
?>
      <!--=============== BREADCRUMB ===============-->
      <section class="breadcrumb">
        <ul class="breadcrumb__list container">
         <li><a href="index.php" class="breadcrumb__link">Accueil</a></li>
         <li><span class="breadcrumb__link">></span></li>
         <li><span class="breadcrumb__link">Details de</span></li>
         <li><span class="breadcrumb__link">:</span></li>
         <li><span class="breadcrumb__link"><?= htmlspecialchars($product['name']) ?></span></li>
        </ul>
      </section>

      <!--=============== DETAILS ===============-->

      <section class="details">
        <div class="details__container container grid">
        <div class="details__group">
            <img src="<?= $defaultImage ?>" alt="Image du Produit" class="details__img">
            <div class="details__small-images grid">
                <?php
                foreach ($images as $image) {
                    echo '<img src="' . $image . '" alt="Additional Product Image" class="details__small-img">';
                }
                ?>
            </div>
        </div>

        <div class="details__info-group details__group" style="margin-top: 70px">
            <h3 class="details__title"><?= htmlspecialchars($product['name']) ?></h3>
            <p class="details__brand">Marque : <span><?= htmlspecialchars($product['brand']) ?></span></p>

            <div class="details__price flex">
        <?php
    
        $promoQuery = "
            SELECT promotions.percent, promotions.date_fin 
            FROM product_promo 
            INNER JOIN promotions ON product_promo.promo_id = promotions.id 
            WHERE product_promo.product_id = '$id'
        ";
        $promoResult = mysqli_query($con, $promoQuery);
        $promoData = mysqli_fetch_assoc($promoResult);


        $maxPriceQuery = "SELECT MAX(price) as max_price FROM ram_rom WHERE product_id = '$id'";
        $maxPriceResult = mysqli_query($con, $maxPriceQuery);
        $maxPriceData = mysqli_fetch_assoc($maxPriceResult);


        if ($maxPriceData && !empty($maxPriceData['max_price'])) {
            $price = $maxPriceData['max_price'];
        } elseif (isset($priceWithoutRamRom)) {
            $price = $priceWithoutRamRom['price'];
        } else {
            $price = null;
        }

    
        if ($price !== null) {
            $newPrice = $price;
            $oldPrice = '';

        
            if ($promoData && !empty($promoData['percent'])) {
                $percent = $promoData['percent'];
                $newPrice = $price * (1 - $percent / 100);
                $oldPrice = number_format((float)$price, 0, '', ' ') . ' DZD';
            }

            $newPriceFormatted = number_format((float)$newPrice, 0, '', ' ') . ' DZD';

            echo '<span class="new__price">' . $newPriceFormatted . '</span>';
            if (!empty($oldPrice)) {
                echo '<span class="old__price">' . $oldPrice . '</span>';
            }
        } else {
            echo '<span class="new__price">Prix non disponible</span>';
        }
        ?>
    </div>


            <ul class="product__list">
                <li class="list__item flex" style="color: #455852;"><i class="fi-rs-shield"></i> Garantie 1 an</li>
                <li class="list__item flex" style="color: #8F8F8C;"><i class="fi-rs-refresh" ></i> Politique de retour de 30 jours</li>
                <li class="list__item flex" style="color: #8F8F8C;"><i class="fi-rs-credit-card" ></i> Card, Espèces</li>
            </ul>

            <?php if (!empty($colors)): ?>
                <div class="details__color flex">
                    <span class="details__color-title">Couleurs</span>
                    <ul class="color__list flex">
                        <?php foreach ($colors as $color): ?>
                            <li class="color__item" data-color="<?= htmlspecialchars($color) ?>" style="background-color: <?= htmlspecialchars($color) ?>;" onclick="selectColor('<?= htmlspecialchars($color) ?>')"></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($hasRamRom && !empty($ramRom)): ?>
                <div class="details__ram-rom">
                    <div class="ram-rom__list grid">
                        <?php foreach ($ramRom as $pair): ?>
                            <?php
                            $price = $pair['price'];
                            $newPrice = $price;
                            $oldPrice = '';

                            if ($promoData && !empty($promoData['percent'])) {
                                $percent = $promoData['percent'];
                                $newPrice = $price * (1 - $percent / 100);
                                $oldPrice = number_format((float)$price, 0, '', ' ') . ' DZD';
                            }

                            $newPriceFormatted = number_format((float)$newPrice, 0, '', ' ') . ' DZD';
                            ?>
                            <div class="ram-rom__item" data-ram="<?= htmlspecialchars($pair['ram']) ?>" data-rom="<?= htmlspecialchars($pair['rom']) ?>">
                                <span class=""><?= htmlspecialchars($pair['ram']) ?></span> / 
                                <span class=""><?= htmlspecialchars($pair['rom']) ?></span>
                                <span class="" ><?= htmlspecialchars($pair['price']) ?> DZD<span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="details__action">
        <?php if (isset($_SESSION['user_id'])): ?>
            <form id="add-to-cart-form" action="cart/cart_actions.php" method="POST">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                <input type="hidden" name="product_image" value="<?= $defaultImage ?>">
                <input type="hidden" name="product_qty" value="<?= $product['qty'] ?>">
                <input type="hidden" name="selected_color" id="selected_color" value="">
                <input type="hidden" name="selected_ram" id="selected_ram" value="">
                <input type="hidden" name="selected_rom" id="selected_rom" value="">
                <input type="hidden" name="product_price" id="product_price" value="<?= $newPriceFormatted ?>">
                <input type="number" name="quantity" class="quantity" value="1" min="1" max="<?= $product['qty'] ?>">
                <button type="submit" class="btn btn--sm" style="cursor: pointer; background-color: rgb(230, 54, 54); transition: background-color 0.3s ease; color: #ffffff;" 
        onmouseover="this.style.backgroundColor='#FFDFB3'; this.style.color='#000000'" 
        onmouseout="this.style.backgroundColor='rgb(230, 54, 54)'; this.style.color='#ffffff'">
        Ajouter au Panier
    </button>
            </form>
        <?php else: ?>
            <input type="number" name="quantity" class="quantity" value="1" min="1" max="10">
            <a href="login-register.php" class="nav__link">
            <button type="button" class="btn btn--sm" style="cursor: pointer; background-color: rgb(230, 54, 54); transition: background-color 0.3s ease; color: #ffffff;" 
        onmouseover="this.style.backgroundColor='#FFDFB3'; this.style.color='#000000'" 
        onmouseout="this.style.backgroundColor='rgb(230, 54, 54)'; this.style.color='#ffffff'">
        Ajouter au Panier
    </button>
            </a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
        <form action="wishlist/add_to_wishlist.php" method="POST" style="display: inline;">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
            <input type="hidden" name="product_image" value="<?= $defaultImage ?>">
            <input type="hidden" name="product_qty" value="<?= $product['qty'] ?>">
            <input type="hidden" name="product_price" id="product_price" value="<?= $newPriceFormatted ?>">

            <button type="submit" class="details__action-btn tooltip-btn" style="margin-top: 10px; background: none; border: none; cursor: pointer;">
            <i class="fi fi-rs-heart" 
    style="font-size:30px; padding:10px; border-radius:8px; transition:color 0.3s ease; cursor:pointer;"
    onmouseover="this.style.color='#2186EB';"
    onmouseout="this.style.color='#8F8F8C';">
    </i>

            </button>
        </form>
    <?php else: ?>
        <a href="login-register.php" class="details__action-btn tooltip-btn" style="margin-top: 10px; background: none; border: none; cursor: pointer;" aria-label="Ajouter à la liste de souhaits">
            <i class="fi fi-rs-heart" style="font-size: 30px;"></i>
        </a>
    <?php endif; ?>

    </div>
            <div id="error-message" style="color: red; margin-top: 10px;"></div>
            <ul class="details__meta">
                <li class="meta__list flex"><span>SKU :</span>FWM15VKT</li>
                <li class="meta__list flex"><span>Tags :</span><?= htmlspecialchars($product['category_name']) ?></li>
                <li class="meta__list flex"><span>Disponibilité :</span><?= $product['qty'] ?> Articles en Stock</li>
            </ul>
        </div>
    </div>
    </section>

    <script>
    const colorItems = document.querySelectorAll('.color__item');
    const ramItems = document.querySelectorAll('.ram-rom__item');
    const addButton = document.querySelector('#add-to-cart-form button');
    const errorMessage = document.querySelector('#error-message');
    const newPriceElement = document.querySelector('.new__price'); 
    const oldPriceElement = document.querySelector('.old__price');
    const productPriceInput = document.querySelector('#product_price'); 

    let selectedColor = '';
    let selectedRam = '';
    let selectedRom = '';
    let selectedPrice = '';
    let displayedPrice = ''; 
    const ramRomSectionExists = ramItems.length > 0;
    const hasPromotion = oldPriceElement !== null;

    // Initialize color selection
    if (colorItems.length === 1) {
        selectedColor = colorItems[0].getAttribute('data-color');
        document.getElementById('selected_color').value = selectedColor;
    } else {
        colorItems.forEach(item => {
            item.addEventListener('click', function() {
                colorItems.forEach(c => c.classList.remove('active'));
                item.classList.add('active');
                selectedColor = item.getAttribute('data-color');
                document.getElementById('selected_color').value = selectedColor;
            });
        });
    }

    // Handle RAM/ROM selection
    ramItems.forEach(item => {
        item.addEventListener('click', function() {
            ramItems.forEach(r => r.classList.remove('active'));
            item.classList.add('active');
            
            selectedRam = item.getAttribute('data-ram');
            selectedRom = item.getAttribute('data-rom');
            document.getElementById('selected_ram').value = selectedRam;
            document.getElementById('selected_rom').value = selectedRom;

            // Get the price from the clicked RAM/ROM item
            const priceText = item.querySelector('span:last-child').textContent;
            selectedPrice = priceText.replace(' DZD', '').replace(/\s+/g, '');
            
            // Always update the displayed price
            updatePriceDisplay(selectedRam, selectedRom);
        });
    });

    // Function to update price display (works for all products)
    function updatePriceDisplay(ram, rom) {
        const productId = document.querySelector('input[name="product_id"]').value;

        fetch('details.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                ram: ram,
                rom: rom,
                product_id: productId
            })
        })
        .then(response => response.text())
        .then(data => {
            if (data) {
                const responseData = JSON.parse(data);
                newPriceElement.textContent = responseData.newPrice;
                displayedPrice = responseData.newPrice.replace(' DZD', '').replace(/\s+/g, '');

                if (responseData.oldPrice) {
                    oldPriceElement.textContent = responseData.oldPrice;
                    oldPriceElement.style.display = 'inline';
                } else if (oldPriceElement) {
                    oldPriceElement.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error fetching price:', error);
        });
    }

    // Handle add to cart button click
    addButton.addEventListener('click', function(e) {
        let error = '';

        if (colorItems.length > 1 && !selectedColor) {
            error = 'Veuillez sélectionner une couleur avant d\'ajouter au panier.';
        } else if (ramRomSectionExists && (!selectedRam || !selectedRom)) {
            error = 'Veuillez sélectionner une option RAM/ROM avant d\'ajouter au panier.';
        }

        // Set the final price for the cart
        if (!error) {
            if (hasPromotion) {
                // For products WITH promotion: use the displayed price
                document.getElementById('product_price').value = displayedPrice;
            } else {
                // For products WITHOUT promotion: use the selected RAM/ROM price
                const activeRamRom = document.querySelector('.ram-rom__item.active');
                if (activeRamRom) {
                    const priceText = activeRamRom.querySelector('span:last-child').textContent;
                    selectedPrice = priceText.replace(' DZD', '').replace(/\s+/g, '');
                    document.getElementById('product_price').value = selectedPrice;
                }
            }
        }

        if (error) {
            e.preventDefault();
            errorMessage.textContent = error;
        } else {
            errorMessage.textContent = '';
        }
    });

    // Initialize price for products without RAM/ROM options
    if (!ramRomSectionExists) {
        displayedPrice = newPriceElement.textContent.replace(' DZD', '').replace(/\s+/g, '');
        document.getElementById('product_price').value = displayedPrice;
    }
</script>


<style>
    .color__item.active, 
.ram-rom__item.active {
    border: 1.5px solid black;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}

</style>




      <!--=============== DETAILS TAB ===============-->
      <section class="details__tab">
        <div class="detail__tabs">
            <span class="detail__tab active-tab" data-target="#description" >
                Description
            </span>
          <span class="detail__tab " data-target="#info">
          Informations Supplémentaires
          </span>
        
 
        <span class="detail__tab" data-target="#reviews">
        Avis (<?php 
                $product_id = $product['id'];                 $reviews_query_count = "SELECT COUNT(*) AS review_count FROM reviews WHERE product_id = '$product_id'"; 
                $result = mysqli_query($con, $reviews_query_count);                 $row = mysqli_fetch_assoc($result);
                echo $row['review_count'];             ?>)
        </span>
        </div>
        
        <div class="details__tabs-content">
            <div class="details__tab-content active-tab" content id="description" style="padding-right:100px; font-size: 20px;" >
            <p class="description__text" style="color: #212121;" >
            <?= (htmlspecialchars($product['description'])) ?>
        </p>
            </div>
          <div class="details__tab-content" content id="info">
            <table class="info__table">
              <tr>
                <th>Quantité</th>
                <td><?= htmlspecialchars($product['qty']) ?></td>
              </tr>

              <tr>
                <th>Hauteur</th>
                <td><?= htmlspecialchars($product['height']) ?></td>
              </tr>
               <tr> 
                <th>Largeur</th>
                <td><?= htmlspecialchars($product['width']) ?></td>
              </tr>
              <tr>
                <th>marque</th>
                <td><?= htmlspecialchars($product['brand']) ?></td>
              </tr>
            </table>
          </div>


          <div class="details__tab-content" content id="reviews">
    <div class="reviews__container grid">
        <?php 
        $product_id = $product['id'];       
        $reviews_query = "SELECT reviews.*, users.username 
        FROM reviews 
        JOIN users ON reviews.user_id = users.id 
        WHERE reviews.product_id = '$product_id'";
        $reviews = mysqli_query($con, $reviews_query); 
        if(mysqli_num_rows($reviews) > 0) {
            foreach($reviews as $item) {
                ?>
                <div class="review__single">
                    <div class="review__data">
                        <div class="review__rating">
                            <h4 class="review__title"><?= htmlspecialchars($item['username']) ?></h4>
                        </div>
                        <p class="review__description"><?= htmlspecialchars($item['comments']) ?></p>
                        <span class="review__data">
    <?php 
    $created_at = htmlspecialchars($item['created_at']); 
    echo date("Y-m-d ", strtotime($created_at)); 
    ?>
</span>

                    </div>
                </div>
                <?php
            }
        } else {
            echo "aucun avis trouvé dans ce produit";
        }
        ?>
    </div>

    <div class="review__form">
        <h4 class="review__form-title">Ajouter un Avis</h4>
        <div class="review__data">
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="add_review.php" method="POST" class="form grid">
             
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                <input type="hidden" name="username" value="<?= htmlspecialchars($_SESSION['username']) ?>">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id']) ?>">
                
           
                <textarea class="form__input textarea" name="comments" placeholder="Write a comment" required></textarea>
            

            <div class="form__btn">
                <button type="submit" name="submit_review_btn" class="btn">Soumettre un Avis</button>
            </div>
        </form>

        <?php else: ?>
          <form action="login-register.php" class="form grid">
          <textarea class="form__input textarea" name="comments" placeholder="Écrire un commentaire" required></textarea>
          
            
          <div class="form__btn">
                <button type="submit" name="submit_review_btn" class="btn">Soumettre un Avis</button>
            </div>
            </form>
          
          <?php endif; ?>
    </div>
</div>
      </section>

<?php 
    } else {
       echo "no comments found";
    }
} else {
    echo 'ID missing from the URL';
}
?>
      <!--=============== PRODUCTS ===============-->
      <?php

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    
    $checkQuery = "SELECT table_name, category_id FROM (
        SELECT 'products' AS table_name, category_id FROM products WHERE id = $id
    ) AS subquery";
    
    $result = mysqli_query($con, $checkQuery);
    if ($result && mysqli_num_rows($result) > 0) {
        $selectedProduct = mysqli_fetch_assoc($result);
        $categoryId = $selectedProduct['category_id'];
        $table = $selectedProduct['table_name'];

        $relatedProductsQuery = getpbyc($table);
    }
}
?>

<section class="products section--lg">
    <h3 class="section__title"><span>Produits </span>connexes</h3>

    <div class="products__container grid">
    <?php
    if ($relatedProductsQuery && mysqli_num_rows($relatedProductsQuery) > 0) {
        while ($product = mysqli_fetch_assoc($relatedProductsQuery)) {
            if ($product['category_id'] == $categoryId && $product['id'] != $id) {
                
                $imagesQuery = "SELECT image FROM product_images WHERE product_id = " . $product['id'];
                $imagesResult = mysqli_query($con, $imagesQuery);
                $images = [];

                while ($imageRow = mysqli_fetch_assoc($imagesResult)) {
                    $images[] = $imageRow['image'];
                }

                $defaultImage = !empty($images) ? '../uploads/images/product/' . trim($images[0]) : 'default-image.jpg';
                $hoverImage = isset($images[1]) ? '../uploads/images/product/' . trim($images[1]) : $defaultImage;

                
                $priceQuery = "SELECT price FROM ram_rom WHERE product_id = " . $product['id'];
                $priceResult = mysqli_query($con, $priceQuery);
                $priceData = mysqli_fetch_assoc($priceResult);
                $price = $priceData ? $priceData['price'] : $product['price'];

                
                $promoQuery = "
                SELECT promotions.percent, promotions.date_fin 
                FROM product_promo 
                INNER JOIN promotions ON product_promo.promo_id = promotions.id 
                WHERE product_promo.product_id = '$id'";

                $promoResult = mysqli_query($con, $promoQuery);
                $promoData = mysqli_fetch_assoc($promoResult);

                
                $newPrice = $price; 
                $oldPrice = ''; 

                if ($promoData && !empty($promoData['percent'])) {
                    $percent = $promoData['percent'];
                    $newPrice = $price * (1 - $percent / 100); 
                    $oldPrice = number_format((float)$price, 0, '', ' ') . ' DZD'; 
                }

                
                $newPriceFormatted = number_format((float)$newPrice, 0, '', ' ') . ' DZD';

                
                echo '
                <div class="product__item">
                    <div class="product__banner">
                        <a href="details.php?id=' . $product['id'] . '" class="product__image">
                            <img src="' . $defaultImage . '" alt="Product Image 1" class="product__img default">
                            <img src="' . $hoverImage . '" alt="Product Image 2" class="product__img hover">
                        </a>
                        <div class="product__actions">
                            <a href="details.php?id=' . $product['id'] . '" class="action__btn" aria-label="Quick View">
                                <i class="fi fi-rs-eye"></i>
                            </a>
                            <a href="#" class="action__btn" aria-label="Add To Wishlist">
                                <i class="fi fi-rs-heart"></i>
                            </a>
                        </div>
                        <div class="product__badge light-pink">Hot</div>
                    </div>
                    <div class="product__content">
                        <span class="product__category">' . htmlspecialchars($product['category_name']) . '</span>
                        <a href="details.php?id=' . $product['id'] . '">
                            <h3 class="product__title">' . htmlspecialchars($product['name']) . '</h3>
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
        }
    } else {
        echo '<p>No related products found.</p>';
    }
    ?>
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
            <a href="https:              class="footer__social-icon"></a>
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

    <!--=============== ALERTIFY ===============-->

    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {
    const colorItems = document.querySelectorAll('.color__item');
    const ramRomItems = document.querySelectorAll('.ram-rom__item');

    colorItems.forEach(item => {
        item.addEventListener('click', function () {
            if (!item.classList.contains('selected'))  {
                                colorItems.forEach(i => i.classList.remove('selected'));
                                item.classList.add('selected');
                                document.getElementById('selected_color').value = item.dataset.color;
            }
        });
    });

    ramRomItems.forEach(item => {
        item.addEventListener('click', function () {
            if (!item.classList.contains('selected')) {
                                
                                ramRomItems.forEach(i => i.classList.remove('selected'));
                                item.classList.add('selected');
                                document.getElementById('selected_ram').value = item.dataset.ram;
                document.getElementById('selected_rom').value = item.dataset.rom;
            }
        });
    });
});


</script>


<?php  if(isset($_SESSION['message'])) { ?>
<script>
   alertify.set('notifier','position', 'top-right');
   alertify.success('<?= $_SESSION['message']; ?>');

   <?php  
  unset($_SESSION['message']);
  } ?>


</script>

  </body>
</html>
