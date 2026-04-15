<?php
session_start();
include "../config/db.php";


if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}


$cid = $_SESSION['customer_id'];

/* Fetch logged-in user details */
$userQuery = mysqli_query($conn, "SELECT name, email FROM users WHERE id = $cid");
$user = mysqli_fetch_assoc($userQuery);

/* Total quantity */
$totalQuery = mysqli_query($conn, "
    SELECT SUM(quantity) AS total_qty 
    FROM requests 
    WHERE customer_id = $cid
");
$totalQty = mysqli_fetch_assoc($totalQuery)['total_qty'] ?? 0;

/* Item-wise quantity */
$itemQuery = mysqli_query($conn, "
    SELECT e.item_name, SUM(r.quantity) AS qty
    FROM requests r
    JOIN ewaste_items e ON r.item_id = e.id
    WHERE r.customer_id = $cid
    GROUP BY e.item_name
");

/* Fetch requests grouped by date with total quantities (latest first) */
$dateQuery = mysqli_query($conn, "
    SELECT DATE(created_at) as order_date, COUNT(*) as order_count, SUM(quantity) as total_quantity
    FROM requests
    WHERE customer_id = $cid
    GROUP BY DATE(created_at)
    ORDER BY order_date DESC
    LIMIT 10
");

$chartData = [];
while ($row = mysqli_fetch_assoc($dateQuery)) {
    $chartData[] = $row;
}

/* Fetch customer requests with product details */
$reqQuery = mysqli_query($conn, "
    SELECT 
        product_name,
        company,
        age_months,
        working_condition,
        quantity,
        estimated_price
    FROM requests
    WHERE customer_id = $cid
    ORDER BY id DESC
");

$itemNames = [];
$itemQtys  = [];

while ($row = mysqli_fetch_assoc($itemQuery)) {
    $itemNames[] = $row['item_name'];
    $itemQtys[]  = $row['qty'];
}

$addrQuery = mysqli_query($conn, "SELECT * FROM customer_addresses WHERE customer_id = $cid");
$address = mysqli_fetch_assoc($addrQuery);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="chatbot.js" defer></script>
</head>

<body>

<div class="app-container">

    <!-- TOP HEADER -->
    <div class="topbar">
        <div class="welcome">
            Welcome, <strong><?= htmlspecialchars($user['name']) ?></strong>
        </div>

        <div class="user-actions">
            <div class="user-email">
                <?= htmlspecialchars($user['email']) ?>
            </div>

            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>


    <!-- DASHBOARD -->
    <div class="dashboard">

        <!-- LEFT COLUMN (60%) -->
        <div class="left-column">

            <!-- PORTFOLIO -->
            <div class="card portfolio-card">
                <h3>Your Portfolio</h3>
                <p>Total E-Waste Submitted: <b><?= $totalQty ?> kg</b></p>

                <?php if ($totalQty == 0) { ?>
                    <p style="color:#e0e0e0;">
                        No e-waste submitted yet. Start by adding your first item.
                    </p>
                <?php } else { ?>
                    <div style="height: 260px; margin-bottom: 0.5rem;">
                        <canvas id="portfolioChart"></canvas>
                    </div>
                    
                    <!-- Daily Breakdown Card (Single Consolidated Card) -->
                    <div class="daily-breakdown-card">
                        <h4 style="margin: 0 0 0.8rem 0; font-size: 1rem;">📊 Daily Breakdown</h4>
                        <div class="daily-items-scroll">
                            <?php 
                            // Reverse to show latest first
                            $reversedData = array_reverse($chartData);
                            foreach ($reversedData as $index => $data) { 
                            ?>
                                <div class="daily-item">
                                    <div class="daily-date">📅 <?= htmlspecialchars($data['order_date']) ?></div>
                                    <div class="daily-stats">
                                        <div class="stat-value"><?= $data['total_quantity'] ?? 0 ?> kg</div>
                                        <div class="stat-label"><?= $data['order_count'] ?> order(s)</div>
                                    </div>
                                    <canvas id="chart_<?= $index ?>" style="width: 100%; height: 120px;"></canvas>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- REQUEST DETAILS -->
            <div class="card products-card">
                <h3>Your Submitted Products</h3>

                <?php if (mysqli_num_rows($reqQuery) == 0) { ?>
                    <p style="color:#e0e0e0;">No products submitted yet.</p>
                <?php } else { ?>
                    <div class="products-container">
                        <table class="requests-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Company</th>
                                    <th>Age</th>
                                    <th>Condition</th>
                                    <th>Qty (kg)</th>
                                    <th>Estimated Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($r = mysqli_fetch_assoc($reqQuery)) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['product_name']) ?></td>
                                    <td><?= htmlspecialchars($r['company']) ?></td>
                                    <td><?= round($r['age_months'] / 12, 1) ?> yrs</td>
                                    <td><?= $r['working_condition'] == 'Y' ? 'Working' : 'Not Working' ?></td>
                                    <td><?= $r['quantity'] ?> kg</td>
                                    <td>₹<?= number_format($r['estimated_price'] ?? 0, 2) ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>

            <!-- SUBMIT FORM -->
            <div class="card" style="margin-top:25px;">
                <h3>Add Your Product</h3>

                <form id="productForm" action="submit_request.php" method="POST">

                    <input name="product_name" id="product" placeholder="Product (Laptop, Mobile...)" required>

                    <input name="company" id="company" placeholder="Company (Apple, Dell, Samsung...)" required>

                    <input type="number" name="age_months" id="age_months" placeholder="Age (in months)" required>

                    <input type="text" id="age_years" placeholder="Age (in years)" disabled>

                    <div class="radio-group">
                        <label>
                            <input type="radio" name="working_condition" value="Y" required> Working
                        </label>
                        <label>
                            <input type="radio" name="working_condition" value="N"> Not Working
                        </label>
                    </div>

                    <input type="number" name="qty" id="qty" placeholder="Quantity (kg)" required>

                    <!-- Estimate Button -->
                    <button type="button" id="estimateBtn">Estimate Price</button>

                    <!-- Estimated Price Display -->
                    <div id="estimateResult" class="estimate-box" style="display:none;"></div>

                    

                </form>

                <!-- LIVE PREVIEW -->
                <div id="previewCard" style="margin-top:20px; display:none;">
                    <h4>Product Preview</h4>
                    <p id="previewText"></p>
                </div>
            </div>

            <!-- DELIVERY ADDRESS -->
            <div class="card">
                <h3>Delivery Address</h3>

                <form id="addressForm">

                    <input name="full_name" placeholder="Full Name"
                        value="<?= htmlspecialchars($address['full_name'] ?? '') ?>" required>

                    <input name="phone" placeholder="Phone Number"
                        value="<?= htmlspecialchars($address['phone'] ?? '') ?>" required>

                    <textarea name="address_line" placeholder="Full Address" rows="3" required><?= htmlspecialchars($address['address_line'] ?? '') ?></textarea>

                    <input name="city" placeholder="City"
                        value="<?= htmlspecialchars($address['city'] ?? '') ?>" required>

                    <input name="state" placeholder="State"
                        value="<?= htmlspecialchars($address['state'] ?? '') ?>" required>

                    <input name="pincode" placeholder="Pincode"
                        value="<?= htmlspecialchars($address['pincode'] ?? '') ?>" required>

                    <button type="button" id="saveAddressBtn">Save Address</button>
                    <div id="addressStatus" style="margin-top:10px; font-size:14px;"></div>

                </form>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="card">
                <h3>Submit Your Request</h3>
                <p style="color:#666; margin-bottom:15px;">Make sure your address is saved before submitting!</p>
                <form id="submitForm" action="submit_request.php" method="POST">
                    <input type="hidden" name="product_name" id="submit_product">
                    <input type="hidden" name="company" id="submit_company">
                    <input type="hidden" name="age_months" id="submit_age">
                    <input type="hidden" name="working_condition" id="submit_condition">
                    <input type="hidden" name="qty" id="submit_qty">
                    <button type="submit" id="finalSubmitBtn">
                        Submit Request
                    </button>
                </form>
            </div>


        </div>

        <!-- RIGHT COLUMN (40%) -->
        <div>

            <!-- PRICING -->
            <div class="card">
                <h3>Item Pricing (₹ / kg)</h3>
                <table class="pricing-table">
                    <tr><th>Item</th><th>Price</th></tr>
                    <tr><td>Laptop</td><td>₹300</td></tr>
                    <tr><td>Mobile</td><td>₹250</td></tr>
                    <tr><td>Battery</td><td>₹180</td></tr>
                    <tr><td>Charger</td><td>₹100</td></tr>
                </table>
            </div>

            <!-- AI BOT -->
            <div class="card" style="margin-top:25px;">
                <h3>EcoBot Assistant</h3>
                <div id="chatbox"></div>
                <input id="userInput" placeholder="Ask about pricing, pickup, process…">
                <button onclick="sendMessage()">Ask</button>
            </div>

        </div>

    </div>

</div>

<script>
const ctx = document.getElementById('portfolioChart');

if (ctx) {
    const chartData = <?= json_encode($chartData) ?>;
    
    // Extract dates and quantities for daily chart
    const dates = chartData.map(d => d.order_date).reverse();
    const quantities = chartData.map(d => d.total_quantity || 0).reverse();
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dates.length > 0 ? dates : ['No data'],
            datasets: [{
                label: 'E-Waste Submitted (kg)',
                data: quantities.length > 0 ? quantities : [0],
                backgroundColor: [
                    '#00d4ff',
                    '#4ade80', 
                    '#fbbf24',
                    '#f87171',
                    '#60a5fa',
                    '#a78bfa'
                ],
                borderColor: 'rgba(255,255,255,0.3)',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y', // Horizontal bar chart
            plugins: {
                legend: { 
                    display: true,
                    labels: { color: 'white', padding: 15 }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Total: ' + context.parsed.x + ' kg';
                        }
                    },
                    backgroundColor: 'rgba(0,0,0,0.7)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 10
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { 
                        color: 'rgba(255,255,255,0.8)',
                        callback: function(value) {
                            return value + ' kg';
                        }
                    },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                y: {
                    ticks: { color: 'rgba(255,255,255,0.8)' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                }
            }
        }
    });
}
</script>
</script>

<script>
// Generate charts for each order date (horizontal scrolling)
const chartData = <?= json_encode($chartData) ?>;
const itemNames = <?= json_encode($itemNames) ?>;
const itemQtys = <?= json_encode($itemQtys) ?>;

if (chartData && chartData.length > 0) {
    // Reverse to show latest first
    const reversedData = [...chartData].reverse();
    
    // Create mini charts for each date in reversed order
    reversedData.forEach((data, index) => {
        const canvasId = 'chart_' + index;
        const ctx = document.getElementById(canvasId);
        
        if (ctx) {
            // Show the actual products submitted on that date as pie chart
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: itemNames.length > 0 ? itemNames.slice(0, 3) : ['No data'],
                    datasets: [{
                        label: 'Quantity (kg)',
                        data: itemQtys.length > 0 ? itemQtys.slice(0, 3) : [0],
                        backgroundColor: ['#00d4ff', '#4ade80', '#fbbf24'],
                        borderColor: 'rgba(255,255,255,0.3)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { 
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + ' kg';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
}
</script>

<script>
const ageMonthsInput = document.getElementById('age_months');
const ageYearsInput  = document.getElementById('age_years');
const form           = document.getElementById('productForm');
const previewCard    = document.getElementById('previewCard');
const previewText    = document.getElementById('previewText');

if (ageMonthsInput && ageYearsInput && form && previewCard && previewText) {
    ageMonthsInput.addEventListener('input', () => {
        const months = ageMonthsInput.value;
        if (months > 0) {
            ageYearsInput.value = (months / 12).toFixed(1) + " years";
        } else {
            ageYearsInput.value = "";
        }
    });

    form.addEventListener('input', () => {
        const product  = form.product_name.value;
        const company  = form.company.value;
        const ageM     = form.age_months.value;
        const condition = form.working_condition.value || "";

        if (product && company && ageM && condition) {
            previewCard.style.display = "block";
            previewText.innerHTML = `
                <strong>${company} ${product}</strong><br>
                Age: ${ageYearsInput.value}<br>
                Condition: ${condition === 'Y' ? 'Working' : 'Not Working'}
            `;
        }
    });
}
</script>

<script>
const ageMonths = document.getElementById('age_months');
const ageYears  = document.getElementById('age_years');
const estimateBtn = document.getElementById('estimateBtn');
const resultBox = document.getElementById('estimateResult');
const submitBtn = document.getElementById('submitBtn');

/* Auto convert months → years */
ageMonths.addEventListener('input', () => {
    ageYears.value = (ageMonths.value / 12).toFixed(1);
});

/* Estimate price */
estimateBtn.addEventListener('click', async () => {

    const data = {
        product_name: document.getElementById('product').value,
        company: document.getElementById('company').value,
        age_months: ageMonths.value,
        working_condition: document.querySelector('input[name="working_condition"]:checked')?.value,
        qty: document.getElementById('qty').value
    };

    if (Object.values(data).includes(undefined) || Object.values(data).includes("")) {
        alert("Please fill all fields");
        return;
    }

    const res = await fetch("estimate_price.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    });

    const result = await res.json();

    resultBox.style.display = "block";
    resultBox.innerHTML = `
        <strong>Estimated Price:</strong> ₹${result.price}<br>
        <small>This estimate may vary based on final inspection.</small>
    `;

    submitBtn.disabled = false;
});
</script>
<script>
// Save Address Handler
document.getElementById("saveAddressBtn").addEventListener("click", async () => {
    const addressForm = document.getElementById("addressForm");
    
    // Check if all fields are filled
    if (!addressForm.checkValidity()) {
        alert("Please fill all address fields");
        return;
    }

    const data = {
        full_name: addressForm.querySelector("input[name='full_name']").value,
        phone: addressForm.querySelector("input[name='phone']").value,
        address: addressForm.querySelector("textarea[name='address_line']").value,
        city: addressForm.querySelector("input[name='city']").value,
        state: addressForm.querySelector("input[name='state']").value,
        pincode: addressForm.querySelector("input[name='pincode']").value
    };

    console.log("Sending address data:", data);

    try {
        const res = await fetch("save_address.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });

        console.log("Response status:", res.status);
        
        const result = await res.json();
        console.log("Response data:", result);

        if (result.status === "success") {
            document.getElementById("addressStatus").innerHTML = "<span style='color:green; font-weight:bold;'>✓ Address saved successfully!</span>";
            setTimeout(() => {
                document.getElementById("addressStatus").innerHTML = "";
            }, 5000);
        } else {
            document.getElementById("addressStatus").innerHTML = "<span style='color:red; font-weight:bold;'>✗ Error: " + (result.msg || "Save failed") + "</span>";
        }
    } catch (error) {
        console.error("Fetch error:", error);
        document.getElementById("addressStatus").innerHTML = "<span style='color:red; font-weight:bold;'>✗ Error: " + error.message + "</span>";
    }
});

// Product Form Handler - Copy data to hidden fields and submit
document.getElementById("finalSubmitBtn").addEventListener("click", (e) => {
    e.preventDefault();
    
    const productForm = document.getElementById("productForm");
    
    // Check if all fields are filled
    if (!productForm.checkValidity()) {
        alert("Please fill all product fields");
        return;
    }

    // Copy product form data to submit form hidden fields
    document.getElementById("submit_product").value = document.getElementById("product").value;
    document.getElementById("submit_company").value = document.getElementById("company").value;
    document.getElementById("submit_age").value = document.getElementById("age_months").value;
    document.getElementById("submit_condition").value = document.querySelector("input[name='working_condition']:checked").value;
    document.getElementById("submit_qty").value = document.getElementById("qty").value;
    
    // Submit the form
    document.getElementById("submitForm").submit();
});
</script>


</body>
</html>