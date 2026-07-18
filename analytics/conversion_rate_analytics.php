<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/header.php";

/* ===================== */
/* TOTAL ORDERS */
/* ===================== */

$sales = mysqli_fetch_assoc(
  mysqli_query($conn,"SELECT COUNT(*) c FROM orders")
)['c'];

/* ===================== */
/* TOTAL VIEWS */
/* ===================== */

$views = 0;

$res = mysqli_query($conn,"SHOW TABLES LIKE 'car_views'");
if($res && mysqli_num_rows($res)>0){
  $views = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) c FROM car_views")
  )['c'];
}

/* ===================== */
/* CONVERSION RATE */
/* ===================== */

$rate = $views>0 ? ($sales/$views)*100 : 0;

/* ===================== */
/* DAILY ORDERS (FIXED) */
/* ===================== */

$data = mysqli_query($conn,"
SELECT DATE(created_at) d,
COUNT(id) orders
FROM orders
GROUP BY d
ORDER BY d ASC
");

$dates = [];
$orders = [];

if($data){
while($row=mysqli_fetch_assoc($data)){
  $dates[] = $row['d'];
  $orders[] = $row['orders'];
}
}
?>

<h1>🔄 Conversion Analytics</h1>

<!-- STATS -->

<div class="grid" style="grid-template-columns:repeat(3,1fr);gap:20px">

<div class="card">
<div class="p">
<div class="muted">Total Views</div>
<div style="font-size:26px;font-weight:900">
<?php echo $views; ?>
</div>
</div>
</div>

<div class="card">
<div class="p">
<div class="muted">Orders</div>
<div style="font-size:26px;font-weight:900">
<?php echo $sales; ?>
</div>
</div>
</div>

<div class="card">
<div class="p">
<div class="muted">Conversion Rate</div>
<div style="font-size:26px;font-weight:900;color:#00d2ff">
<?php echo number_format($rate,2); ?>%
</div>
</div>
</div>

</div>

<!-- CHART -->

<h2 style="margin-top:30px">Orders Trend</h2>

<canvas id="conversionChart"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('conversionChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
            label: 'Orders',
            data: <?php echo json_encode($orders); ?>,
            borderWidth: 2,
            tension: 0.3
        }]
    }
});
</script>

<!-- INFO -->

<?php if($views == 0): ?>
<div class="alert" style="margin-top:20px">
⚠️ Conversion rate requires <b>car_views tracking</b>.
</div>
<?php endif; ?>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>