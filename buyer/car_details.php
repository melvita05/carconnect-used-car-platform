<?php
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$id = (int)($_GET['id'] ?? 0);

/* ===================== */
/* FETCH CAR */
/* ===================== */

$stmt = mysqli_prepare($conn,"
SELECT c.*, 
s.name AS seller_name, 
s.id AS seller_id

FROM car_listings c

LEFT JOIN sellers s 
ON s.id = c.seller_id

WHERE c.id=?

LIMIT 1
");

mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);

$res = mysqli_stmt_get_result($stmt);

$car = mysqli_fetch_assoc($res);

if(!$car){
  echo '<div class="alert">Car not found.</div>';
  require_once __DIR__ . "/../includes/footer.php";
  exit();
}

$img = $car['image_path'] 
? e($car['image_path']) 
: "/carconnect/assets/images/default_car.jpg";
?>


<style>

.car-details-container{
    max-width:1100px;
    margin:40px auto;
    padding:20px;
}

.car-header{
    text-align:center;
    margin-bottom:30px;
}

.car-header h1{
    font-size:36px;
    color:#111827;
}


.car-layout{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:25px;
}


.car-image-card,
.info-card{
    background:white;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,0.12);
}


.car-image{
    width:100%;
    height:450px;
    object-fit:cover;
}


.car-content{
    padding:25px;
}


.price-box{
    background:#2563eb;
    color:white;
    padding:20px;
    border-radius:15px;
    margin-bottom:20px;
}


.price-box h2{
    font-size:32px;
    margin:0;
}


.quick-specs{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:15px;
    margin-top:20px;
}


.spec{
    background:#f8fafc;
    padding:15px;
    border-radius:12px;
    font-size:15px;
}


.section-title{
    margin-top:30px;
    margin-bottom:15px;
    font-size:22px;
}


.spec-table{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}


.spec-item{
    background:#f8fafc;
    padding:15px;
    border-radius:12px;
}


.seller-box{
    background:#f8fafc;
    padding:20px;
    border-radius:15px;
    margin-top:25px;
}


.badge{
    background:#22c55e;
    color:white;
    padding:5px 12px;
    border-radius:20px;
    font-size:13px;
}


.action-buttons{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    margin-top:25px;
}


.action-buttons .btn{
    flex:1;
    text-align:center;
    padding:14px;
    border-radius:12px;
}


@media(max-width:900px){

.car-layout{
grid-template-columns:1fr;
}

.car-image{
height:300px;
}

}

</style>


<div class="car-details-container">


<div class="car-header">

<h1>
🚗 <?php echo e($car['make']." ".$car['model']); ?>
</h1>

</div>



<div class="car-layout">


<!-- LEFT SIDE -->

<div>


<div class="car-image-card">

<img class="car-image"
src="<?php echo $img; ?>">


<div class="car-content">


<div class="price-box">

<p>Price</p>

<h2>
₹<?php echo number_format((float)$car['price']); ?>
</h2>

</div>



<h2 class="section-title">
🚘 Quick Details
</h2>


<div class="quick-specs">


<div class="spec">
📅 Year<br>
<b><?php echo e($car['year']); ?></b>
</div>


<div class="spec">
🛣 Mileage<br>
<b><?php echo number_format($car['mileage']); ?> km</b>
</div>


<div class="spec">
⛽ Fuel<br>
<b><?php echo e($car['fuel_type']); ?></b>
</div>


<div class="spec">
⚙ Transmission<br>
<b><?php echo e($car['transmission']); ?></b>
</div>


</div>




<h2 class="section-title">
📋 Specifications
</h2>


<div class="spec-table">


<div class="spec-item">
<strong>Brand</strong><br>
<?php echo e($car['make']); ?>
</div>


<div class="spec-item">
<strong>Model</strong><br>
<?php echo e($car['model']); ?>
</div>


<div class="spec-item">
<strong>Color</strong><br>
<?php echo e($car['color']); ?>
</div>


<div class="spec-item">
<strong>Owner</strong><br>
<?php echo e($car['owner_type']); ?>
</div>


<div class="spec-item">
<strong>Body Type</strong><br>
<?php echo e($car['body_type']); ?>
</div>


<div class="spec-item">
<strong>Seats</strong><br>
<?php echo e($car['seating_capacity']); ?> Seats
</div>


<div class="spec-item">
<strong>Location</strong><br>
📍 <?php echo e($car['location']); ?>
</div>


</div>




<h2 class="section-title">
📝 Description
</h2>


<p style="line-height:1.8;color:#555">

<?php echo nl2br(e($car['description'])); ?>

</p>
</br></br>
<a class="btn primary"
href="add_review.php?car_id=<?php echo intval($car['id']); ?>">
⭐ Add Review
</a>

</div>

</div>


</div>




<!-- RIGHT SIDE -->


<div>


<div class="info-card">

<div class="car-content">


<h2>
👤 Seller
</h2>


<div class="seller-box">


<h3>
<?php echo e($car['seller_name']); ?>
</h3>


<span class="badge">
✔ Verified Seller
</span>


</div>




<div class="action-buttons">


<a class="btn"
href="/carconnect/buyer/wishlist.php?add=<?php echo intval($car['id']); ?>">
❤️ Wishlist
</a>


<a class="btn primary"
href="/carconnect/buyer/checkout.php?id=<?php echo intval($car['id']); ?>">
💳 Buy Now
</a>


<a class="btn"
href="/carconnect/buyer/chat.php?car_id=<?php echo intval($car['id']); ?>&seller=<?php echo intval($car['seller_id']); ?>">
💬 Chat
</a>



</div>


</div>

</div>


</div>


</div>


</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>