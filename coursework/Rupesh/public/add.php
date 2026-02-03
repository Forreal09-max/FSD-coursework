<?php
require_once '../includes/header.php';

if (!isAdminLoggedIn()) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand = sanitize($_POST['brand']);
    $type = sanitize($_POST['type']);
    $price = $_POST['price_per_day'];
    $description = sanitize($_POST['description']);
    $availability = $_POST['availability'];
    
    $image_data = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_data = file_get_contents($_FILES['image']['tmp_name']);
    }

    $stmt = $pdo->prepare("INSERT INTO vehicles (brand, type, price_per_day, description, availability, image) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$brand, $type, $price, $description, $availability, $image_data])) {
        redirect('dashboard.php', 'Fleet Addition Successful', 'success');
    } else { $error = "Inventory Update Failed"; }
}
?>

<div class="container">
    <div class="auth-wrapper" style="max-width: 800px;">
        <div class="section-label">Inventory Management</div>
        <h1 class="admin-title">Expand<br>The Fleet.</h1>
        
        <?php if (isset($error)): ?>
            <div style="background: #000; color: #fff; padding: 1.5rem; font-weight: 800; font-size: 0.75rem; letter-spacing: 1px; margin-bottom: 2rem;">
                ! <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="add.php" method="POST" enctype="multipart/form-data">
            <div class="admin-form-grid">
                <div class="form-group">
                    <label>Brand Designation</label>
                    <input type="text" name="brand" required placeholder="e.g. TESLA MODEL S">
                </div>
                <div class="form-group">
                    <label>Fleet Category</label>
                    <select name="type" required>
                        <option value="Car">SEDAN</option>
                        <option value="Bike">MOTORBIKE</option>
                        <option value="SUV">SUV</option>
                        <option value="Luxury">EXOTIC</option>
                    </select>
                </div>
            </div>

            <div class="admin-form-grid">
                <div class="form-group">
                    <label>Valuation (Per 24h)</label>
                    <input type="number" name="price_per_day" step="0.01" required placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Deployment State</label>
                    <select name="availability" required>
                        <option value="available">READY FOR DEPLOYMENT</option>
                        <option value="unavailable">MAINTENANCE / UNAVAILABLE</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Inventory Narrative</label>
                <textarea name="description" rows="4" placeholder="Detail the performance and visual features..."></textarea>
            </div>

            <div class="form-group">
                <label>Visual Asset (IMAGE)</label>
                <div class="drop-zone" id="drop-zone">
                    <span class="drop-zone__prompt">Drop file here or click to upload</span>
                    <input type="file" name="image" class="drop-zone__input" id="fileInput" accept="image/*">
                </div>
                <div id="image-preview" style="margin-top: 1rem; display: none;">
                    <img id="preview-img" src="" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 4px;">
                </div>
            </div>

            <button type="submit" class="btn-cta full-width">Commit to Inventory</button>
        </form>
    </div>
</div>

<section style="height: 10vh;"></section>

<?php require_once '../includes/footer.php'; ?>
