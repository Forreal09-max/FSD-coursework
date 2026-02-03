<?php
// Centralized Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Project Base URL (Detects automatically for XAMPP or Remote Server)
$script_path = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$base_path = '/';
if (strpos($script_path, '/public/') !== false) {
    $base_path = substr($script_path, 0, strpos($script_path, '/public/') + 1);
} else {
    // Fallback if not accessed through public/
    $base_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__))) . '/';
    $base_path = '/' . ltrim($base_path, '/');
}
define('BASE_URL', $base_path);

// Database connection
$pdo = require_once __DIR__ . '/../config/db.php';
?>
