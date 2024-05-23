<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];
if(!isset($user_id)){
    header('location:login_page.php');
 }
 if(isset($_POST['add_to_cart'])){

    $product_name = $_POST['product_name'];
    $res_name = $_POST['res_name'];
    $product_quantity = $_POST['product_quantity'];
    $product_price = $_POST['product_price'];
 
    $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE item = '$product_name' AND user_id = '$user_id'") or die('query failed');
 
    if(mysqli_num_rows($check_cart_numbers) > 0){
       $message[] = 'Already added to cart!';
    }else{
       mysqli_query($conn, "INSERT INTO `cart`(user_id, item, restaurant,quantity,price) VALUES('$user_id', '$product_name', '$res_name', '$product_quantity', '$product_price')") or die('query failed');
       $message[] = 'Item added to cart!';
    }
 
 }

 
 

 ?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
 
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
 
    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css">
 
 </head>
 <body>
    
 <?php include 'header.php'; ?>
 
 <div class="heading">
    <h3>Our Menu</h3>
    <p> <a href="home.php">Home</a> / Menu </p>
 </div>
 
 <section class="products">
 
    <h1 class="title">Our Menu</h1>
 
    <div class="box-container">
 
    <?php  
    if(isset($_GET['name'])){
        $res_name=mysqli_real_escape_string($conn, $_GET['name']);
         $select_products = mysqli_query($conn, "SELECT * FROM `menu` where res_name='$res_name' ") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
     <form action="" method="post" class="box">
      <div class="name"><?php echo $fetch_products['item']; ?></div>
      <div class="name"><?php echo $fetch_products['price']; ?></div>
      <input type="number" min="1" name="product_quantity" value="1" class="qty">
      <input type="hidden" name="product_name" value="<?php echo $fetch_products['item']; ?>">
      <input type-"hidden" name="res_name" value="<?php echo $fetch_products['res_name'];?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
      <input type="submit" value="Add to cart" name="add_to_cart" class="btn">
     </form>
      <?php
         }
      }else{
         echo '<p class="empty">No items added yet!</p>';
      }
    }
      ?>
                        
                            
    </div>
 
 </section>
 
 
 
 
 
 
 
 
 <?php include 'footer.php'; ?>
 
 <!-- custom js file link  -->
 <script src="js/script.js"></script>
 
 </body>
 </html>




