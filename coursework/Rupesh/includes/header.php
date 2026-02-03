<?php
// Main Header Partial
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MARIA | Premium Fleet</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;900&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="<?php echo BASE_URL; ?>public/index.php" class="logo">
                    MARIA FLEET
                </a>
                <div class="mobile-menu-toggle" id="mobile-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <ul class="nav-links" id="nav-menu">
                    <li><a href="<?php echo BASE_URL; ?>public/search.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'search.php' || basename($_SERVER['PHP_SELF']) == 'collection.php') ? 'active' : ''; ?>">Collection</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="<?php echo BASE_URL; ?>public/booking-history.php">My Rentals</a></li>
                        <li><a href="<?php echo BASE_URL; ?>public/logout.php">Logout</a></li>
                    <?php elseif (isAdminLoggedIn()): ?>
                        <li><a href="<?php echo BASE_URL; ?>public/dashboard.php">Admin Panel</a></li>
                        <li><a href="<?php echo BASE_URL; ?>public/logout.php">Sign Out</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>public/login.php">Sign In</a></li>
                        <li><a href="<?php echo BASE_URL; ?>public/register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div style="padding: 2rem 0;">
            <div style="background: #000; color: #fff; padding: 1.5rem; text-transform: uppercase; font-weight: 800; font-size: 0.8rem; letter-spacing: 1px;">
                <?php 
                    echo $_SESSION['flash_message']; 
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                ?>
            </div>
        </div>
    <?php endif; ?>
