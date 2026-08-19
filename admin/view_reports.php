<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

/* ===================== */
/* STATS */
/* ===================== */

$totalSales = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT IFNULL(SUM(total_price),0) s FROM orders")
)['s'];

$totalOrders = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) c FROM orders")
)['c'];

$totalCars = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) c FROM car_listings")
)['c'];

$totalBuyers = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) c FROM buyers")
)['c'];

$totalSellers = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) c FROM sellers")
)['c'];

/* ===================== */
/* TOP CARS */
/* ===================== */

$topCars = mysqli_query($conn,"
SELECT c.make,c.model,COUNT(*) cnt
FROM orders o
JOIN car_listings c ON c.id=o.car_id
GROUP BY o.car_id
ORDER BY cnt DESC
LIMIT 5
");
?>

<h1>📊 Admin Reports</h1>

<!-- ===================== -->
<!-- STATS CARDS -->
<!-- ===================== -->

<div class="grid" style="grid-template-columns:repeat(4,1fr);gap:20px;margin-top:20px">

<div class="card"><div class="p">
<div class="muted">Total Sales</div>
<div style="font-size:26px;font-weight:900">₹<?php echo number_format((float)$totalSales); ?></div>
</div></div>

<div class="card"><div class="p">
<div class="muted">Orders</div>
<div style="font-size:26px;font-weight:900"><?php echo $totalOrders; ?></div>
</div></div>

<div class="card"><div class="p">
<div class="muted">Cars Listed</div>
<div style="font-size:26px;font-weight:900"><?php echo $totalCars; ?></div>
</div></div>

<div class="card"><div class="p">
<div class="muted">Users</div>
<div style="font-size:26px;font-weight:900">
<?php echo $totalBuyers + $totalSellers; ?>
</div>
</div></div>

</div>

<!-- ===================== -->
<!-- TOP CARS -->
<!-- ===================== -->

<h2 style="margin-top:30px">🔥 Top Selling Cars</h2>

<table class="table">

<tr>
<th>Car</th>
<th>Orders</th>
</tr>

<?php
if(mysqli_num_rows($topCars)>0){

while($r=mysqli_fetch_assoc($topCars)){

echo "
<tr>
<td>".e($r['make'])." ".e($r['model'])."</td>
<td>".intval($r['cnt'])."</td>
</tr>
";

}

}else{
echo "<tr><td colspan='2'>No sales data available</td></tr>";
}
?>

</table>

<!-- ===================== -->
<!-- EXTRA LINKS -->
<!-- ===================== -->

<h2 style="margin-top:30px">📈 Analytics</h2>

<div style="display:flex;gap:10px;flex-wrap:wrap">

<a class="btn" href="/carconnect/analytics/car_sales_analytics.php">
Car Sales
</a>

<a class="btn" href="/carconnect/analytics/user_activity_analytics.php">
User Activity
</a>
<!-- 
<a class="btn" href="/carconnect/analytics/conversion_rate_analytics.php">
Conversion Rate
</a>

 <a class="btn" href="/carconnect/analytics/car_views_analytics.php">
Car Views
</a>   -->

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>