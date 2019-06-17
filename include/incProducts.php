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

//    get list of all products
function get_products() {
    require_once 'incDbh.php';
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
            while($row = $result->fetch_assoc()) {
                if(isset($_GET['product']) && $row['id']==$_GET['product']) {
                    $active = 'active';
                }
                else {
                    $active = '';
                }
                $product = <<<HTML
                        <a href="index.php?product={$row['id']}" class="list-group-item list-group-item-action {$active} d-flex justify-content-between align-items-center">
                        {$row['product_name']}
                        <span class="badge badge-primary badge-pill">{$row['product_stock']}</span>
                    </a>
HTML;
                
                echo $product;
            }
        }
        $stmt->close();
    }
}

// Get detail of product by id
function get_product_detail() {
    require 'incDbh.php';
    // require 'helper.php';
    $returnurl = $_SERVER['REQUEST_URI'];
    display_message();
    if(!isset($_GET['product'])) {
        $alert = <<<HTML
                <div class="alert alert-danger" role="alert">
                  Select product in list
                </div>
HTML;
        echo $alert;
        exit();
    }
    $id = $_GET['product'];
    $sqlquery = "SELECT * FROM tbl_product WHERE id = ?";
    $stmt = mysqli_stmt_init($connection);
    if(!$stmt->prepare($sqlquery))
    {
        header("Location: ../error.php?error=sqlerror");
        exit();
    }
    else {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 1) {
            while($row = $result->fetch_assoc()) {
                $timeToken = encrypt();
                $product = <<<HTML
                <form method="post" action="cart.php?action=add&product={$row['id']}">
                
                <input type='hidden' value='$timeToken' name='csrftoken'/>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{$row['product_name']}</h5>
                        <p class="card-text">{$row['product_desc']}</p>
                        <span class="card-text">€ {$row['product_price']}</span>
                        <input type="text" hidden name="returnurl" value={$returnurl}/>
                        <input type="text" name="quantity" value="1" size="2"/>
                        <input type="submit" class="btn btn-primary" value="Add to cart"/>
                    </div>
                </div>
                </form>
HTML;
                echo $product;
            }
        }
        $stmt->close();

    }
}
?>