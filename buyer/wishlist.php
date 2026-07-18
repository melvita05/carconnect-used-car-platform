<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("buyer");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$buyerId = (int)$_SESSION['user_id'];

/* ===================== */
/* ADD */
/* ===================== */

if(isset($_GET['add'])){
  $carId = (int)$_GET['add'];

  $stmt = mysqli_prepare($conn,"
  INSERT IGNORE INTO wishlist(buyer_id,car_id)
  VALUES (?,?)
  ");
  mysqli_stmt_bind_param($stmt,"ii",$buyerId,$carId);
  mysqli_stmt_execute($stmt);

  echo '<div class="alert success">Added to wishlist ❤️</div>';
}

/* ===================== */
/* REMOVE */
/* ===================== */

if(isset($_GET['remove'])){
  $carId = (int)$_GET['remove'];

  $stmt = mysqli_prepare($conn,"
  DELETE FROM wishlist 
  WHERE buyer_id=? AND car_id=?
  ");
  mysqli_stmt_bind_param($stmt,"ii",$buyerId,$carId);
  mysqli_stmt_execute($stmt);

  echo '<div class="alert success">Removed from wishlist</div>';
}
?>

<h1>❤️ My Wishlist</h1>

<div class="grid" style="margin-top:20px">

<?php

$stmt = mysqli_prepare($conn,"
SELECT c.id,c.make,c.model,c.price,c.image_path
FROM wishlist w
JOIN car_listings c ON c.id=w.car_id
WHERE w.buyer_id=?
ORDER BY w.created_at DESC
");

mysqli_stmt_bind_param($stmt,"i",$buyerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($res)>0){

while($c=mysqli_fetch_assoc($res)){

$img = $c['image_path'] ?: "/carconnect/assets/images/default_car.jpg";

echo "

<div class='card'>

<img src='".e($img)."' style='height:200px;object-fit:cover'>

<div class='p'>

<div style='font-weight:800;font-size:16px'>
".e($c['make'])." ".e($c['model'])."
</div>

<div style='margin-top:6px;color:#00d2ff;font-weight:700'>
₹".number_format($c['price'])."
</div>

<div style='margin-top:12px;display:flex;gap:8px'>

<a class='btn primary'
href='car_details.php?id=".$c['id']."'>
View
</a>

<a class='btn'
style='background:#ff4d4d;color:#fff'
onclick=\"return confirm('Remove from wishlist?')\"
href='wishlist.php?remove=".$c['id']."'>
Remove
</a>

</div>

</div>

</div>

";

}

}else{

echo "

<div style='text-align:center;width:100%'>

<p class='muted' style='font-size:18px'>
No wishlist items ❤️
</p>

<p class='muted'>
Start adding cars you like.
</p>

<a class='btn primary' href='car_listings.php'>
Browse Cars
</a>

</div>

";

}

?>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>