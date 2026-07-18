<?php
require_once "../core/middleware.php";
requireRole("admin");

require_once "../includes/db_connect.php";
require_once "../includes/header.php";
require_once "../includes/functions.php";
?>

<h1>💳 Manage Payments</h1>

<table class="table">
<tr>
<th>ID</th>
<th>Order</th>
<th>Method</th>
<th>Status</th>
<th>Date</th>
</tr>

<?php
$res = mysqli_query($conn,"
SELECT
    p.id,
    p.payment_method,
    p.payment_status,
    p.created_at,
    o.id AS order_id
FROM payments p
JOIN orders o ON o.id = p.order_id
ORDER BY p.id DESC
");

if(!$res){
    die(mysqli_error($conn));
}
if($res && mysqli_num_rows($res)>0){

while($p=mysqli_fetch_assoc($res)){

$payColor = "#999";
if($p['payment_status']=="paid") $payColor="#16a34a";
if($p['payment_status']=="pending") $payColor="#f59e0b";
if($p['payment_status']=="failed") $payColor="#ef4444";

echo "<tr>

<td>#".intval($p['id'])."</td>

<td>#".intval($p['order_id'])."</td>

<td>".e($p['payment_method'])."</td>

<td>
<span style='color:$payColor;font-weight:700'>
".ucfirst($p['payment_status'])."
</span>
</td>

<td>".date("d M Y, h:i A", strtotime($p['created_at']))."</td>
</tr>";

}

}else{

echo "<tr>
<td colspan='5' style='text-align:center'>
No payments found 💳
</td>
</tr>";

}

?>

</table>

<?php require_once "../includes/footer.php"; ?>