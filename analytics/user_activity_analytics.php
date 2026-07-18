<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/header.php";

/* ===================== */
/* TOTAL USERS */
/* ===================== */

$buyers = mysqli_fetch_assoc(
  mysqli_query($conn,"SELECT COUNT(*) c FROM buyers")
)['c'];

$sellers = mysqli_fetch_assoc(
  mysqli_query($conn,"SELECT COUNT(*) c FROM sellers")
)['c'];

/* ===================== */
/* MONTHLY GROWTH */
/* ===================== */

$data = mysqli_query($conn,"
SELECT DATE_FORMAT(created_at,'%Y-%m') month,
(SELECT COUNT(*) FROM buyers b WHERE DATE_FORMAT(b.created_at,'%Y-%m')=month) buyers,
(SELECT COUNT(*) FROM sellers s WHERE DATE_FORMAT(s.created_at,'%Y-%m')=month) sellers
FROM buyers
GROUP BY month
ORDER BY month ASC
");

$months = [];
$buyerData = [];
$sellerData = [];

while($row=mysqli_fetch_assoc($data)){
  $months[] = $row['month'];
  $buyerData[] = $row['buyers'];
  $sellerData[] = $row['sellers'];
}
?>
<div class="analytics-header">
    <div>
        <h1>User Analytics Dashboard</h1>
        <p>Monitor buyers and sellers growth in real time.</p>
    </div>

    <button onclick="location.reload()" class="refresh-btn">
        ⟳ Refresh
    </button>
</div>
<!-- ===================== -->
<!-- STATS -->
<!-- ===================== -->

<div class="stats-grid">

<div class="stats-card buyers">

    <div class="icon">👤</div>

    <div>
        <div class="title">Total Buyers</div>

        <div class="number">
            <?php echo number_format($buyers); ?>
        </div>
    </div>

</div>


<div class="stats-card sellers">

    <div class="icon">🚗</div>

    <div>
        <div class="title">Total Sellers</div>

        <div class="number">
            <?php echo number_format($sellers); ?>
        </div>
    </div>

</div>

</div>  

<!-- ===================== -->
<!-- CHART -->
<!-- ===================== -->

<div class="chart-card">

<div class="chart-title">
📈 Monthly User Growth
</div>

<canvas id="userChart"></canvas>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById("userChart"),{

type:"line",

data:{

labels:<?php echo json_encode($months); ?>,

datasets:[

{

label:"Buyers",

data:<?php echo json_encode($buyerData); ?>,

borderColor:"#00b4ff",

backgroundColor:"rgba(0,180,255,.15)",

fill:true,

pointRadius:5,

pointHoverRadius:8,

tension:.4

},

{

label:"Sellers",

data:<?php echo json_encode($sellerData); ?>,

borderColor:"#4caf50",

backgroundColor:"rgba(76,175,80,.15)",

fill:true,

pointRadius:5,

pointHoverRadius:8,

tension:.4

}

]

},

options:{

responsive:true,

plugins:{

legend:{

position:"top"

},

tooltip:{

mode:"index",

intersect:false

}

},

interaction:{

mode:"nearest",

axis:"x",

intersect:false

},

scales:{

y:{

beginAtZero:true,

grid:{

color:"#eee"

}

},

x:{

grid:{

display:false

}

}

}

}

});
</script>
<style>

.analytics-header{

display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;

}

.analytics-header h1{

margin:0;
font-size:32px;
font-weight:700;

}

.analytics-header p{

color:#888;
margin-top:5px;

}

.refresh-btn{

background:#3b82f6;
color:white;
border:none;
padding:12px 24px;
border-radius:10px;
cursor:pointer;
transition:.3s;

}

.refresh-btn:hover{

transform:translateY(-3px);
box-shadow:0 10px 20px rgba(0,0,0,.2);

}

.stats-grid{

display:grid;
grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
gap:25px;
margin-top:20px;

}

.stats-card{

display:flex;
align-items:center;
justify-content:space-between;
padding:30px;
border-radius:18px;
color:white;
transition:.35s;
cursor:pointer;

}

.stats-card:hover{

transform:translateY(-8px) scale(1.03);

}

.buyers{

background:linear-gradient(135deg,#00c6ff,#0072ff);

}

.sellers{

background:linear-gradient(135deg,#00b09b,#96c93d);

}

.icon{

font-size:55px;

}

.title{

font-size:15px;
opacity:.9;

}

.number{

font-size:38px;
font-weight:bold;
margin-top:10px;

}

.chart-card{

margin-top:35px;
padding:30px;
background:white;
border-radius:20px;
box-shadow:0 10px 30px rgba(0,0,0,.08);

}

.chart-title{

font-size:22px;
font-weight:bold;
margin-bottom:25px;

}

canvas{

height:420px !important;

}

</style>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>