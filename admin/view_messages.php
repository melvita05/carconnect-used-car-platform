<?php
require_once "../core/middleware.php";
requireRole("admin");

require_once "../includes/db_connect.php";
require_once "../includes/header.php";
?>

<h1>All Messages</h1>

<table class="table">
<tr>
<th>Car</th>
<th>Message</th>
<th>Date</th>
</tr>

<?php
$res=mysqli_query($conn,"
SELECT m.*, c.make,c.model
FROM messages m
JOIN car_listings c ON c.id=m.car_id
ORDER BY m.created_at DESC
LIMIT 100
");

while($m=mysqli_fetch_assoc($res)){
echo "<tr>
<td>{$m['make']} {$m['model']}</td>
<td>{$m['message']}</td>
<td>{$m['created_at']}</td>
</tr>";
}
?>
</table>

<?php require_once "../includes/footer.php"; ?>