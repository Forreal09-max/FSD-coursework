<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$search = isset($_POST['search']) ? sanitize($_POST['search']) : '';
$type = isset($_POST['type']) ? sanitize($_POST['type']) : '';

$query = "SELECT * FROM vehicles WHERE availability = 'available'";
$params = [];

if ($type) {
    $query .= " AND type = ?";
    $params[] = $type;
}
if ($search) {
    $query .= " AND (brand LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$vehicles = $stmt->fetchAll();

if (count($vehicles) > 0) {
    foreach ($vehicles as $vehicle) {
        $img = getImagePath($vehicle['image'], $vehicle['brand'], $vehicle['vehicle_id']);
        $price = formatCurrency($vehicle['price_per_day']);
        
        echo "
            <a href='vehicle-details.php?id={$vehicle['vehicle_id']}' class='portfolio-item'>
                <div class='portfolio-img'>
                    <img src='{$img}' alt='{$vehicle['brand']}'>
                </div>
                <div class='portfolio-info'>
                    <h3>{$vehicle['brand']}</h3>
                    <span class='rate'>{$price} / 24H</span>
                </div>
            </a>
        ";
    }
} else {
    echo "
        <div style='grid-column: span 2; padding: var(--space-xl) 0; border-top: 1px solid var(--border); text-align: center;'>
            <p style='color: var(--text-muted); font-size: 1.5rem; font-weight: 600;'>No matching assets found in the current collection.</p>
        </div>
    ";
}
?>
