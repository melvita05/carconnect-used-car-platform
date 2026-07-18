<?php
require_once "../core/middleware.php";
requireRole("buyer");

require_once "../includes/db_connect.php";
require_once "../includes/header.php";

$buyer  = (int) $_SESSION['user_id'];
$seller = (int) ($_GET['seller'] ?? 0);
$car    = (int) ($_GET['car_id'] ?? 0);

/* VALIDATION */
if(!$seller || !$car){
  echo "<div class='alert'>Invalid chat request.</div>";
  require_once "../includes/footer.php";
  exit();
}

/* SEND MESSAGE (AJAX) */
if($_SERVER['REQUEST_METHOD'] === "POST"){

  $msg = trim($_POST['message'] ?? '');

  if($msg !== ""){
    $stmt = mysqli_prepare($conn,"
      INSERT INTO messages(car_id, sender_id, receiver_id, message)
      VALUES (?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt,"iiis",$car,$buyer,$seller,$msg);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
  }

  exit();
}
?>

<h2>💬 Chat with Seller</h2>

<div class="chat-container">

  <!-- CHAT BOX -->
  <div id="chatBox" class="chat-box"></div>

  <!-- INPUT -->
  <form onsubmit="sendMsg(event)" class="chat-input">
    <input class="input" id="msgInput" name="message" placeholder="Type your message..." required autocomplete="off">
    <button class="btn primary">Send</button>
  </form>

</div>

<script>

/* ===================== */
/* LOAD MESSAGES (UPDATED) */
/* ===================== */
function loadMessages(){
  fetch("/carconnect/includes/fetch_messages.php?other=<?php echo $seller; ?>&car_id=<?php echo $car; ?>")
  .then(res => res.text())
  .then(data => {
    let box = document.getElementById("chatBox");

    if(box.innerHTML !== data){
      box.innerHTML = data;
      box.scrollTop = box.scrollHeight;
    }
  });
}

/* ===================== */
/* SEND MESSAGE */
/* ===================== */
function sendMsg(e){
  e.preventDefault();

  let input = document.getElementById("msgInput");
  let msg = input.value.trim();

  if(msg === "") return;

  fetch("chat.php?seller=<?php echo $seller; ?>&car_id=<?php echo $car; ?>",{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:"message="+encodeURIComponent(msg)
  }).then(()=>{
    input.value="";
    loadMessages();
  });
}

/* ENTER SEND */
document.getElementById("msgInput").addEventListener("keydown", function(e){
  if(e.key === "Enter"){
    e.preventDefault();
    sendMsg(e);
  }
});

/* AUTO REFRESH */
setInterval(loadMessages, 2000);

/* FIRST LOAD */
loadMessages();

/* AUTO FOCUS */
document.getElementById("msgInput").focus();

</script>

<?php require_once "../includes/footer.php"; ?>