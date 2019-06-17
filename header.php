
<!DOCTYPE html>

<html>
  <head>
    <meta  charset="utf-8">
    <meta name="description" content="description">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>WebShop</title>
    <link href="bootstrap.css" rel="stylesheet" type="text/css">
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.css">
  </head>
  <header>
      <body class="loggedin">
        <nav class="navtop">
          <div>
            <h1>
              <a href="index.php">
                FH WebShop
              </a>
            </h1>
            <?php
              require_once "include/helper.php";

              if (session_status() == PHP_SESSION_NONE) {
                  session_start();
              }
              // if user login, set session duration to 4 hours
              $hour = 3600;
              if (isset($_SESSION['id']) && (time() - $_SESSION['login_time'] > 4*$hour)) {
                session_destroy();
                header("Location:../webshop/login.php");
              }
              $count = count_items();
              $order = <<<HTML
                  <a href="order.php"><i class="fa fa-history"></i>Orders</a>
HTML;
              if (isset($_SESSION['id'])) {
                echo $order;
              }
              $cart = <<<HTML
                <a href="cart.php"><i class="fa fa-shopping-cart"></i>{$count}</a>
HTML;
              echo $cart;
              if (isset($_SESSION['name'])) {
                $logout = <<<HTML
                <a href="profile.php"><i class="fa fa-user"></i>{$_SESSION['name']}</a>
                <a href="include/incLogout.php"><i class="fa fa-sign-out"></i>Log out</a>
HTML;
                echo $logout;
              }
              else {
                $login = <<<HTML
                <a href="login.php"><i class="fa fa-sign-in"></i>Login</a>
HTML;
                  echo $login;
              }
            ?>
          </div>
        </nav>
        </header>

      <!-- <nav> -->
        <!-- <a href="#">
        </a>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="#">Basket</a></li>
        </ul> -->

      <!-- </nav> -->

  </body>
</html>
