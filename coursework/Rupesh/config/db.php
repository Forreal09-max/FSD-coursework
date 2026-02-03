<?php
// PHP Script to handle Database Connection and Auto-Initialization
// Located in database/db.php

$host = 'localhost';
$db_user = 'np03cs4a240297';
$db_name = 'np03cs4a240297';
// $db_password = '';
$db_pass = 'Q57i3rHBX9';
// $db_name = 'vehicle_rental_db';



try {
    // 1. Connect to MySQL Server
    $pdo = new PDO("mysql:host=$host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Create Database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db_name`");

    // 3. Create Tables
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS vehicles (
        vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
        type VARCHAR(50) NOT NULL,
        brand VARCHAR(100) NOT NULL,
        price_per_day DECIMAL(10, 2) NOT NULL,
        availability ENUM('available', 'unavailable') DEFAULT 'available',
        image LONGBLOB,
        description TEXT
    );

    CREATE TABLE IF NOT EXISTS bookings (
        booking_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        vehicle_id INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        total_cost DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS admins (
        admin_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    );
    ";
    
    $pdo->exec($sql);
    
    // Ensure image column is LONGBLOB (upgrade if it was VARCHAR)
    try { $pdo->exec("ALTER TABLE vehicles MODIFY COLUMN image LONGBLOB"); } catch(Exception $e) {}

    // 4. Force Reset Admin Credentials (admin / admin123)
    $admin_user = 'admin';
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    
    $check = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
    $check->execute([$admin_user]);
    
    if ($check->fetchColumn() > 0) {
        // Update existing admin to ensure password is admin123
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
        $stmt->execute([$admin_pass, $admin_user]);
    } else {
        // Create admin if not exists
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute([$admin_user, $admin_pass]);
    }

} catch (PDOException $e) {
    die("Database Initialization Error: " . $e->getMessage());
}

return $pdo;
?>
