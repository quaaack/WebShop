<?php
    function simpleCrypt( $string, $action = 'e' ) {
        // this is the encryption function
        $secretKey1 = 'my_simple_secret_key'; // this is the key
        $secretIv1 = 'my_simple_secret_iv'; // this is the initial value which is kinda used as a salt
     
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secretKey1 ); // SHA II encryption method
        $iv = substr( hash( 'sha256', $secretIv1 ), 0, 16 ); // encrypting the initial value
     
        if( $action == 'e' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'd' ){
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }
             return $output;
    }
    //encryption function
   function encrypt(){
        date_default_timezone_set('GMT');
        $now = date('m/d/Y h:i:s a', time());
        $oneMoreHourFromNow = date('Y-m-d H:i',strtotime('+2 hour +5 seconds',strtotime($now)));
        $token = strtotime($oneMoreHourFromNow);  //token generated from normal time to the unix time
        return simpleCrypt($token, 'e'); // token encrypted
   }
   
   function decrypt($token){
    return simpleCrypt($token, 'd');
   }
// show the cart list
    function show_cart() {
        // session_start();
        require 'incDbh.php';
        $sqlquery = "SELECT * FROM tbl_product";
        $stmt = mysqli_stmt_init($connection);
        if(!$stmt->prepare($sqlquery))
        {
            header("Location: ../index.php?error=sqlerror");
            exit();
        }
        else {
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0) {
                $i = 0;
                $sum = 0;
                while($row = $result->fetch_assoc()) {
                    $cart_item = null;
                    if (isset($_SESSION['id'])) {
                        if (isset($_SESSION['cart_' . $row['id']])) {
                            $cart_item = $_SESSION['cart_' . $row['id']];
                        }
                    }
                    else if (isset($_COOKIE['cart_' . $row['id']])) {
                        $cart_item = $_COOKIE['cart_' . $row['id']];
                    }

                    if (isset($cart_item)) {
                        $i++;
                        $total_price = (int) $row['product_price'] * $cart_item;
                        $product_name = $row['product_name'];
                        $quantity = htmlspecialchars($cart_item, ENT_QUOTES, 'UTF-8'); // sanitization to avoid xss attack
                        $sum += $total_price;
                        $cart = <<<HTML
                        <tr>
                            <th scope="row">$i</th>
                            <td>{$row['product_name']}</td>
                            <td>
                                <form method="post" action="cart.php?action=update&product={$row['id']}">
                                    <input type="text" name="quantity" value="$quantity" size="2"/>
                                    <input type="submit" class="btn btn-primary" value="update"/>
                                </form>
                            </td>
                            <td>{$row['product_price']}</td>
                            <td>{$total_price}</td>
                            <td>
                                <form method="post" action="cart.php?action=remove&product={$row['id']}">
                                    <input type="text" hidden name="product_name" value="$product_name" />
                                    <input type="submit" class="btn btn-danger" value="X"/>
                                </form>
                            </td>
                        </tr>
HTML;
                    
                    echo $cart;
                    }
                }
                $s_price = <<<HTML
                    <strong>Sum: </strong>
                    {$sum} €
HTML;
                echo $s_price;
            }
        }
    }

    require 'incDbh.php';

// update basket, params = product id, amount and user id
    function update_basket($product_id, $amount, $user_id) {
        require 'incDbh.php';
        $sqlquery = "UPDATE tbl_basket SET amount = ? WHERE product_id = ? AND user_id = ? AND order_id IS NULL";
        $stmt = mysqli_stmt_init($connection);
        if(!$stmt->prepare($sqlquery))
        {
          header("Location: ../error.php");
          exit();
        }else {
          $stmt->bind_param("iii", $amount, $product_id, $user_id);
          $stmt->execute();
        //setcookie('cart_' . $i, '', time() - 3600, '/');
        }
    }

    // add new product to basket
    function add_basket($product_id, $amount, $user_id) {
        require 'incDbh.php';
        $sqlquery = "SELECT * FROM tbl_basket WHERE product_id = ? AND user_id = ? AND order_id IS NULL";
        $stmt = mysqli_stmt_init($connection);
        if(!$stmt->prepare($sqlquery))
        {
            header("Location: ../index.php?error=sqlerror");
            exit();
        }
        else {
            $stmt->bind_param("ii", $product_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows == 1) {
                while($row = $result->fetch_assoc()) {
                    $new_value = $amount + $row['amount'];
                    update_basket($product_id, $new_value, $user_id);
                  }            }
            else {
                $sqlquery = "INSERT INTO tbl_basket (product_id, amount, user_id) VALUES (?, ?, ?)";
                $stmt = mysqli_stmt_init($connection);
                if(!$stmt->prepare($sqlquery))
                {
                  header("Location: ../error.php");
                  exit();
                }else {
                  $stmt->bind_param("iii", $product_id, $amount, $user_id);
                  $stmt->execute();
                //setcookie('cart_' . $i, '', time() - 3600, '/');
                }
            }
        }
    }

    // delete product from basket
    function delete_basket($product_id, $user_id) {
        require 'incDbh.php';
        $sqlquery = "DELETE FROM tbl_basket WHERE product_id = ? AND user_id = ? AND order_id IS NULL";
        $stmt = mysqli_stmt_init($connection);
        if(!$stmt->prepare($sqlquery))
        {
          header("Location: ../error.php");
          exit();
        }else {
          $stmt->bind_param("ii", $product_id, $user_id);
          $stmt->execute();
        //setcookie('cart_' . $i, '', time() - 3600, '/');
        }
    }
    // require 'helper.php';
    if (isset($_GET["action"])) {
        switch($_GET["action"]) {
            case "add":
                if(isset($_GET["product"]) && isset($_POST["quantity"])) {
                   
                //    first we check isset
                if(isset($_POST["csrftoken"])){
                    $csrftoken= $_POST["csrftoken"];
                    $decryptedToken = decrypt($csrftoken);
                    if($decryptedToken < time()) {
                        header("Location: error.php");
                        exit();
                    }
                }
                else{
                    header("Location: error.php");
                        exit();

                }
                //    echo $csrftoken; die();
                    $id = (int) $_GET["product"];
                    $quantity = (int) $_POST['quantity'];
                    if ($quantity <= 0) {
                        if (session_status() == PHP_SESSION_NONE) {
                            session_start();
                        }
                        set_message("Quantity must be positive value.");
                        header("Location: ../webshop/index.php?product={$id}");
                        exit();
                    }
                    $sqlquery = "SELECT * FROM tbl_product WHERE id = ?";
                    $stmt = mysqli_stmt_init($connection);
                    if(!$stmt->prepare($sqlquery))
                    {
                        header("Location: ../index.php?error=sqlerror");
                        exit();
                    }
                    else {
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if($result->num_rows == 1) {
                            while($row = $result->fetch_assoc()) {
                                if ($row['product_stock'] >= $quantity) {
                                    if (session_status() == PHP_SESSION_NONE) {
                                        session_start();
                                    }
                                    //if loggin, add no database
                                    if (isset($_SESSION['id'])) {
                                        $new_value = $quantity;
                                        if(isset($_SESSION['cart_' . $id])) {
                                            // $_SESSION['cart_' . $id] = 0;
                                            $new_value = $_SESSION['cart_' . $id] + $quantity;
                                        }
                                        add_basket($id, $quantity, (int) $_SESSION['id']);
                                        $_SESSION['cart_' . $id] = $new_value;
                                    }
                                    else {
                                        // if not, work with cookie
                                        if(isset($_COOKIE['cart_' . $id])) {
                                            // $_SESSION['cart_' . $id] = 0;
                                            $new_value = $_COOKIE['cart_' . $id] + $quantity;
                                            setcookie("cart_" . $id, $new_value, time() + (86400 * 30), "/");
                                        }
                                        else {
                                            setcookie("cart_" . $id, $quantity, time() + (86400 * 30), "/");
                                        }
                                    }
                                    header("Location: ../webshop/index.php?product={$id}");
                                    exit();
                                }
                                else {
                                    if (session_status() == PHP_SESSION_NONE) {
                                        session_start();
                                    }
                                    set_message("We do not have enough " . $quantity . " " . $row['product_name']);
                                    header("Location: ../webshop/index.php?product={$id}");
                                    exit();
                                }
                            }
                        }
                    }
                }
            break;
            case "update":
                if(isset($_GET["product"]) && isset($_POST["quantity"])) {
                    $id = (int) $_GET["product"];
                    $quantity = (int) $_POST['quantity'];
                    if ($quantity <= 0) {
                        if (session_status() == PHP_SESSION_NONE) {
                            session_start();
                        }
                        set_message("Quantity must be positive value.");
                        header("Location: ../webshop/cart.php");
                        exit();
                    }
                    $sqlquery = "SELECT * FROM tbl_product WHERE id = ?";
                    $stmt = mysqli_stmt_init($connection);
                    if(!$stmt->prepare($sqlquery))
                    {
                        header("Location: ../index.php?error=sqlerror");
                        exit();
                    }
                    else {
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if($result->num_rows == 1) {
                            while($row = $result->fetch_assoc()) {
                                if ($row['product_stock'] >= $quantity) {
                                    if (session_status() == PHP_SESSION_NONE) {
                                        session_start();
                                    }
                                    if (isset($_SESSION['id'])) {
                                        // if user login, update with database
                                        update_basket($id, $quantity, (int) $_SESSION['id']);
                                        $_SESSION['cart_' . $id] = $quantity;
                                    }
                                    else {
                                        // if not, update new cookie
                                        setcookie("cart_" . $id, $quantity, time() + (86400 * 30), "/");
                                    }
                                    header("Location: ../webshop/cart.php");
                                    exit();
                                }
                                else {
                                    if (session_status() == PHP_SESSION_NONE) {
                                        session_start();
                                    }
                                    set_message("We do not have enough " . $quantity . " " . $row['product_name']);
                                    header("Location: ../webshop/cart.php");
                                    exit();
                                }
                            }
                        }
                    }
                }
            break;
            case "remove":
                if(isset($_GET["product"])) {
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    // if user login, remove from database
                    if (isset($_SESSION['id'])) {
                        delete_basket($id, (int) $_SESSION['id']);
                        $key = 'cart_' . $_GET["product"];
                        unset($_SESSION[$key]);
                    }
                    else {
                        // if not, remove from cookie
                        $key = 'cart_' . $_GET["product"];
                        unset($_COOKIE[$key]);
                        setcookie($key, null, time() - 3600, '/');
                    }
                    set_message("Removed product " . $_POST["product_name"] . " from cart.");
                    header("Location: ../webshop/cart.php");
                    exit();
                }
            break;
            default:
            break;
        }
    }
?>
