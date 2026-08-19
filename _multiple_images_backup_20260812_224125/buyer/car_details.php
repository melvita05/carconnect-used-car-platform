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
s.id AS seller_id,
s.phone

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

body{
    background:#f4f7fb;
}

.car-details-container{
    max-width:1250px;
    margin:40px auto;
    padding:20px;
}

.car-header{
    text-align:center;
    margin-bottom:25px;
}

.car-header h1{
    font-size:38px;
    color:#111827;
    font-weight:700;
}

.car-layout{
    display:grid;
    grid-template-columns:2.2fr 1fr;
    gap:30px;
    align-items:start;
}

.car-image-card,
.info-card{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    transition:.3s;
}

.car-image-card:hover,
.info-card:hover{
    transform:translateY(-5px);
    box-shadow:0 18px 45px rgba(0,0,0,.12);
}

.car-image{
    width:100%;
    height:520px;
    object-fit:cover;
}

.car-content{
    padding:28px;
}

.price-box{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
    border-radius:15px;
    padding:25px;
    text-align:center;
    margin-bottom:25px;
}

.price-box p{
    margin:0;
    opacity:.9;
}

.price-box h2{
    margin:8px 0 0;
    font-size:36px;
}

.section-title{
    margin:30px 0 15px;
    font-size:22px;
    color:#111827;
    border-left:5px solid #2563eb;
    padding-left:12px;
}

.quick-specs,
.spec-table{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:15px;
}

.spec,
.spec-item{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:15px;
    padding:18px;
    transition:.3s;
}

.spec:hover,
.spec-item:hover{
    background:#eff6ff;
    border-color:#2563eb;
}

.spec b,
.spec-item strong{
    display:block;
    margin-top:6px;
    font-size:17px;
    color:#111827;
}

.seller-box{
    text-align:center;
    background:#f8fafc;
    border-radius:15px;
    padding:25px;
    border:1px solid #e5e7eb;
}

.seller-box h3{
    margin-bottom:10px;
}

.seller-box p{
    margin:15px 0;
    font-size:17px;
}

.badge{
    display:inline-block;
    background:#16a34a;
    color:#fff;
    padding:8px 18px;
    border-radius:30px;
    font-size:14px;
    margin-top:10px;
}

.action-buttons{
    display:flex;
    flex-direction:column;
    gap:15px;
    margin-top:25px;
}

.action-buttons .btn,
.seller-box .btn{
    width:100%;
    text-align:center;
    padding:14px;
    border-radius:12px;
    font-size:16px;
    font-weight:600;
}

.description{
    background:#fafafa;
    padding:20px;
    border-radius:15px;
    border:1px solid #e5e7eb;
    line-height:1.8;
    color:#444;
}

@media(max-width:900px){

.car-layout{
    grid-template-columns:1fr;
}

.car-image{
    height:300px;
}

.quick-specs,
.spec-table{
    grid-template-columns:1fr;
}

}
.btn{
    transition:.3s;
}

.btn:hover{
    transform:translateY(-2px);
}
</style>


<div class="car-details-container">


<div class="car-header">

<h1>
🚗 <?php echo e($car['make']); ?>
<span style="color:#2563eb;">
<?php echo e($car['model']); ?>
</span>
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





<h2 class="section-title">📝 Description</h2>

<div class="description">
<?php echo nl2br(e($car['description'])); ?>
</div>

<div style="margin-top:20px;">
<a class="btn primary"
href="add_review.php?car_id=<?php echo intval($car['id']); ?>">
⭐ Add Review
</a>
</div>
</div>

</div>


</div>




<!-- RIGHT SIDE -->


<div>


<div class="info-card">

<div class="car-content">

<h2 class="section-title">
👤 Seller Information
</h2>
<div class="seller-box">

<img src="/carconnect/assets/images/user.png"
style="width:90px;height:90px;border-radius:50%;margin-bottom:15px;">
<h3><?php echo e($car['seller_name']); ?></h3>

<p>📞 <?php echo e($car['phone']); ?></p>

<a class="btn primary"
style="display:block;margin-top:15px;background:#16a34a;"
href="tel:<?php echo e($car['phone']); ?>">
📞 Call Seller
</a>

<br><br>

<span class="badge">
✔ Verified Seller
</span>

</div>

</div>

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