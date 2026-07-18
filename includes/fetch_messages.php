<?php
session_start();

require_once __DIR__ . "/db_connect.php";
require_once __DIR__ . "/functions.php";

/* ===================== */
/* CURRENT USER */
/* ===================== */

$user  = (int)($_SESSION['user_id'] ?? 0);
$other = (int)($_GET['other'] ?? 0); // ✅ IMPORTANT CHANGE
$car   = (int)($_GET['car_id'] ?? 0);

/* ===================== */
/* VALIDATION */
/* ===================== */

if(!$user || !$other || !$car){
  exit();
}

/* ===================== */
/* FETCH MESSAGES */
/* ===================== */

$stmt = mysqli_prepare($conn,"
SELECT sender_id,message,created_at
FROM messages
WHERE car_id=?
AND (
 (sender_id=? AND receiver_id=?)
 OR
 (sender_id=? AND receiver_id=?)
)
ORDER BY created_at ASC
");

mysqli_stmt_bind_param($stmt,"iiiii",$car,$user,$other,$other,$user);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

/* ===================== */
/* EMPTY STATE */
/* ===================== */

if(mysqli_num_rows($res) == 0){
  echo "<div class='muted center mt-20'>
  No messages yet 👋 Start chatting
  </div>";
  exit();
}

/* ===================== */
/* OUTPUT */
/* ===================== */

while($m = mysqli_fetch_assoc($res)){

$msg  = e($m['message']);
$time = date("h:i A", strtotime($m['created_at']));

if($m['sender_id'] == $user){

// 👉 CURRENT USER (RIGHT)

echo "
<div class='chat-row right'>
  <div class='chat-bubble right'>
    $msg
    <div class='chat-time'>$time</div>
  </div>
</div>
";

}else{

// 👉 OTHER USER (LEFT)

echo "
<div class='chat-row left'>
  <div class='chat-bubble left'>
    $msg
    <div class='chat-time'>$time</div>
  </div>
</div>
";

}

}

mysqli_stmt_close($stmt);
?>