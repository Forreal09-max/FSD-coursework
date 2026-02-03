<?php
require_once '../includes/header.php';

if (!isAdminLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Logic for approval/cancellation
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $booking_id = (int)$_GET['id'];
    $status = ($action == 'confirm') ? 'confirmed' : 'cancelled';
    
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    $stmt->execute([$status, $booking_id]);
    redirect('manage-bookings.php', "Rental status updated to {$status}.");
}

$stmt = $pdo->query("
    SELECT b.*, u.name as user_name, v.brand 
    FROM bookings b 
    JOIN users u ON b.user_id = u.user_id 
    JOIN vehicles v ON b.vehicle_id = v.vehicle_id 
    ORDER BY b.booking_date DESC
");
$bookings = $stmt->fetchAll();
?>

<div class="container" style="padding-top: var(--space-lg); padding-bottom: var(--space-lg);">
    <div class="section-label">Fleet Operations</div>
    <h1 class="admin-title">Manage<br>Requests.</h1>

    <div class="admin-table-container">
        <table class="clean-table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Asset</th>
                    <th>Timeline</th>
                    <th>Valuation</th>
                    <th>State</th>
                    <th>Operation</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td data-label="Member">
                            <div style="font-weight: 800; text-transform: uppercase;"><?php echo $b['user_name']; ?></div>
                        </td>
                        <td data-label="Asset">
                            <div style="font-size: 0.9rem; font-weight: 600;"><?php echo $b['brand']; ?></div>
                        </td>
                        <td data-label="Timeline">
                            <div style="font-size: 0.8rem; color: #666;">
                                <?php echo date('M d', strtotime($b['start_date'])); ?> — <?php echo date('M d', strtotime($b['end_date'])); ?>
                            </div>
                        </td>
                        <td data-label="Valuation">
                            <div style="font-weight: 700;"><?php echo formatCurrency($b['total_cost']); ?></div>
                        </td>
                        <td data-label="State">
                            <span style="font-size: 0.6rem; font-weight: 900; letter-spacing: 1px; text-transform: uppercase;">
                                <?php echo $b['status']; ?>
                            </span>
                        </td>
                        <td data-label="Operation">
                            <?php if ($b['status'] == 'pending'): ?>
                                <div style="display: flex; gap: 1rem;">
                                    <a href="manage-bookings.php?action=confirm&id=<?php echo $b['booking_id']; ?>" style="color: #000; text-decoration: none; font-weight: 800; font-size: 0.65rem; text-transform: uppercase;">Approve</a>
                                    <a href="manage-bookings.php?action=cancel&id=<?php echo $b['booking_id']; ?>" style="color: #999; text-decoration: none; font-weight: 800; font-size: 0.65rem; text-transform: uppercase;">Reject</a>
                                </div>
                            <?php else: ?>
                                <span style="font-size: 0.6rem; color: #ccc;">FINALIZED</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<section style="height: 10vh;"></section>

<?php require_once '../includes/footer.php'; ?>
