<?php
require_once '../includes/header.php';

if (!isAdminLoggedIn()) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vehicle = getVehicle($pdo, $id);

if (!$vehicle) {
    redirect('dashboard.php', 'Invalid Fleet Reference', 'danger');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand = sanitize($_POST['brand']);
    $type = sanitize($_POST['type']);
    $price = $_POST['price_per_day'];
    $description = sanitize($_POST['description']);
    $availability = $_POST['availability'];
    
    $image_data = $vehicle['image']; // Keep old binary data
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_data = file_get_contents($_FILES['image']['tmp_name']);
    }

    $stmt = $pdo->prepare("UPDATE vehicles SET brand=?, type=?, price_per_day=?, description=?, availability=?, image=? WHERE vehicle_id=?");
    if ($stmt->execute([$brand, $type, $price, $description, $availability, $image_data, $id])) {
        redirect('dashboard.php', 'Fleet Revision Successful', 'success');
    } else { $error = "Inventory Revision Failed"; }
}
?>

<div class="container">
    <div class="auth-wrapper" style="max-width: 800px;">
        <div class="section-label">Inventory Modification</div>
        <h1 class="admin-title">Modify<br>The Fleet.</h1>
        
        <?php if (isset($error)): ?>
            <div style="background: #000; color: #fff; padding: 1.5rem; font-weight: 800; font-size: 0.75rem; letter-spacing: 1px; margin-bottom: 2rem;">
                ! <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <div class="admin-form-grid">
                <div class="form-group">
                    <label>Brand Designation</label>
                    <input type="text" name="brand" required value="<?php echo $vehicle['brand']; ?>">
                </div>
                <div class="form-group">
                    <label>Fleet Category</label>
                    <select name="type" required>
                        <option value="Car" <?php echo $vehicle['type'] == 'Car' ? 'selected' : ''; ?>>SEDAN</option>
                        <option value="Bike" <?php echo $vehicle['type'] == 'Bike' ? 'selected' : ''; ?>>MOTORBIKE</option>
                        <option value="SUV" <?php echo $vehicle['type'] == 'SUV' ? 'selected' : ''; ?>>SUV</option>
                        <option value="Luxury" <?php echo $vehicle['type'] == 'Luxury' ? 'selected' : ''; ?>>EXOTIC</option>
                    </select>
                </div>
            </div>

            <div class="admin-form-grid">
                <div class="form-group">
                    <label>Valuation (Per 24h)</label>
                    <input type="number" name="price_per_day" step="0.01" required value="<?php echo $vehicle['price_per_day']; ?>">
                </div>
                <div class="form-group">
                    <label>Deployment State</label>
                    <select name="availability" required>
                        <option value="available" <?php echo $vehicle['availability'] == 'available' ? 'selected' : ''; ?>>READY FOR DEPLOYMENT</option>
                        <option value="unavailable" <?php echo $vehicle['availability'] == 'unavailable' ? 'selected' : ''; ?>>MAINTENANCE / UNAVAILABLE</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Inventory Narrative</label>
                <textarea name="description" rows="4"><?php echo $vehicle['description']; ?></textarea>
            </div>

            <div class="form-group">
                <label>Current Visual Asset</label>
                <div style="padding: 2rem; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                    <img src="<?php echo getImagePath($vehicle['image'], $vehicle['brand'], $id); ?>" style="height: 120px; object-fit: contain;">
                </div>
                <label>Replace Asset (OPTIONAL)</label>
                <div style="padding: 2rem; border: 2px dashed var(--border); text-align: center;">
                    <input type="file" name="image" accept="image/*">
                </div>
            </div>

            <button type="submit" class="btn-cta full-width">Update Inventory Record</button>
        </form>
    </div>
</div>

<section style="height: 10vh;"></section>

<?php require_once '../includes/footer.php'; ?>
