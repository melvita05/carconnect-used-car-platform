<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = (int)$_SESSION['user_id'];

/* ===================== */
/* FETCH REVIEWS */
/* ===================== */

$stmt = mysqli_prepare($conn,"
SELECT 
r.rating,
r.comment,
r.created_at,
b.name buyer,
c.make,
c.model
FROM reviews r
JOIN buyers b ON b.id = r.user_id  -- ✅ FIXED HERE
JOIN car_listings c ON c.id = r.car_id
WHERE c.seller_id=?
ORDER BY r.id DESC   -- ✅ SAFE SORT (no error)
");

mysqli_stmt_bind_param($stmt,"i",$sellerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>

<h1>⭐ Buyer Reviews</h1>

<table class="table">

<tr>
<th>Car</th>
<th>Buyer</th>
<th>Rating</th>
<th>Comment</th>
<th>Date</th>
</tr>

<?php

if(mysqli_num_rows($res)>0){

while($r=mysqli_fetch_assoc($res)){

$stars = str_repeat("⭐", (int)$r['rating']);

$date = !empty($r['created_at']) 
  ? date("d M Y", strtotime($r['created_at'])) 
  : "-";

echo "

<tr>

<td>".e($r['make'])." ".e($r['model'])."</td>

<td>".e($r['buyer'])."</td>

<td style='color:#ffc107;font-weight:700'>
$stars
</td>

<td>".e($r['comment'])."</td>

<td>$date</td>

</tr>

";

}

}else{

echo "

<tr>
<td colspan='5' style='text-align:center'>

<p class='muted'>No reviews yet ⭐</p>

<p class='muted'>Customer reviews will appear here</p>

</td>
</tr>

";

}

?>

</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>