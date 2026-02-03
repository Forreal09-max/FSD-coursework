<?php
require_once '../includes/header.php';
?>

<div class="container">
    <div style="padding: var(--space-lg) 0;">
        <div class="section-label">Member Records</div>
        <h1 style="font-size: 3.5rem; font-weight: 900; text-transform: uppercase; line-height: 1; margin-bottom: var(--space-lg);">Rental<br>History.</h1>
        
        <?php
        if (!isLoggedIn()) redirect('login.php');
        $user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT b.*, v.brand, v.image, v.vehicle_id FROM bookings b JOIN vehicles v ON b.vehicle_id = v.vehicle_id WHERE b.user_id = ? ORDER BY b.booking_date DESC");
        $stmt->execute([$user_id]);
        $bookings = $stmt->fetchAll();
        ?>

        <?php if (count($bookings) > 0): ?>
            <table class="clean-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Collection Item</th>
                        <th>Timeline</th>
                        <th>Value</th>
                        <th>State</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 1.5rem;">
                                    <div class="portfolio-img" style="width: 80px; aspect-ratio: 4/3; margin-bottom: 0;">
                                        <img src="<?php echo getImagePath($b['image'], $b['brand'], $b['vehicle_id']); ?>" style="width: 70%;">
                                    </div>
                                    <div style="font-size: 1.25rem; font-weight: 900; text-transform: uppercase;"><?php echo $b['brand']; ?></div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem; color: #666;">
                                    <?php echo date('M d', strtotime($b['start_date'])); ?> — <?php echo date('M d, Y', strtotime($b['end_date'])); ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 900; font-size: 1.1rem;"><?php echo formatCurrency($b['total_cost']); ?></div>
                            </td>
                            <td>
                                <span style="font-size: 0.65rem; font-weight: 900; letter-spacing: 2px; text-transform: uppercase;">
                                    <?php echo $b['status']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="padding: var(--space-lg) 0; border-top: 1px solid var(--border);">
                <p style="color: var(--text-muted); font-size: 1.5rem; font-weight: 600;">No active rental records found in your portfolio.</p>
                <a href="index.php" style="color: #000; font-weight: 900; text-decoration: none; border-bottom: 2px solid #000; display: inline-block; margin-top: 2rem;">Explore Fleet</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<section style="height: 10vh;"></section>

<?php require_once '../includes/footer.php'; ?>
