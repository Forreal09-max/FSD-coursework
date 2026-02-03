<?php
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehicle_id = (int)$_POST['vehicle_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Check if there are any overlapping confirmed bookings
    // Logic: (StartA <= EndB) and (EndA >= StartB)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM bookings 
        WHERE vehicle_id = ? 
        AND status = 'confirmed'
        AND (
            (start_date <= ?) AND (end_date >= ?)
        )
    ");
    
    $stmt->execute([$vehicle_id, $end_date, $start_date]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['available' => ($result['count'] == 0)]);
    exit();
}
?>
