<?php
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vehicle = getVehicle($pdo, $id);

if (!$vehicle) redirect('index.php');

$currentUser = null;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch();
}
?>

<div class="container">
    <div class="detail-layout">
        <div class="detail-visual">
            <img src="<?php echo getImagePath($vehicle['image'], $vehicle['brand'], $vehicle['vehicle_id']); ?>" style="width: 80%; object-fit: contain;">
        </div>
        
        <div class="detail-content">
            <div class="section-label"><?php echo $vehicle['type']; ?> Selection</div>
            <h1><?php echo $vehicle['brand']; ?></h1>
            <p class="desc"><?php echo nl2br($vehicle['description']); ?></p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 4rem;">
                <div style="border: 1px solid var(--border); padding: 2rem;">
                    <div style="font-size: 0.65rem; font-weight: 800; color: #999; text-transform: uppercase;">Performance</div>
                    <div style="font-weight: 700;">Optimized</div>
                </div>
                <div style="border: 1px solid var(--border); padding: 2rem;">
                    <div style="font-size: 0.65rem; font-weight: 800; color: #999; text-transform: uppercase;">Availability</div>
                    <div style="font-weight: 700;">Immediate</div>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: flex-end; border-top: 2px solid #000; padding-top: 3rem;">
                <div>
                    <div style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #999;">Engagement Rate</div>
                    <div style="font-size: 2.5rem; font-weight: 900; line-height: 1;"><?php echo formatCurrency($vehicle['price_per_day']); ?></div>
                    <div style="font-size: 0.9rem; font-weight: 600;">PER 24 HOURS</div>
                </div>
                <?php if (isLoggedIn()): ?>
                    <button class="btn-cta" id="open-request-modal">Request Fleet</button>
                <?php else: ?>
                    <a href="login.php" class="btn-cta">Sign in to Access</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Request Modal Popup -->
<div class="modal-overlay" id="request-modal">
    <div class="modal-container">
        <div class="modal-close" id="close-modal">Close Window</div>
        
        <div class="modal-header">
            <div class="section-label">Fleet Access Protocol</div>
            <h2>Secure Your<br>Deployment.</h2>
        </div>

        <form action="booking.php?id=<?php echo $id; ?>" method="POST" id="modal-booking-form">
            <input type="hidden" id="modal-vehicle-id" value="<?php echo $id; ?>">
            <input type="hidden" id="modal-price-per-day" value="<?php echo $vehicle['price_per_day']; ?>">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; margin-bottom: 3rem;">
                <!-- Identity Confirmation Section -->
                <div>
                    <div class="section-label">Member Identity</div>
                    <div class="form-group">
                        <label>Signature</label>
                        <input type="text" value="<?php echo $currentUser['name'] ?? ''; ?>" readonly style="opacity: 0.5;">
                    </div>
                    <div class="form-group">
                        <label>Verified Email</label>
                        <input type="text" value="<?php echo $currentUser['email'] ?? ''; ?>" readonly style="opacity: 0.5;">
                    </div>
                    <div class="form-group">
                        <label>Contact Number (Required)</label>
                        <input type="text" name="phone" value="<?php echo $currentUser['phone'] ?? ''; ?>" required placeholder="ENTER DIGITS">
                    </div>
                </div>

                <!-- Timeline Selection Section -->
                <div>
                    <div class="section-label">Rental Timeline</div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" id="modal-start-date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" id="modal-end-date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div id="modal-availability-msg" style="margin-top: 1rem; font-weight: 900; font-size: 0.65rem; letter-spacing: 1px; display: none; text-transform: uppercase;"></div>
                </div>
            </div>

            <div style="background: var(--bg-light); padding: 3rem; margin-bottom: 3rem; border: 1px solid var(--border);">
                <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <div>
                        <div class="section-label" style="margin-bottom: 0.5rem;">Calculated Valuation</div>
                        <div style="font-size: 3rem; font-weight: 950; display: block; line-height: 1;" id="modal-total-valuation">$0.00</div>
                        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted);" id="modal-days-label">0 DAYS TOTAL</div>
                    </div>
                    <button type="submit" class="btn-cta" id="modal-submit-btn" disabled>Confirm Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Modal Toggle
    $('#open-request-modal').on('click', function(e) {
        e.preventDefault();
        $('#request-modal').fadeIn(400).css('display', 'flex');
        $('body').css('overflow', 'hidden'); 
    });

    $('#close-modal').on('click', function() {
        $('#request-modal').fadeOut(400);
        $('body').css('overflow', 'auto');
    });

    // Close on overlay click
    $('.modal-overlay').on('click', function(e) {
        if ($(e.target).hasClass('modal-overlay')) {
            $(this).fadeOut(400);
            $('body').css('overflow', 'auto');
        }
    });

    // Booking Logic
    function updateModalCost() {
        const start = $('#modal-start-date').val();
        const end = $('#modal-end-date').val();
        const price = parseFloat($('#modal-price-per-day').val());
        const vehicleId = $('#modal-vehicle-id').val();

        if (start && end) {
            const d1 = new Date(start);
            const d2 = new Date(end);
            const diffTime = d2 - d1;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (diffDays > 0) {
                $('#modal-days-label').text(diffDays + ' DAYS TOTAL');
                $('#modal-total-valuation').text('$' + (diffDays * price).toFixed(2));
                
                // AJAX Availability Check
                $.ajax({
                    url: '<?php echo BASE_URL; ?>ajax/check-availability.php',
                    method: 'POST',
                    data: {
                        vehicle_id: vehicleId,
                        start_date: start,
                        end_date: end
                    },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            const msg = $('#modal-availability-msg');
                            msg.show();
                            if (data.available) {
                                msg.text('✓ FLEET ITEM IS AVAILABLE').css('color', '#000');
                                $('#modal-submit-btn').prop('disabled', false);
                            } else {
                                msg.text('✗ ITEM RESERVED BY OTHER MEMBER').css('color', '#999');
                                $('#modal-submit-btn').prop('disabled', true);
                            }
                        } catch(e) {}
                    }
                });
            } else {
                $('#modal-days-label').text('0 DAYS');
                $('#modal-total-valuation').text('$0.00');
                $('#modal-submit-btn').prop('disabled', true);
                $('#modal-availability-msg').hide();
            }
        }
    }

    $('#modal-start-date, #modal-end-date').on('change', updateModalCost);
});
</script>

<section style="height: 10vh;"></section>

<?php require_once '../includes/footer.php'; ?>
