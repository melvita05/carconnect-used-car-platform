<?php
session_start();
require_once __DIR__ . "/db_connect.php";
require_once __DIR__ . "/functions.php";

$user = (int)($_SESSION['user_id'] ?? 0);

if(!$user){
  exit();
}

$currentCar   = $_GET['car_id'] ?? '';
$currentOther = $_GET['other'] ?? '';

/* ===================== */
/* UPDATED QUERY */
/* ===================== */

$res = mysqli_query($conn,"
SELECT 
  m.car_id,

  IF(m.sender_id = $user, m.receiver_id, m.sender_id) AS other_user,

  c.make,
  c.model,

  COALESCE(b.name, s.name) AS other_name,

  MAX(m.created_at) as last_time

FROM messages m

JOIN car_listings c ON c.id = m.car_id

LEFT JOIN buyers b 
  ON b.id = IF(m.sender_id = $user, m.receiver_id, m.sender_id)

LEFT JOIN sellers s 
  ON s.id = IF(m.sender_id = $user, m.receiver_id, m.sender_id)

WHERE m.sender_id=$user OR m.receiver_id=$user

GROUP BY m.car_id, other_user, c.make, c.model, other_name

ORDER BY last_time DESC
");

/* ===================== */
/* OUTPUT */
/* ===================== */

while($row = mysqli_fetch_assoc($res)){

$active = ($currentCar == $row['car_id'] && $currentOther == $row['other_user']) 
  ? "active" 
  : "";

echo "
<div class='chat-item $active'>

<a href='?car_id=".$row['car_id']."&other=".$row['other_user']."'>

<strong>".e($row['other_name'] ?? 'User')."</strong><br>

<span style='font-size:12px;color:#94a3b8'>
🚗 ".e($row['make'])." ".e($row['model'])."
</span>

</a>

</div>
";

}
?>