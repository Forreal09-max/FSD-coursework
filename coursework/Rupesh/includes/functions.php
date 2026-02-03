<?php
// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Redirect with message
function redirect($url, $message = '', $type = 'success') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit();
}

// Get vehicle details by ID
function getVehicle($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE vehicle_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Format currency
function formatCurrency($amount) {
    return "$" . number_format($amount, 2);
}

// Get correct image path
function getImagePath($path, $brand = 'Vehicle', $id = null) {
    if ($id) {
        return BASE_URL . 'public/view-image.php?id=' . $id;
    }
    if (!$path) {
        return "https://placehold.co/600x400/1e293b/f8fafc?text=" . urlencode($brand);
    }
    if (strpos($path, 'http') === 0) {
        return $path;
    }
    return BASE_URL . ltrim($path, '/');
}
?>
