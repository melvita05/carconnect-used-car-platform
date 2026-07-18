<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = (int)$_SESSION['user_id'];

/* ===================== */
/* DELETE */
/* ===================== */

if(isset($_GET['delete'])){
  $id = (int)$_GET['delete'];

  $stmt = mysqli_prepare($conn,"
  DELETE FROM car_listings 
  WHERE id=? AND seller_id=?
  ");
  mysqli_stmt_bind_param($stmt,"ii",$id,$sellerId);
  mysqli_stmt_execute($stmt);

  echo "<div class='alert success'>Car deleted successfully</div>";
}
?>

<h1>🚗 Manage Listings</h1>

<p class="muted">
Edit or delete your car listings. After edit, listing goes for admin approval.
</p>

<div class="grid" style="margin-top:20px">

<?php

$stmt = mysqli_prepare($conn,"
SELECT id,make,model,status,price,image_path
FROM car_listings
WHERE seller_id=?
ORDER BY created_at DESC
");

mysqli_stmt_bind_param($stmt,"i",$sellerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($res)>0){

while($c=mysqli_fetch_assoc($res)){

$img = $c['image_path'] ?: "/carconnect/assets/images/default_car.jpg";

/* STATUS COLOR */

$statusColor = "#888";

if($c['status']=="approved") $statusColor="#4caf50";
if($c['status']=="pending")  $statusColor="#ff9800";
if($c['status']=="sold")     $statusColor="#9c27b0";

echo "

<div class='card'>

<img src='$img' style='height:180px;object-fit:cover'>

<div class='p'>

<div style='font-weight:800;font-size:16px'>
".e($c['make'])." ".e($c['model'])."
</div>

<div style='margin-top:6px;color:$statusColor;font-weight:700'>
".ucfirst($c['status'])."
</div>

<div style='margin-top:6px;font-weight:700;color:#00d2ff'>
₹".number_format($c['price'])."
</div>

<div style='margin-top:10px;display:flex;gap:8px'>

<a class='btn' href='/carconnect/seller/edit_car.php?id=".$c['id']."'>
✏️ Edit
</a>

<a class='btn' style='background:#ff4d4d;color:#fff'
onclick=\"return confirm('Delete this car?')\"
href='?delete=".$c['id']."'>
🗑️ Delete
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
No listings yet 🚗
</p>

<p class='muted'>
Start by adding your first car.
</p>

<a class='btn primary' href='/carconnect/seller/add_car.php'>
➕ Add Car
</a>

</div>

";

}

?>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>