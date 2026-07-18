<style>

.filter-box{

    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.10);
    margin-bottom:30px;

}


.filter-title{

    font-size:22px;
    font-weight:800;
    margin-bottom:20px;
    color:#111827;

}



.filter-grid{

    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:18px;

}



.filter-item label{

    display:block;
    font-weight:600;
    margin-bottom:7px;
    color:#374151;

}



.filter-item .input{

    border-radius:12px;
    padding:12px;
    border:1px solid #d1d5db;

}



.filter-actions{

    display:flex;
    gap:12px;
    margin-top:25px;
    flex-wrap:wrap;

}


.filter-actions .btn{

    border-radius:12px;
    padding:12px 25px;

}



</style>



<form method="GET" 
action="car_listings.php" 
class="filter-box">


<div class="filter-title">

🔍 Find Your Dream Car

</div>



<div class="filter-grid">



<div class="filter-item">

<label>
Search Car
</label>

<input
class="input"
type="text"
name="q"
placeholder="Brand or Model"
value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">

</div>




<div class="filter-item">

<label>
Minimum Price
</label>

<input
class="input"
type="number"
name="min"
placeholder="₹ Minimum"
value="<?php echo htmlspecialchars($_GET['min'] ?? ''); ?>">

</div>




<div class="filter-item">

<label>
Maximum Price
</label>

<input
class="input"
type="number"
name="max"
placeholder="₹ Maximum"
value="<?php echo htmlspecialchars($_GET['max'] ?? ''); ?>">

</div>




<div class="filter-item">

<label>
Fuel Type
</label>

<select class="input" name="fuel_type">

<option value="">
All Fuel
</option>

<option>Petrol</option>
<option>Diesel</option>
<option>CNG</option>
<option>Electric</option>

</select>

</div>




<div class="filter-item">

<label>
Transmission
</label>

<select class="input" name="transmission">

<option value="">
All Transmission
</option>

<option>Manual</option>
<option>Automatic</option>

</select>

</div>




<div class="filter-item">

<label>
Color
</label>

<select class="input" name="color">

<option value="">
All Colors
</option>

<option>White</option>
<option>Black</option>
<option>Silver</option>
<option>Grey</option>
<option>Blue</option>
<option>Red</option>
<option>Brown</option>
<option>Green</option>

</select>

</div>




<div class="filter-item">

<label>
Owner
</label>

<select class="input" name="owner_type">

<option value="">
All Owners
</option>

<option>First Owner</option>
<option>Second Owner</option>
<option>Third Owner</option>
<option>Fourth Owner</option>

</select>

</div>




<div class="filter-item">

<label>
Body Type
</label>

<select class="input" name="body_type">

<option value="">
All Types
</option>

<option>Hatchback</option>
<option>Sedan</option>
<option>SUV</option>
<option>MUV</option>
<option>Coupe</option>
<option>Convertible</option>

</select>

</div>




<div class="filter-item">

<label>
Seats
</label>

<select class="input" name="seating_capacity">

<option value="">
Any Seats
</option>

<option>2</option>
<option>4</option>
<option>5</option>
<option>6</option>
<option>7</option>
<option>8</option>

</select>

</div>




<div class="filter-item">

<label>
Location
</label>

<input
class="input"
type="text"
name="location"
placeholder="City"
value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>">

</div>



</div>



<div class="filter-actions">


<button 
class="btn primary"
type="submit">

🔍 Search Cars

</button>



<a 
href="car_listings.php"
class="btn">

Reset

</a>


</div>



</form>