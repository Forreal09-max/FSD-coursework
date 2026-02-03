<?php
require_once '../includes/header.php';

if (!isAdminLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_vehicles = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pending_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
?>

<div class="container" style="padding-top: var(--space-lg); padding-bottom: var(--space-lg);">
    <div class="admin-header">
        <div>
            <div class="section-label">Management Control</div>
            <h1 class="admin-title">Admin<br>Dashboard.</h1>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="manage-bookings.php" class="btn-cta" style="background: transparent; border: 1px solid #000; color: #000;">Manage Bookings</a>
            <a href="add.php" class="btn-cta">Add New Fleet</a>
        </div>
    </div>

    <div class="admin-stat-grid">
        <div class="admin-stat-box">
            <span class="count"><?php echo $total_users; ?></span>
            <span class="label">Total Members</span>
        </div>
        <div class="admin-stat-box">
            <span class="count"><?php echo $total_vehicles; ?></span>
            <span class="label">Active Fleet</span>
        </div>
        <div class="admin-stat-box">
            <span class="count"><?php echo $total_bookings; ?></span>
            <span class="label">Total Rentals</span>
        </div>
        <div class="admin-stat-box" style="border-width: 2px; border-color: #000;">
            <span class="count"><?php echo $pending_bookings; ?></span>
            <span class="label">Pending Requests</span>
        </div>
    </div>

    <div class="admin-main-grid">
        <div class="admin-table-container">
            <div class="section-label">Recent Fleet Updates</div>
            <div class="table-responsive">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th>Model</th>
                            <th>Category</th>
                            <th>Rate</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM vehicles ORDER BY vehicle_id DESC LIMIT 5");
                        while ($v = $stmt->fetch()) {
                            echo "<tr>
                                <td data-label='Model' style='text-transform: uppercase; font-weight: 800;'>{$v['brand']}</td>
                                <td data-label='Category' style='font-size: 0.8rem; color: #666;'>{$v['type']}</td>
                                <td data-label='Rate'>" . formatCurrency($v['price_per_day']) . "</td>
                                <td data-label='Status'>
                                    <span style='font-size: 0.6rem; font-weight: 900; letter-spacing: 1px; text-transform: uppercase;'>" . ($v['availability'] == 'available' ? 'Ready' : 'In Use') . "</span>
                                </td>
                                <td data-label='Action'>
                                    <div style='display: flex; gap: 1rem; justify-content: flex-end;'>
                                        <a href='edit.php?id={$v['vehicle_id']}' style='color: #000; text-decoration: none; font-weight: 800; font-size: 0.7rem; text-transform: uppercase;'>Edit</a>
                                        <a href='delete.php?id={$v['vehicle_id']}' style='color: #999; text-decoration: none; font-weight: 800; font-size: 0.7rem; text-transform: uppercase;' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </div>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align: right; margin-top: 1rem;">
                <a href="search.php" style="color: #000; font-weight: 900; text-decoration: none; border-bottom: 2px solid #000; font-size: 0.8rem; text-transform: uppercase;">View Full Collection →</a>
            </div>
        </div>
    </div>
</div>

<section style="height: 10vh;"></section>

<?php require_once '../includes/footer.php'; ?>
