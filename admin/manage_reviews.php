<?php
require_once "../core/middleware.php";
requireRole("admin");

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";

/* DELETE REVIEW */
if(isset($_GET['delete'])){
  $id = intval($_GET['delete']);
  mysqli_query($conn,"DELETE FROM reviews WHERE id=$id");
}
?>

<h1>⭐ Manage Reviews</h1>

<table class="table">
<tr>
<th>User</th>
<th>Car</th>
<th>Rating</th>
<th>Comment</th>
<th>Action</th>
</tr>

<?php

$res = mysqli_query($conn,"
SELECT 
r.id,
r.rating,
r.comment,
b.name AS user,
c.make,
c.model
FROM reviews r
JOIN buyers b ON b.id = r.user_id
JOIN car_listings c ON c.id = r.car_id
ORDER BY r.id DESC
");

if($res && mysqli_num_rows($res)>0){

while($r=mysqli_fetch_assoc($res)){

$stars = str_repeat("⭐", (int)$r['rating']);

echo "<tr>

<td>".e($r['user'])."</td>

<td>".e($r['make'])." ".e($r['model'])."</td>

<td style='color:#ffc107;font-weight:700'>
$stars
</td>

<td>".e($r['comment'])."</td>

<td>
<a class='btn' href='?delete=".$r['id']."'>Delete</a>
</td>

</tr>";

}

}else{

echo "<tr>
<td colspan='5' style='text-align:center'>
No reviews found ⭐
</td>
</tr>";

}

?>

</table>

<?php require_once "../includes/footer.php"; ?>