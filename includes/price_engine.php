<?php
function estimate_price($conn, $product, $company, $age_months, $condition, $qty) {

    $q = mysqli_query($conn, "
        SELECT base_price_per_kg, weight_factor 
        FROM pricing_model
        WHERE product_name='$product' AND company='$company'
        ORDER BY last_updated DESC LIMIT 1
    ");

    if (mysqli_num_rows($q) == 0) {
        $base = 200;
        $weight = 1.0;
    } else {
        $row = mysqli_fetch_assoc($q);
        $base = $row['base_price_per_kg'];
        $weight = $row['weight_factor'];
    }

    /* Age depreciation */
    $age_factor = max(0.4, 1 - ($age_months / 120));

    /* Condition impact */
    $condition_factor = ($condition == 'Y') ? 1.0 : 0.7;

    $price_per_kg = $base * $weight * $age_factor * $condition_factor;

    return round($price_per_kg * $qty, 2);
}
?>
