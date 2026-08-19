<?php
require_once "../includes/header.php";
?>

<style>

body{
    background:#f4f7fb;
}

.success-container{
    max-width:600px;
    margin:70px auto;
    padding:20px;
}

.success-card{
    background:#fff;
    border-radius:20px;
    padding:45px 35px;
    text-align:center;
    box-shadow:0 15px 40px rgba(0,0,0,.08);
    animation:fadeIn .5s ease;
}

.success-icon{
    width:100px;
    height:100px;
    margin:auto;
    background:#22c55e;
    color:#fff;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:55px;
    margin-bottom:25px;
}

.success-card h1{
    font-size:34px;
    color:#111827;
    margin-bottom:15px;
}

.success-card p{
    font-size:17px;
    color:#6b7280;
    line-height:1.8;
    margin-bottom:35px;
}

.btn-group{
    display:flex;
    justify-content:center;
    gap:15px;
    flex-wrap:wrap;
}

.btn{
    min-width:180px;
    padding:14px;
    border-radius:12px;
    text-decoration:none;
    font-weight:600;
    transition:.3s;
}

.btn:hover{
    transform:translateY(-3px);
}

.btn.secondary{
    background:#f3f4f6;
    color:#111827;
}

@keyframes fadeIn{

from{
opacity:0;
transform:translateY(20px);
}

to{
opacity:1;
transform:translateY(0);
}

}

</style>

<div class="success-container">

<div class="success-card">

<div class="success-icon">
✔
</div>

<h1>Payment Successful!</h1>

<p>
Thank you for your purchase.
Your order has been placed successfully and is now being processed.
</p>

<div class="btn-group">

<a class="btn primary" href="order_history.php">
📦 View Orders
</a>

<a class="btn secondary" href="/carconnect/buyer/home.php">
🏠 Back to Home
</a>

</div>

</div>

</div>

<?php require_once "../includes/footer.php"; ?>