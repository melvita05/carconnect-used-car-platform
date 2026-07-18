<?php
require_once "../core/middleware.php";
requireRole("buyer");

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";

$buyerId = (int)$_SESSION['user_id'];

/* ===================== */
/* FETCH MY REVIEWS */
/* ===================== */

$stmt = mysqli_prepare($conn,"
SELECT 
r.rating,
r.comment,
r.created_at,
c.make,
c.model

FROM reviews r

JOIN car_listings c
ON c.id = r.car_id

WHERE r.user_id=?

ORDER BY r.created_at DESC
");


mysqli_stmt_bind_param($stmt,"i",$buyerId);

mysqli_stmt_execute($stmt);

$res = mysqli_stmt_get_result($stmt);
?>

<h1>⭐ My Reviews</h1>

<?php if(mysqli_num_rows($res) == 0): ?>

<div class="empty-state">
  <p class="empty-title">No reviews yet 🚗</p>
  <p class="muted">You haven’t reviewed any cars.</p>
</div>

<?php else: ?>

<table class="table">

<tr>
<th>Car</th>
<th>Rating</th>
<th>Comment</th>
<th>Date</th>
</tr>

<?php while($r = mysqli_fetch_assoc($res)): ?>

<tr>

<td>
<?php echo e($r['make']." ".$r['model']); ?>
</td>

<td>
<?php
$stars = str_repeat("⭐", (int)$r['rating']);
echo $stars;
?>
</td>

<td>
<?php echo e($r['comment']); ?>
</td>

<td>
<?php echo date("d M Y", strtotime($r['created_at'])); ?>
</td>

</tr>

<?php endwhile; ?>

</table>

<?php endif; ?>


<?php require_once "../includes/footer.php"; ?>