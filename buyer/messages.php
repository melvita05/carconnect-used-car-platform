<?php
require_once "../core/middleware.php";
requireRole("buyer");

require_once "../includes/header.php";
?>

<h2>💬 My Messages</h2>

<div class="chat-layout">

<!-- LEFT -->
<div class="chat-list" id="chatList"></div>

<!-- RIGHT -->
<div class="chat-main">

  <!-- EMPTY STATE -->
  <div id="emptyState" class="empty-state">
    Select a conversation to start chatting 💬
  </div>

  <!-- CHAT BOX -->
  <div id="chatBox" class="chat-box" style="display:none;"></div>

  <!-- INPUT -->
  <form onsubmit="sendMsg(event)" class="chat-input">
    <input id="msgInput" placeholder="Type message..." required autocomplete="off">
    <button class="btn primary">Send</button>
  </form>

</div>

</div>

<script>

function getParam(name){
  return new URL(window.location.href).searchParams.get(name);
}

/* LOAD CHAT LIST */
function loadChats(){
  fetch("/carconnect/includes/fetch_conversations.php")
  .then(res=>res.text())
  .then(data=>{
    document.getElementById("chatList").innerHTML = data;
  });
}

/* LOAD MESSAGES */
function loadMessages(){
  let car = getParam("car_id");
  let other = getParam("other");

  if(!car || !other){
    document.getElementById("chatBox").style.display = "none";
    document.getElementById("emptyState").style.display = "block";
    return;
  }

  document.getElementById("chatBox").style.display = "block";
  document.getElementById("emptyState").style.display = "none";

  fetch("/carconnect/includes/fetch_messages.php?car_id="+car+"&other="+other)
  .then(res=>res.text())
  .then(data=>{
    let box = document.getElementById("chatBox");
    box.innerHTML = data;
    box.scrollTop = box.scrollHeight;
  });
}

/* SEND MESSAGE */
function sendMsg(e){
  e.preventDefault();

  let input = document.getElementById("msgInput");
  let msg = input.value.trim();

  let car = getParam("car_id");
  let other = getParam("other");

  if(!msg || !car || !other) return;

  fetch("chat.php?car_id="+car+"&seller="+other,{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:"message="+encodeURIComponent(msg)
  }).then(()=>{
    input.value="";
    loadMessages();
  });
}

/* ENTER KEY SEND */
document.getElementById("msgInput").addEventListener("keydown",function(e){
  if(e.key === "Enter"){
    e.preventDefault();
    sendMsg(e);
  }
});

/* AUTO REFRESH */
setInterval(loadMessages,2000);

/* INITIAL LOAD */
loadChats();
loadMessages();

</script>

<?php require_once "../includes/footer.php"; ?>