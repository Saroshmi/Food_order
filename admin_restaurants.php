<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login_page.php');
};

if(isset($_POST['add_product'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $location = mysqli_real_escape_string($conn, $_POST['location']);
   $category = mysqli_real_escape_string($conn, $_POST['category']);
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'C:\xampp\htdocs\food_order\uploaded_images/'.$image;

   $select_product_name = mysqli_query($conn, "SELECT name FROM `restaurants` WHERE name = '$name'") or die('query failed');

   if(mysqli_num_rows($select_product_name) > 0){
      $message[] = 'Restaurant already added';
   }else{
      $add_product_query = mysqli_query($conn, "INSERT INTO `restaurants`(name, location, category, image) VALUES('$name', '$location', '$category', '$image')") or die('query failed');

      if($add_product_query){
         if($image_size > 2000000){
            $message[] = 'image size is too large';
         }else{
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Restaurant added successfully!';
         }
      }else{
         $message[] = 'Restaurant could not be added!';
      }
   }
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_image_query = mysqli_query($conn, "SELECT image FROM `restaurants` WHERE id = '$delete_id'") or die('query failed');
   $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
   unlink('C:\xampp\htdocs\food_order\uploaded_images/'.$fetch_delete_image['image']);
   mysqli_query($conn, "DELETE FROM `restaurants` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_restaurants.php');
}

if(isset($_POST['update_product'])){

   $update_p_id = $_POST['update_p_id'];
   $update_name = $_POST['update_name'];
   $update_location = $_POST['update_location'];

   mysqli_query($conn, "UPDATE `restaurants` SET name = '$update_name', location = '$update_location' WHERE id = '$update_p_id'") or die('query failed');

   $update_image = $_FILES['update_image']['name'];
   $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
   $update_image_size = $_FILES['update_image']['size'];
   $update_folder = 'C:\xampp\htdocs\food_order\uploaded_images/'.$update_image;
   $update_old_image = $_POST['update_old_image'];

   if(!empty($update_image)){
      if($update_image_size > 2000000){
         $message[] = 'image file size is too large';
      }else{
         mysqli_query($conn, "UPDATE `restaurants` SET image = '$update_image' WHERE id = '$update_p_id'") or die('query failed');
         move_uploaded_file($update_image_tmp_name, $update_folder);
         unlink('C:\xampp\htdocs\food_order\uploaded_images/'.$update_old_image);
      }
   }

   header('location:admin_restaurants.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Restaurants</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- product CRUD section starts  -->

<section class="add-products">

   <h1 class="title">Restaurants</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Add Restaurant</h3>
      <input type="text" name="name" class="box" placeholder="Enter product name" required>
      <input type="text"  name="location" class="box" placeholder="Enter restaurant location" required>
      <select id="category" class="box" name="category">
      <option value="veg">Vegetarian</option>
      <option value="nonveg">Non-Vegetarian</option>
      </select>
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
      <input type="submit" value="Add restaurant" name="add_product" class="btn">
   </form>

</section>

<!-- product CRUD section ends -->

<!-- show products  -->

<section class="show-products">

   <div class="box-container">

      <?php
         $select_products = mysqli_query($conn, "SELECT * FROM `restaurants`") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
      <div class="box">
         <img src="C:\xampp\htdocs\food_order\uploaded_images/<?php echo $fetch_products['image']; ?>"  alt="image">
         <div class="name"><?php echo $fetch_products['name']; ?></div>
         <div class="price"><?php echo $fetch_products['location']; ?></div>
         <a href="admin_restaurants.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">Update</a>
         <a href="admin_restaurants.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">No restaurants added yet!</p>';
      }
      ?>
   </div>

</section>

<section class="edit-product-form">

   <?php
      if(isset($_GET['update'])){
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `restaurants` WHERE id = '$update_id'") or die('query failed');
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
      <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
      <img src="C:\xampp\htdocs\food_order\uploaded_images/<?php echo $fetch_update['image']; ?>" alt="">
      <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Enter restaurant name">
      <input type="text" name="update_location" value="<?php echo $fetch_update['location']; ?>"  class="box" required placeholder="Enter restaurant location">
      <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
      <input type="submit" value="update" name="update_product" class="btn">
      <input type="reset" value="cancel" id="close-update" class="option-btn">
   </form>
   <?php
         }
      }
      }else{
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
   ?>

</section>







<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>
