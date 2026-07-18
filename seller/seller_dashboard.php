<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = (int)$_SESSION['user_id'];

/* ===================== */
/* STATS */
/* ===================== */

$totalCars = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) c FROM car_listings WHERE seller_id=$sellerId
"))['c'];

$approvedCars = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) c FROM car_listings WHERE seller_id=$sellerId AND status='approved'
"))['c'];

$pendingCars = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) c FROM car_listings WHERE seller_id=$sellerId AND status='pending'
"))['c'];

$soldCars = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) c FROM car_listings WHERE seller_id=$sellerId AND status='sold'
"))['c'];

$totalOrders = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) c FROM orders WHERE seller_id=$sellerId
"))['c'];

$totalEarnings = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT IFNULL(SUM(total_price),0) s FROM orders WHERE seller_id=$sellerId
"))['s'];
?>

<h1>📊 Seller Dashboard</h1>

<p class="muted">Manage your listings, orders, buyer messages and reviews.</p>

<!-- ACTION BUTTONS -->
<div style="display:flex;gap:12px;flex-wrap:wrap;margin:20px 0">

<a class="btn primary" href="/carconnect/seller/add_car.php">➕ Add Car</a>
<a class="btn" href="/carconnect/seller/manage_listings.php">📋 Manage Listings</a>
<a class="btn" href="/carconnect/seller/search_cars.php">🔍 Search Cars</a>
<a class="btn" href="/carconnect/seller/view_orders.php">📦 Orders</a>
<a class="btn" href="/carconnect/seller/view_reviews.php">⭐ Reviews</a>

<!-- ✅ NEW ADD (nothing removed) -->
<a class="btn" href="/carconnect/seller/messages.php">💬 Messages</a>

</div>

<!-- STATS -->
<div class="grid" style="grid-template-columns:repeat(4,1fr);gap:20px;margin-top:20px">

<div class="card"><div class="p">
<div class="muted">Total Cars</div>
<div style="font-size:28px;font-weight:900"><?php echo intval($totalCars); ?></div>
</div></div>

<div class="card"><div class="p">
<div class="muted">Approved</div>
<div style="font-size:28px;font-weight:900;color:#10b981"><?php echo intval($approvedCars); ?></div>
</div></div>

<div class="card"><div class="p">
<div class="muted">Pending</div>
<div style="font-size:28px;font-weight:900;color:#f59e0b"><?php echo intval($pendingCars); ?></div>
</div></div>

<div class="card"><div class="p">
<div class="muted">Sold</div>
<div style="font-size:28px;font-weight:900;color:#2563eb"><?php echo intval($soldCars); ?></div>
</div></div>

<div class="card"><div class="p">
<div class="muted">Orders</div>
<div style="font-size:28px;font-weight:900"><?php echo intval($totalOrders); ?></div>
</div></div>

<div class="card"><div class="p">
<div class="muted">Earnings</div>
<div style="font-size:28px;font-weight:900;color:#06b6d4">
₹<?php echo number_format((float)$totalEarnings); ?>
</div>
</div></div>

</div>

<!-- ===================== -->
<!-- YOUR LISTINGS -->
<!-- ===================== -->

<h2 style="margin-top:35px">🚗 Your Recent Listings</h2>

<table class="table">

<tr>
<th>Car</th>
<th>Price</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php

$stmt = mysqli_prepare($conn,"
SELECT id,make,model,price,status
FROM car_listings
WHERE seller_id=?
ORDER BY created_at DESC
LIMIT 6
");

mysqli_stmt_bind_param($stmt,"i",$sellerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($res) > 0){

while($c=mysqli_fetch_assoc($res)){

$status = e($c['status']);
$statusColor = "#6b7280";

if($c['status'] === "approved") $statusColor = "#10b981";
if($c['status'] === "pending") $statusColor = "#f59e0b";
if($c['status'] === "sold") $statusColor = "#2563eb";

echo "
<tr>
<td>".e($c['make'])." ".e($c['model'])."</td>
<td>₹".number_format((float)$c['price'])."</td>
<td>
<span style='background:$statusColor;color:#fff;padding:5px 10px;border-radius:20px;font-size:12px;font-weight:700'>
".ucfirst($status)."
</span>
</td>
<td>
<a class='btn' href='/carconnect/seller/edit_car.php?id=".intval($c['id'])."'>
✏️ Edit
</a>
</td>
</tr>
";
}

}else{
echo "<tr><td colspan='4' style='text-align:center'>No listings yet</td></tr>";
}
?>

</table>

<!-- ===================== -->
<!-- RECENT ORDERS -->
<!-- ===================== -->

<h2 style="margin-top:35px">📦 Recent Orders</h2>

<table class="table">

<tr>
<th>Order</th>
<th>Car</th>
<th>Buyer</th>
<th>Total</th>
<th>Status</th>
</tr>

<?php

$stmt = mysqli_prepare($conn,"
SELECT 
o.id,
o.total_price,
o.order_status,
c.make,
c.model,
b.name buyer_name
FROM orders o
JOIN car_listings c ON c.id=o.car_id
JOIN buyers b ON b.id=o.buyer_id
WHERE o.seller_id=?
ORDER BY o.id DESC
LIMIT 5
");

mysqli_stmt_bind_param($stmt,"i",$sellerId);
mysqli_stmt_execute($stmt);
$orders = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($orders) > 0){

while($o=mysqli_fetch_assoc($orders)){

echo "
<tr>
<td>#".intval($o['id'])."</td>
<td>".e($o['make'])." ".e($o['model'])."</td>
<td>".e($o['buyer_name'])."</td>
<td>₹".number_format((float)$o['total_price'])."</td>
<td>".e(ucfirst($o['order_status']))."</td>
</tr>
";
}

}else{
echo "<tr><td colspan='5' style='text-align:center'>No orders yet</td></tr>";
}
?>

</table>

<!-- ===================== -->
<!-- BUYER MESSAGES (OPTIMIZED) -->
<!-- ===================== -->

<h2 style="margin-top:35px">💬 Buyer Messages</h2>

<table class="table">

<tr>
<th>Buyer</th>
<th>Car</th>
<th>Last Message</th>
<th>Action</th>
</tr>

<?php

$stmt = mysqli_prepare($conn,"
SELECT 
b.id AS buyer_id,
b.name AS buyer_name,
c.id AS car_id,
c.make,
c.model,
m.message,
MAX(m.created_at) AS last_time
FROM messages m
JOIN car_listings c ON c.id = m.car_id
JOIN buyers b ON b.id = IF(m.sender_id=?, m.receiver_id, m.sender_id)
WHERE c.seller_id = ?
AND (m.sender_id = ? OR m.receiver_id = ?)
GROUP BY c.id, b.id
ORDER BY last_time DESC
");

mysqli_stmt_bind_param($stmt,"iiii",$sellerId,$sellerId,$sellerId,$sellerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($res)>0){

while($row = mysqli_fetch_assoc($res)){

echo "
<tr>
<td>".e($row['buyer_name'])."</td>
<td>".e($row['make'])." ".e($row['model'])."</td>
<td>".mb_strimwidth(e($row['message']),0,45,'...')."</td>
<td>
<a class='btn primary'
href='/carconnect/seller/chat.php?buyer=".$row['buyer_id']."&car_id=".$row['car_id']."'>
💬 Open Chat
</a>
</td>
</tr>
";
}

}else{
echo "<tr><td colspan='4' style='text-align:center'>No messages yet</td></tr>";
}
?>

</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>