<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/header.php";

/* ===================== */
/* TOTAL VIEWS */
/* ===================== */

$totalViews = mysqli_fetch_assoc(
  mysqli_query($conn,"SELECT COUNT(*) c FROM car_views")
)['c'];

/* ===================== */
/* TOP VIEWED CARS */
/* ===================== */

$topCars = mysqli_query($conn,"
SELECT c.make,c.model,COUNT(v.id) views
FROM car_views v
JOIN car_listings c ON c.id=v.car_id
GROUP BY v.car_id
ORDER BY views DESC
LIMIT 5
");

/* ===================== */
/* DAILY VIEWS */
/* ===================== */

$data = mysqli_query($conn,"
SELECT DATE(viewed_at) d, COUNT(*) total
FROM car_views
GROUP BY d
ORDER BY d ASC
");

$dates = [];
$views = [];

while($row=mysqli_fetch_assoc($data)){
  $dates[] = $row['d'];
  $views[] = $row['total'];
}
?>

<h1>👀 Car Views Analytics</h1>

<!-- ===================== -->
<!-- STATS -->
<!-- ===================== -->

<div class="card" style="max-width:500px">
<div class="p">
<div class="muted">Total Views</div>
<div style="font-size:28px;font-weight:900">
<?php echo $totalViews; ?>
</div>
</div>
</div>

<!-- ===================== -->
<!-- CHART -->
<!-- ===================== -->

<h2 style="margin-top:30px">Daily Views</h2>

<canvas id="viewsChart"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('viewsChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
            label: 'Views',
            data: <?php echo json_encode($views); ?>,
            borderWidth: 1
        }]
    }
});
</script>

<!-- ===================== -->
<!-- TOP CARS -->
<!-- ===================== -->

<h2 style="margin-top:30px">🔥 Most Viewed Cars</h2>

<table class="table">
<tr>
<th>Car</th>
<th>Views</th>
</tr>

<?php
if(mysqli_num_rows($topCars)>0){

while($c=mysqli_fetch_assoc($topCars)){
echo "<tr>
<td>{$c['make']} {$c['model']}</td>
<td>{$c['views']}</td>
</tr>";
}

}else{
echo "<tr><td colspan='2'>No data yet</td></tr>";
}
?>

</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>