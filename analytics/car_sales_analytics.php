<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

/* ===================== */
/* TOTAL REVENUE */
/* ===================== */

$total = mysqli_fetch_assoc(
  mysqli_query($conn,"SELECT IFNULL(SUM(total_price),0) s FROM orders")
)['s'];

$totalOrders = mysqli_fetch_assoc(
  mysqli_query($conn,"SELECT COUNT(*) c FROM orders")
)['c'];
$carsSold = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) c FROM car_listings WHERE status='sold'")
)['c'];

$totalUsers = mysqli_fetch_assoc(
    mysqli_query($conn,"
    SELECT
    (SELECT COUNT(*) FROM buyers) +
    (SELECT COUNT(*) FROM sellers) AS total
    ")
)['total'];


$approved = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) c
FROM car_listings
WHERE status='approved'
"))['c'];

$sold = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) c
FROM car_listings
WHERE status='sold'
"))['c'];

$pending = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) c
FROM car_listings
WHERE status='pending'
"))['c'];

/* ===================== */
/* MONTHLY DATA */
/* ===================== */

$data = mysqli_query($conn,"
SELECT
DATE_FORMAT(created_at,'%Y-%m') AS month,
SUM(total_price) AS total
FROM orders
GROUP BY month
ORDER BY month ASC
");
/* ===================== */
/* MONTHLY DATA */
/* ===================== */

// $data = mysqli_query($conn,"
// SELECT 
// DATE_FORMAT(created_at,'%Y-%m') AS month,
// SUM(total_price) AS total
// FROM orders
// GROUP BY month
// ORDER BY month ASC
// ");

$months = [];
$sales = [];

while($row=mysqli_fetch_assoc($data)){
  $months[] = $row['month'];
  $sales[] = $row['total'];
}
?>

<h1>📊 Car Sales Analytics</h1>

<!-- STATS -->

<div class="stats-grid">

<div class="stat-card revenue">
<h2>₹<?php echo number_format($total); ?></h2>
<p>Total Revenue</p>
</div>

<div class="stat-card orders">
<h2><?php echo $totalOrders; ?></h2>
<p>Total Orders</p>
</div>

<div class="stat-card sold">
<h2><?php echo $carsSold; ?></h2>
<p>Cars Sold</p>
</div>

<div class="stat-card users">
<h2><?php echo $totalUsers; ?></h2>
<p>Registered Users</p>
</div>

</div>

<!-- CHART -->
<!-- CHARTS -->

<div style="display:grid;grid-template-columns:2fr 1fr;gap:25px;margin-top:30px;">

    <div class="card analytics-card">

        <h2>📈 Monthly Sales</h2>

        <canvas id="salesChart"></canvas>

    </div>

    <div class="card analytics-card">

        <h2>🚗 Car Status</h2>

        <canvas id="statusChart"></canvas>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById("salesChart");

new Chart(ctx,{
    type:"line",
    data:{
        labels: <?php echo json_encode($months); ?>,
        datasets:[{
            label:"Sales (₹)",
            data: <?php echo json_encode($sales); ?>,
            borderWidth:3,
            fill:false,
            tension:.3
        }]
    },
    options:{
        responsive:true
    }
});

new Chart(document.getElementById("statusChart"),{

    type:"doughnut",

    data:{

        labels:["Approved","Sold","Pending"],

        datasets:[{

            data:[
                <?php echo $approved; ?>,
                <?php echo $sold; ?>,
                <?php echo $pending; ?>
            ]

        }]

    },

    options:{
        responsive:true
    }

});

</script>
<!-- RECENT SALES -->

<h2 style="margin-top:30px">Recent Sales</h2>

<table class="table">

<tr>
<th>Order ID</th>
<th>Amount</th>
<th>Date</th>
</tr>

<?php
$res = mysqli_query($conn,"
SELECT id,total_price,created_at
FROM orders
ORDER BY created_at DESC
LIMIT 5
");

if($res && mysqli_num_rows($res)>0){

while($o=mysqli_fetch_assoc($res)){

echo "
<tr>
<td>#{$o['id']}</td>
<td>₹".number_format($o['total_price'])."</td>
<td>".date("d M Y",strtotime($o['created_at']))."</td>
</tr>
";

}

}else{
echo "<tr><td colspan='3'>No sales yet</td></tr>";
}
?>

</table>
<style>

.stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin:25px 0;
}

.stat-card{
    padding:30px;
    border-radius:15px;
    color:white;
    text-align:center;
    box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.stat-card h2{
    margin:0;
    font-size:34px;
}

.stat-card p{
    margin-top:10px;
    font-size:15px;
    font-weight:600;
}

.revenue{
    background:linear-gradient(135deg,#0ea5e9,#2563eb);
}

.orders{
    background:linear-gradient(135deg,#7c3aed,#9333ea);
}

.sold{
    background:linear-gradient(135deg,#16a34a,#22c55e);
}

.users{
    background:linear-gradient(135deg,#f97316,#fb923c);
}

.analytics-card{
    padding:25px;
    margin-top:30px;
}

canvas{
    max-height:350px;
}

</style>


<?php require_once __DIR__ . "/../includes/footer.php"; ?>