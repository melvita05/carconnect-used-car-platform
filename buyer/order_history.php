<?php
require_once "../core/middleware.php";
requireRole("buyer");

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";

$buyerId = (int)$_SESSION['user_id'];

$stmt = mysqli_prepare($conn,"
SELECT 

o.id,
o.total_price,
o.order_status,
o.created_at,
c.make,
c.model,
s.name seller,
p.payment_status
FROM orders o
JOIN car_listings c ON c.id=o.car_id
JOIN sellers s ON s.id=o.seller_id
LEFT JOIN payments p ON p.order_id=o.id
WHERE o.buyer_id=?
ORDER BY o.id DESC
");

mysqli_stmt_bind_param($stmt,"i",$buyerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>

<h1>📦 My Orders</h1>

<table class="table">
<tr>
<th>ID</th>
<th>Car</th>
<th>Seller</th>
<th>Price</th>
<th>Status</th>
<th>Payment</th>
<th>Date</th>
<th>Action</th>
</tr>

<?php if(mysqli_num_rows($res)>0): ?>

<?php while($r=mysqli_fetch_assoc($res)): ?>

<?php
$statusColor = "#999";
if($r['order_status']=="completed") $statusColor="#16a34a";
if($r['order_status']=="pending")   $statusColor="#f59e0b";
if($r['order_status']=="cancelled") $statusColor="#ef4444";

$payColor = "#999";
if($r['payment_status']=="paid")    $payColor="#16a34a";
if($r['payment_status']=="pending") $payColor="#f59e0b";
if($r['payment_status']=="failed")  $payColor="#ef4444";
?>

<tr>

<td>
#<?php echo intval($r['id']); ?>
</td>


<td>
<?php echo e($r['make']." ".$r['model']); ?>
</td>


<td>
<?php echo e($r['seller']); ?>
</td>


<td>
₹<?php echo number_format((float)$r['total_price']); ?>
</td>


<td>

<span style="background:<?php echo $statusColor; ?>;
color:#fff;
padding:5px 12px;
border-radius:20px;
font-size:12px">

<?php echo ucfirst($r['order_status']); ?>

</span>

</td>



<td>

<span style="color:<?php echo $payColor; ?>;
font-weight:700">

<?php echo ucfirst($r['payment_status'] ?? 'N/A'); ?>

</span>

</td>



<td>

<?php echo date("d M Y", strtotime($r['created_at'])); ?>

</td>
<td>

<a class="btn primary"
href="order_details.php?id=<?php echo $r['id']; ?>">

View

</a>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="8" style="text-align:center">No orders yet</td>
</tr>

<?php endif; ?>

</table>

<?php require_once "../includes/footer.php"; ?>