<?php
require_once '../includes/header.php';

// Filter logic
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

<div class="container">
    <div style="padding: var(--space-lg) 0;">
        <div class="section-label">Fleet Identification</div>
        <h1 style="font-size: clamp(3rem, 8vw, 5rem); font-weight: 900; text-transform: uppercase; line-height: 1; margin-bottom: var(--space-lg);">Full<br>Collection.</h1>

        <!-- Filter Bar -->
        <div style="margin-bottom: var(--space-lg); border-top: 1px solid var(--border); padding-top: var(--space-md);">
            <form id="filter-form" style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-lg); align-items: flex-end;">
                <div class="form-group mb-0">
                    <label>Search Collection</label>
                    <input type="text" id="ajax-search" name="search" placeholder="START TYPING BRAND OR SERIES..." autocomplete="off">
                </div>
                <div class="form-group mb-0">
                    <label>Fleet Category</label>
                    <select id="ajax-type" name="type">
                        <option value="">ALL DEPLOYMENTS</option>
                        <option value="Car">SEDANS</option>
                        <option value="SUV">SUVS</option>
                        <option value="Luxury">EXOTICS</option>
                        <option value="Bike">MOTORBIKES</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="portfolio-grid" id="ajax-results">
            <?php foreach ($vehicles as $vehicle): ?>
                <a href="vehicle-details.php?id=<?php echo $vehicle['vehicle_id']; ?>" class="portfolio-item">
                    <div class="portfolio-img">
                        <img src="<?php echo getImagePath($vehicle['image'], $vehicle['brand'], $vehicle['vehicle_id']); ?>" alt="<?php echo $vehicle['brand']; ?>">
                    </div>
                    <div class="portfolio-info">
                        <h3><?php echo $vehicle['brand']; ?></h3>
                        <span class="rate"><?php echo formatCurrency($vehicle['price_per_day']); ?> / 24H</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function performAjaxSearch() {
        const searchVal = $('#ajax-search').val();
        const typeVal = $('#ajax-type').val();

        $.ajax({
            url: '../ajax/search-vehicles.php',
            method: 'POST',
            data: {
                search: searchVal,
                type: typeVal
            },
            success: function(response) {
                $('#ajax-results').html(response);
            }
        });
    }

    $('#ajax-search').on('keyup', performAjaxSearch);
    $('#ajax-type').on('change', performAjaxSearch);
    $('#filter-form').on('submit', function(e) { e.preventDefault(); });
});
</script>

<section style="height: 10vh;"></section>

<?php require_once '../includes/footer.php'; ?>
