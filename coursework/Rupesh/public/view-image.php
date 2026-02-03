<?php
require_once '../includes/config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT image FROM vehicles WHERE vehicle_id = ?");
    $stmt->execute([$id]);
    $vehicle = $stmt->fetch();

    if ($vehicle && $vehicle['image']) {
        header("Content-Type: image/jpeg"); 
        echo $vehicle['image'];
        exit();
    }
}

// Fallback to placeholder if no image found in DB
header("Location: https://placehold.co/600x400/1e293b/f8fafc?text=No+Image");
exit();
