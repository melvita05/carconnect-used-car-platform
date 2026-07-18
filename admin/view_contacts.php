<?php
require_once "../core/middleware.php";
requireRole("admin");

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";
?>

<h2>📞 Buyer Contact Messages</h2>

<table class="table">

<tr>
<th>Buyer</th>
<th>Message</th>
<th>Time</th>
</tr>

<?php

$res = mysqli_query($conn,"
SELECT 
c.message,
c.created_at,
b.name AS buyer_name
FROM contact_messages c
JOIN buyers b ON b.id = c.user_id
ORDER BY c.id DESC
");

if($res && mysqli_num_rows($res) > 0){

while($row = mysqli_fetch_assoc($res)){

echo "
<tr>

<td>".e($row['buyer_name'])."</td>

<td>".e($row['message'])."</td>

<td>".date("d M Y, h:i A", strtotime($row['created_at']))."</td>

</tr>
";

}

}else{

echo "
<tr>
<td colspan='3' style='text-align:center'>
No messages yet 📭
</td>
</tr>
";

}
?>

</table>

<?php require_once "../includes/footer.php"; ?>