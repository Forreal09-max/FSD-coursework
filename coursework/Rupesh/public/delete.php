<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE vehicle_id = ?");
    if ($stmt->execute([$id])) {
        redirect('dashboard.php', 'Vehicle deleted successfully!', 'success');
    } else {
        redirect('dashboard.php', 'Failed to delete vehicle.', 'danger');
    }
} else {
    redirect('dashboard.php');
}
?>
