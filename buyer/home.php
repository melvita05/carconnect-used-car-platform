<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("buyer");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$buyerId = (int)$_SESSION['user_id'];

/* ===================== */
/* STATS */
/* ===================== */

$stmt1 = mysqli_prepare($conn,"SELECT COUNT(*) FROM orders WHERE buyer_id=?");
mysqli_stmt_bind_param($stmt1,"i",$buyerId);
mysqli_stmt_execute($stmt1);
mysqli_stmt_bind_result($stmt1,$totalOrders);
mysqli_stmt_fetch($stmt1);
mysqli_stmt_close($stmt1);

$stmt2 = mysqli_prepare($conn,"SELECT COUNT(*) FROM wishlist WHERE buyer_id=?");
mysqli_stmt_bind_param($stmt2,"i",$buyerId);
mysqli_stmt_execute($stmt2);
mysqli_stmt_bind_result($stmt2,$totalWishlist);
mysqli_stmt_fetch($stmt2);
mysqli_stmt_close($stmt2);

/* ===================== */
/* BUYER REVIEWS */
/* ===================== *//* ===================== */
/* FETCH CUSTOMER REVIEWS */
/* ===================== */

$reviewStmt = mysqli_prepare($conn,"
SELECT 
r.rating,
r.comment,
r.created_at,
b.name,
c.make,
c.model

FROM reviews r

JOIN buyers b
ON b.id = r.user_id

JOIN car_listings c
ON c.id = r.car_id

ORDER BY r.created_at DESC

LIMIT 6
");


mysqli_stmt_execute($reviewStmt);

$reviewResult = mysqli_stmt_get_result($reviewStmt);
?>

<h1>👤 Buyer Dashboard</h1>

<p class="muted">Welcome back! Manage your cars, wishlist and orders.</p>

<!-- ===================== -->
<!-- ACTION BUTTONS -->
<!-- ===================== -->

<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:25px">

<a class="btn primary" href="car_listings.php">🚗 Browse Cars</a>

<a class="btn" href="wishlist.php">❤️ Wishlist</a>

<a class="btn" href="order_history.php">📦 Orders</a>

<a class="btn" href="messages.php">💬 Messages</a>

<a class="btn" href="my_reviews.php">⭐ My Reviews</a>

<!-- ✅ NEW CONTACT BUTTON -->
<a class="btn" href="contact.php">📞 Contact Admin</a>

</div>

<!-- ===================== -->
<!-- STATS -->
<!-- ===================== -->

<div class="grid" style="grid-template-columns:repeat(2,1fr);gap:20px">

<div class="card">
<div class="p">
<div class="muted">Total Orders</div>
<div style="font-size:30px;font-weight:900;color:#2563eb">
<?php echo intval($totalOrders); ?>
</div>
</div>
</div>

<div class="card">
<div class="p">
<div class="muted">Wishlist Items</div>
<div style="font-size:30px;font-weight:900;color:#ec4899">
<?php echo intval($totalWishlist); ?>
</div>
</div>
</div>

</div>

<!-- ===================== -->
<!-- RECENT ORDERS -->
<!-- ===================== -->

<h2 style="margin-top:35px">📦 Recent Orders</h2>

<table class="table">

<tr>
<th>ID</th>
<th>Car</th>
<th>Price</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php

$stmt = mysqli_prepare($conn,"
SELECT o.id,o.total_price,o.order_status,
c.make,c.model,c.id AS car_id
FROM orders o
JOIN car_listings c ON c.id=o.car_id
WHERE o.buyer_id=?
ORDER BY o.id DESC
LIMIT 5
");

mysqli_stmt_bind_param($stmt,"i",$buyerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($res)>0){

while($o=mysqli_fetch_assoc($res)){

$statusColor = "#999";

if($o['order_status']=="completed") $statusColor="#16a34a";
if($o['order_status']=="pending") $statusColor="#f59e0b";
if($o['order_status']=="cancelled") $statusColor="#ef4444";

echo "
<tr>

<td>#".intval($o['id'])."</td>

<td>".e($o['make'])." ".e($o['model'])."</td>

<td>₹".number_format((float)$o['total_price'])."</td>

<td>
<span style='
background:$statusColor;
color:#fff;
padding:5px 12px;
border-radius:20px;
font-size:12px;
font-weight:700
'>
".ucfirst($o['order_status'])."
</span>
</td>

<td>
<a class='btn'
href='/carconnect/buyer/car_details.php?id=".$o['car_id']."'>
View
</a>
</td>

</tr>
";

}

}else{

echo "
<tr>
<td colspan='5' style='text-align:center'>
No recent orders 🚗
</td>
</tr>
";

}

?>

</table>

<!-- ===================== -->
<!-- MY REVIEWS -->
<!-- ===================== -->

<section class="home-reviews">

<h2>⭐ Customer Reviews</h2>


<div class="review-container">


<?php while($review=mysqli_fetch_assoc($reviewResult)): ?>


<div class="review-card">


<h3>
<?php echo e($review['name']); ?>
</h3>


<div class="stars">

<?php echo str_repeat("⭐",$review['rating']); ?>

</div>


<p>
"<?php echo e($review['comment']); ?>"
</p>


<small>
🚗 
<?php echo e($review['make']." ".$review['model']); ?>
</small>


</div>


<?php endwhile; ?>


</div>

</section>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>