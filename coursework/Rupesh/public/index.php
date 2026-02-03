<?php
require_once '../includes/header.php';

$type = isset($_GET['type']) ? sanitize($_GET['type']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$query = "SELECT * FROM vehicles WHERE availability = 'available'";
$params = [];

if ($type) {
    $query .= " AND type = ?";
    $params[] = $type;
}
if ($search) {
    $query .= " AND (brand LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$vehicles = $stmt->fetchAll();
?>

<section class="hero-block mt-0">
    <div class="hero-text">
        <div class="section-label">Fleet Excellence</div>
        <h1>Explore.<br>Innovate.<br>Drive.</h1>
        <p>A curated collection of premium vehicles tailored for the visionary driver. Redefining mobility through design and performance.</p>
        <a href="#collection" class="btn-cta">Browse Fleet</a>
    </div>
    <div class="hero-image-box">
        <img src="<?php echo BASE_URL; ?>assets/images/ui_hero.png" alt="Premium Supercar">
    </div>
</section>

<!-- Visual Narrative Spread -->
<div class="visual-spread">
    <div class="visual-item">
        <img src="<?php echo BASE_URL; ?>assets/images/ui_heritage.png" alt="Classic Heritage">
    </div>
    <div class="visual-item">
        <img src="<?php echo BASE_URL; ?>assets/images/ui_ducati.png" alt="Night Rider">
    </div>
</div>

<div class="dark-collection" id="collection">
    <div class="container">
        <div class="section-label" style="color: #444;">Selection 2026</div>
        <div class="portfolio-grid">
            <?php foreach ($vehicles as $vehicle): ?>
                <a href="vehicle-details.php?id=<?php echo $vehicle['vehicle_id']; ?>" class="portfolio-item">
                    <div class="portfolio-img">
                    <img src="<?php echo getImagePath($vehicle['image'], $vehicle['brand'], $vehicle['vehicle_id']); ?>" alt="<?php echo $vehicle['brand']; ?>">
                </div>
                    <div class="portfolio-info">
                        <h3><?php echo $vehicle['brand']; ?></h3>
                        <span class="rate"><?php echo formatCurrency($vehicle['price_per_day']); ?> / DAY</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<section>
    <div class="container">
        <div class="section-label">Find Your Match</div>
        <form action="index.php" method="GET" class="responsive-filter-form">
            <div class="form-group mb-0">
                <label>Searching for</label>
                <input type="text" name="search" placeholder="Enter vehicle brand..." value="<?php echo $search; ?>">
            </div>
            <div class="form-group mb-0">
                <label>Category</label>
                <select name="type" onchange="this.form.submit()">
                    <option value="">All Fleets</option>
                    <option value="Car" <?php echo $type == 'Car' ? 'selected' : ''; ?>>Sedans</option>
                    <option value="SUV" <?php echo $type == 'SUV' ? 'selected' : ''; ?>>SUVs</option>
                    <option value="Luxury" <?php echo $type == 'Luxury' ? 'selected' : ''; ?>>Exotics</option>
                    <option value="Bike" <?php echo $type == 'Bike' ? 'selected' : ''; ?>>Motorbikes</option>
                </select>
            </div>
            <button type="submit" class="btn-cta">Identify</button>
        </form>
    </div>
</section>

<!-- Final Creative CTA -->
<div class="visual-spread wide-narrative">
    <div class="visual-item narrative-full">
        <img src="<?php echo BASE_URL; ?>assets/images/ui_moto_dark.png" alt="Blacked Out Motorbike">
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
