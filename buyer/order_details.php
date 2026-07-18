<?php
require_once "../core/middleware.php";
requireRole("buyer");

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";

$id = (int)($_GET['id'] ?? 0);
$buyerId = $_SESSION['user_id'];

$res = mysqli_query($conn,"
SELECT
o.*,
c.make,
c.model,
c.year,
c.fuel_type,
c.transmission,
s.name AS seller,
p.payment_method,
p.payment_status
FROM orders o
JOIN car_listings c ON c.id=o.car_id
JOIN sellers s ON s.id=o.seller_id
LEFT JOIN payments p ON p.order_id=o.id
WHERE o.id=$id AND o.buyer_id=$buyerId
");

$order = mysqli_fetch_assoc($res);
?>

<h1>Order Details</h1>

<?php if($order): ?>

<div class="card p">

<h2>
<?php echo e($order['make']." ".$order['model']); ?>
</h2>

<hr>

<p><strong>Seller:</strong> <?php echo e($order['seller']); ?></p>

<p><strong>Year:</strong> <?php echo e($order['year']); ?></p>

<p><strong>Fuel:</strong> <?php echo e($order['fuel_type']); ?></p>

<p><strong>Transmission:</strong> <?php echo e($order['transmission']); ?></p>

<p><strong>Amount:</strong>
₹<?php echo number_format($order['total_price']); ?>
</p>

<p><strong>Order Status:</strong>

<?php

$statusColor="#999";

if($order['order_status']=="pending") $statusColor="#f59e0b";
if($order['order_status']=="completed") $statusColor="#16a34a";
if($order['order_status']=="cancelled") $statusColor="#ef4444";

?>

<span style="
background:<?php echo $statusColor; ?>;
color:#fff;
padding:5px 12px;
border-radius:20px;
">

<?php echo ucfirst($order['order_status']); ?>

</span>

</p>

<p><strong>Payment:</strong>

<?php echo ucfirst($order['payment_status']); ?>

</p>

<p><strong>Payment Method:</strong>

<?php echo e($order['payment_method']); ?>

</p>

</div>

<?php else: ?>

<p class="alert">Order not found</p>

<?php endif; ?>

<?php require_once "../includes/footer.php"; ?>