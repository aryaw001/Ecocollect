<?php
session_start();
include "../config/db.php";

// 🔥 INSERT CODE STARTS HERE
if(isset($_POST['submit_request'])){

    if(!isset($_SESSION['retailer_id'])){
        die("Retailer session missing");
    }

    $retailer_id = $_SESSION['retailer_id'];

    $waste_types = isset($_POST['waste_types']) 
        ? implode(',', $_POST['waste_types']) 
        : '';

    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $pickup_date = $_POST['pickup_date'];
    $notes = mysqli_real_escape_string($conn,$_POST['notes']);

    $request_code = "REQ-" . time();

    $sql = "INSERT INTO retailer_requests
            (retailer_id, request_code, waste_types, quantity, quantity_unit, preferred_pickup_date, recycler_notes)
            VALUES
            ('$retailer_id','$request_code','$waste_types','$quantity','$unit','$pickup_date','$notes')";

    if(!mysqli_query($conn,$sql)){
        die("Insert Error: " . mysqli_error($conn));
    }

    header("Location: dashboard.php");
    exit;
}
// 🔥 INSERT CODE ENDS HERE

if (!isset($_SESSION['retailer_id'])) {
    header("Location: login.php");
    exit;
}

$retailer_id = $_SESSION['retailer_id'];
$retailer_name = $_SESSION['retailer_name'];

/* ================= FETCH DATA ================= */

$stmt = $conn->prepare("
    SELECT * FROM retailer_requests
    WHERE retailer_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $retailer_id);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
$total_requests = 0;
$total_kg = 0;
$active = 0;
$completed = 0;

$monthly_data = [];

while ($row = $result->fetch_assoc()) {

    $requests[] = $row;
    $total_requests++;

    if ($row['quantity_unit'] === 'kg') {
        $total_kg += (float)$row['quantity'];
    }

    if (in_array($row['status'], ['pending','approved','scheduled'])) {
        $active++;
    }

    if ($row['status'] === 'completed') {
        $completed++;
    }

    $month = date("M", strtotime($row['created_at']));
    $monthly_data[$month] = ($monthly_data[$month] ?? 0) + $row['quantity'];
}

/* CATEGORY DISTRIBUTION */
$category_data = [];
foreach ($requests as $r) {
    $types = explode(",", $r['waste_types']);
    foreach ($types as $t) {
        $t = trim($t);
        $category_data[$t] = ($category_data[$t] ?? 0) + $r['quantity'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Retailer Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    margin:0;
    font-family:'Inter',sans-serif;
    background:#f3f4f6;
    display:flex;
}

/* SIDEBAR */
.sidebar{
    width:280px;
    background:white;
    padding:30px 20px;
    box-shadow:2px 0 20px rgba(0,0,0,0.05);
    height:100vh;
    position:fixed;
}

.sidebar h2{
    margin:0 0 30px;
    font-size:20px;
}

.sidebar button{
    width:100%;
    padding:12px;
    margin-top:20px;
    border:none;
    border-radius:12px;
    background:#16a34a;
    color:white;
    font-weight:600;
    cursor:pointer;
}

/* MAIN */
.main{
    margin-left:280px;
    padding:40px;
    width:100%;
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

/* STATS */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
    margin-bottom:40px;
}

.card{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

.card h3{
    margin:0;
    font-size:28px;
}

.card small{
    color:#6b7280;
}

/* REQUEST LIST */
.request-card{
    background:white;
    padding:20px;
    border-radius:18px;
    margin-bottom:15px;
    box-shadow:0 8px 20px rgba(0,0,0,0.04);
}

.badge{
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.pending{background:#fef3c7;color:#92400e;}
.approved{background:#dcfce7;color:#166534;}
.scheduled{background:#e0f2fe;color:#075985;}
.completed{background:#ede9fe;color:#5b21b6;}
.rejected{background:#fee2e2;color:#991b1b;}

/* ANALYTICS */
.analytics{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(350px,1fr));
    gap:20px;
    margin-top:40px;
}

@media(max-width:900px){
    .sidebar{
        display:none;
    }
    .main{
        margin-left:0;
    }
}

.modal{
    display:none;
    position:fixed;
    z-index:1000;
    left:0;top:0;
    width:100%;height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
}
.modal-content{
    background:white;
    padding:30px;
    border-radius:20px;
    width:500px;
    max-height:90vh;
    overflow:auto;
}
.modal-content input,
.modal-content textarea,
.modal-content select{
    width:100%;
    padding:10px;
    margin:8px 0;
    border-radius:8px;
    border:1px solid #ddd;
}
.chip-group label{
    display:inline-block;
    margin:5px 8px 5px 0;
}
.submit-btn{
    background:#16a34a;
    color:white;
    padding:12px;
    border:none;
    border-radius:12px;
    width:100%;
    cursor:pointer;
}
.close{
    float:right;
    cursor:pointer;
    font-size:20px;
}
</style>
</head>
<body>

<div class="sidebar">
    <h2>♻ E-Waste Portal</h2>
    <p><strong><?= htmlspecialchars($retailer_name) ?></strong></p>

    <button onclick="openModal()">+ Create New Request</button>

    <button style="background:#ef4444;margin-top:15px;"
        onclick="window.location.href='logout.php'">
        Logout
    </button>
</div>

<div class="main">

<div class="header">
    <h1>Welcome back 👋</h1>
</div>

<!-- STATS -->
<div class="stats">
    <div class="card">
        <small>Total Requests</small>
        <h3><?= $total_requests ?></h3>
    </div>
    <div class="card">
        <small>Total KG</small>
        <h3><?= number_format($total_kg,2) ?> kg</h3>
    </div>
    <div class="card">
        <small>Active</small>
        <h3><?= $active ?></h3>
    </div>
    <div class="card">
        <small>Completed</small>
        <h3><?= $completed ?></h3>
    </div>
</div>

<!-- REQUESTS -->
<h2>Submitted Requests</h2>

<?php if(empty($requests)) { ?>
    <div class="card">No requests submitted yet.</div>
<?php } else { ?>
    <?php foreach($requests as $r) { ?>
        <div class="request-card">
            <strong><?= htmlspecialchars($r['request_code']) ?></strong>
            <br><br>
            <small><?= htmlspecialchars($r['waste_types']) ?></small>
            <br>
            <?= $r['quantity'] ?> <?= $r['quantity_unit'] ?>
            <br><br>
            <span class="badge <?= $r['status'] ?>">
                <?= ucfirst($r['status']) ?>
            </span>
        </div>
    <?php } ?>
<?php } ?>

<!-- ANALYTICS -->
<div class="analytics">

<div class="card">
    <h3>Monthly Waste Volume</h3>
    <canvas id="barChart"></canvas>
</div>

<div class="card">
    <h3>Category Distribution</h3>
    <canvas id="pieChart"></canvas>
</div>

</div>

</div>

<script>
const monthlyLabels = <?= json_encode(array_keys($monthly_data)) ?>;
const monthlyValues = <?= json_encode(array_values($monthly_data)) ?>;

new Chart(document.getElementById('barChart'),{
    type:'bar',
    data:{
        labels:monthlyLabels,
        datasets:[{
            label:'KG',
            data:monthlyValues,
            backgroundColor:'#16a34a'
        }]
    }
});

const categoryLabels = <?= json_encode(array_keys($category_data)) ?>;
const categoryValues = <?= json_encode(array_values($category_data)) ?>;

new Chart(document.getElementById('pieChart'),{
    type:'doughnut',
    data:{
        labels:categoryLabels,
        datasets:[{
            data:categoryValues,
            backgroundColor:['#16a34a','#3b82f6','#f59e0b','#ef4444','#8b5cf6']
        }]
    }
});
</script>

<!-- MODAL -->
<div id="requestModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Create E-Waste Request</h2>

        <form method="POST">

            <input type="text" name="contact_person" placeholder="Contact Person" required>
            <input type="email" name="email" placeholder="Official Email" required>
            <input type="text" name="phone" placeholder="Phone Number" required>

            <textarea name="company_address" placeholder="Company Address" required></textarea>

            <label>Type of E-Waste</label>
            <div class="chip-group">
                <label><input type="checkbox" name="waste_types[]" value="Laptops"> Laptops</label>
                <label><input type="checkbox" name="waste_types[]" value="Mobiles"> Mobiles</label>
                <label><input type="checkbox" name="waste_types[]" value="Batteries"> Batteries</label>
                <label><input type="checkbox" name="waste_types[]" value="Servers"> Servers</label>
                <label><input type="checkbox" name="waste_types[]" value="Industrial"> Industrial</label>
                <label><input type="checkbox" name="waste_types[]" value="Mixed Scrap"> Mixed Scrap</label>
            </div>

            <input type="number" name="quantity" placeholder="Estimated Quantity" required>

            <select name="unit">
                <option value="kg">KG</option>
                <option value="tons">Tons</option>
            </select>

            <input type="date" name="pickup_date" required>

            <textarea name="notes" placeholder="Additional Notes"></textarea>

            <button type="submit" name="submit_request" class="submit-btn">Submit Request →</button>
        </form>
    </div>
</div>

<script>
function openModal(){
    document.getElementById("requestModal").style.display="flex";
}
function closeModal(){
    document.getElementById("requestModal").style.display="none";
}
</script>
</body>
</html>