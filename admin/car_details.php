<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$id = (int)($_GET['id'] ?? 0);

/* ===================== */
/* FETCH CAR */
/* ===================== */

$stmt = mysqli_prepare($conn,"
SELECT
c.*,
s.name AS seller_name,
cc.name AS category_name
FROM car_listings c
JOIN sellers s ON s.id=c.seller_id
LEFT JOIN car_categories cc ON cc.id=c.category_id
WHERE c.id=?
LIMIT 1
");

mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$res=mysqli_stmt_get_result($stmt);
$car=mysqli_fetch_assoc($res);

if(!$car){
    echo "<div class='alert'>Car not found</div>";
    require_once __DIR__ . "/../includes/footer.php";
    exit();
}

$img=$car['image_path'] ?: "/carconnect/assets/images/default_car.jpg";

/* STATUS COLOR */
$statusColor="#888";

if($car['status']=="approved") $statusColor="#16a34a";
if($car['status']=="pending")  $statusColor="#f59e0b";
if($car['status']=="sold")     $statusColor="#9333ea";
if($car['status']=="rejected") $statusColor="#ef4444";
?>

<h1 style="margin-bottom:25px">
🚗 <?php echo e($car['make']." ".$car['model']); ?>
</h1>

<div class="card" style="max-width:1100px;margin:auto">

<img
src="<?php echo e($img); ?>"
style="
width:100%;
height:420px;
object-fit:cover;
border-radius:12px 12px 0 0;
">

<div class="p">

<h2 style="color:#2563eb;font-size:34px">
₹<?php echo number_format((float)$car['price']); ?>
</h2>

<p class="muted" style="margin-bottom:25px">
<?php echo e($car['year']); ?> •
<?php echo e($car['mileage']); ?> km •
<?php echo e($car['fuel_type']); ?> •
<?php echo e($car['transmission']); ?>
</p>

<table class="table">

<tr>
<th width="230">Category</th>
<td><?php echo e($car['category_name']); ?></td>
</tr>

<tr>
<th>Brand</th>
<td><?php echo e($car['make']); ?></td>
</tr>

<tr>
<th>Model</th>
<td><?php echo e($car['model']); ?></td>
</tr>

<tr>
<th>Manufacturing Year</th>
<td><?php echo e($car['year']); ?></td>
</tr>

<tr>
<th>Price</th>
<td>₹<?php echo number_format((float)$car['price']); ?></td>
</tr>

<tr>
<th>Mileage</th>
<td><?php echo number_format((int)$car['mileage']); ?> km</td>
</tr>

<tr>
<th>Fuel Type</th>
<td><?php echo e($car['fuel_type']); ?></td>
</tr>

<tr>
<th>Transmission</th>
<td><?php echo e($car['transmission']); ?></td>
</tr>

<tr>
<th>Color</th>
<td><?php echo e($car['color']); ?></td>
</tr>

<tr>
<th>Owner Type</th>
<td><?php echo e($car['owner_type']); ?></td>
</tr>

<tr>
<th>Body Type</th>
<td><?php echo e($car['body_type']); ?></td>
</tr>

<tr>
<th>Seating Capacity</th>
<td><?php echo e($car['seating_capacity']); ?> Seats</td>
</tr>

<tr>
<th>Location</th>
<td><?php echo e($car['location']); ?></td>
</tr>

<tr>
<th>Seller</th>
<td><?php echo e($car['seller_name']); ?></td>
</tr>

<tr>
<th>Status</th>

<td>

<span style="
background:<?php echo $statusColor; ?>;
color:white;
padding:6px 14px;
border-radius:20px;
font-weight:bold;
">

<?php echo ucfirst($car['status']); ?>

</span>

</td>

</tr>

<tr>
<th>Description</th>

<td>

<?php echo nl2br(e($car['description'])); ?>

</td>

</tr>

<?php if(!empty($car['created_at'])): ?>

<tr>
<th>Listed On</th>

<td>

<?php echo date("d M Y, h:i A",strtotime($car['created_at'])); ?>

</td>

</tr>

<?php endif; ?>

</table>

<div style="margin-top:25px">

<a
class="btn"
href="manage_cars.php">
⬅ Back to Cars
</a>

</div>

</div>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>