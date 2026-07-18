<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

/* ===================== */
/* SAFE COUNT FUNCTION */
/* ===================== */

function safeCount($conn, $sql){
    $res = mysqli_query($conn, $sql);
    if($res){
        $row = mysqli_fetch_assoc($res);
        return $row ? (int)$row['c'] : 0;
    }
    return 0;
}

/* ===================== */
/* STATS */
/* ===================== */

$pending      = safeCount($conn,"SELECT COUNT(*) c FROM car_listings WHERE status='pending'");
$approved     = safeCount($conn,"SELECT COUNT(*) c FROM car_listings WHERE status='approved'");
$sold         = safeCount($conn,"SELECT COUNT(*) c FROM car_listings WHERE status='sold'");
$totalBuyers  = safeCount($conn,"SELECT COUNT(*) c FROM buyers");
$totalSellers = safeCount($conn,"SELECT COUNT(*) c FROM sellers");
$totalOrders  = safeCount($conn,"SELECT COUNT(*) c FROM orders");
$totalCategories = safeCount($conn,"SELECT COUNT(*) c FROM car_categories");

/* ✅ NEW CONTACT COUNT */
$totalContacts = safeCount($conn,"SELECT COUNT(*) c FROM contact_messages");

$salesRes = mysqli_query($conn,"SELECT IFNULL(SUM(total_price),0) s FROM orders");
$totalSales = $salesRes ? mysqli_fetch_assoc($salesRes)['s'] : 0;
?>

<h1>🚀 Admin Dashboard</h1>

<?php if($pending > 0): ?>
<div class="alert">
⚠️ <?php echo intval($pending); ?> cars pending approval
</div>
<?php endif; ?>

<!-- ===================== -->
<!-- QUICK ACTIONS -->
<!-- ===================== -->

<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px">

<a class="btn primary" href="/carconnect/admin/manage_cars.php">🚗 Manage Cars</a>

<a class="btn" href="/carconnect/admin/manage_categories.php">🏷️ Categories</a>

<a class="btn" href="/carconnect/admin/add_car.php">➕ Add Car</a>

<a class="btn" href="/carconnect/cars.php">🔍 Browse Cars</a>

<a class="btn" href="/carconnect/admin/manage_users.php">👥 Users</a>

<a class="btn" href="/carconnect/admin/manage_orders.php">📦 Orders</a>

<a class="btn" href="/carconnect/admin/manage_payments.php">💳 Payments</a>

<a class="btn" href="/carconnect/admin/manage_reviews.php">⭐ Reviews</a>

<!-- ✅ NEW BUTTON -->
<a class="btn" href="/carconnect/admin/view_contacts.php">
📞 Contact Messages
</a>

<a class="btn" href="/carconnect/admin/view_reports.php">📊 Reports</a>

</div>

<!-- ===================== -->
<!-- STATS CARDS -->
<!-- ===================== -->

<div class="grid" style="grid-template-columns:repeat(4,1fr);gap:20px">

<?php
$cards = [
["Pending Cars",$pending,"#f59e0b"],
["Approved Cars",$approved,"#16a34a"],
["Sold Cars",$sold,"#2563eb"],
["Total Users",$totalBuyers + $totalSellers,"#3b82f6"],
["Total Buyers",$totalBuyers,"#06b6d4"],
["Total Sellers",$totalSellers,"#8b5cf6"],
["Orders",$totalOrders,"#ef4444"],
["Revenue","₹".number_format((float)$totalSales),"#06b6d4"],
["Categories",$totalCategories,"#f97316"],
["Contact Messages",$totalContacts,"#22c55e"]
];

foreach($cards as $c){
echo "
<div class='card'>
<div class='p'>
<div class='muted'>{$c[0]}</div>
<div style='font-size:26px;font-weight:900;color:{$c[2]}'>
{$c[1]}
</div>
</div>
</div>
";
}
?>

</div>

<!-- ===================== -->
<!-- TOP SELLING CARS -->
<!-- ===================== -->

<h2 style="margin-top:30px">🔥 Top Selling Cars</h2>

<table class="table">
<tr>
<th>Car</th>
<th>Orders</th>
</tr>

<?php
$top = mysqli_query($conn,"
SELECT c.make,c.model,COUNT(*) cnt
FROM orders o
JOIN car_listings c ON c.id=o.car_id
GROUP BY o.car_id,c.make,c.model
ORDER BY cnt DESC
LIMIT 5
");

if($top && mysqli_num_rows($top)>0){
while($t=mysqli_fetch_assoc($top)){
echo "<tr>
<td>".e($t['make'])." ".e($t['model'])."</td>
<td>".$t['cnt']."</td>
</tr>";
}
}else{
echo "<tr><td colspan='2'>No data</td></tr>";
}
?>
</table>

<!-- ===================== -->
<!-- RECENT ORDERS -->
<!-- ===================== -->

<h2 style="margin-top:30px">📦 Recent Orders</h2>

<table class="table">

<tr>
<th>ID</th>
<th>Car</th>
<th>Buyer</th>
<th>Status</th>
<th>Price</th>
</tr>

<?php
$res = mysqli_query($conn,"
SELECT o.id,o.total_price,o.order_status,c.make,c.model,b.name buyer
FROM orders o
JOIN car_listings c ON c.id=o.car_id
JOIN buyers b ON b.id=o.buyer_id
ORDER BY o.id DESC
LIMIT 5
");

if($res && mysqli_num_rows($res)>0){
while($o=mysqli_fetch_assoc($res)){

$color="#6b7280";
if($o['order_status']=="completed") $color="#16a34a";
if($o['order_status']=="pending") $color="#f59e0b";
if($o['order_status']=="cancelled") $color="#ef4444";

echo "<tr>
<td>#".$o['id']."</td>
<td>".e($o['make'])." ".e($o['model'])."</td>
<td>".e($o['buyer'])."</td>
<td><span style='background:$color;color:#fff;padding:5px 12px;border-radius:20px'>".ucfirst($o['order_status'])."</span></td>
<td>₹".number_format($o['total_price'])."</td>
</tr>";
}
}else{
echo "<tr><td colspan='5'>No orders</td></tr>";
}
?>

</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>