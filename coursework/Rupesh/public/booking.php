<?php
require_once '../includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php', 'Identity Verification Required for Request.', 'danger');
}

$vehicle_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vehicle = getVehicle($pdo, $vehicle_id);

if (!$vehicle) {
    redirect('index.php', 'Invalid Collection Selection.', 'danger');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $user_id = $_SESSION['user_id'];

    $d1 = new DateTime($start_date);
    $d2 = new DateTime($end_date);
    $diff = $d2->diff($d1)->format("%a");
    
    if ($diff < 0) {
        $error = "Error: Invalid timeline selected.";
    } else {
        $days = $diff + 1;
        $total_cost = $days * $vehicle['price_per_day'];

        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, vehicle_id, start_date, end_date, total_cost, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        if ($stmt->execute([$user_id, $vehicle_id, $start_date, $end_date, $total_cost])) {
            redirect('booking-history.php', 'Fleet Request Submitted Successfully.', 'success');
        } else {
            $error = "Request Error: Please attempt again later.";
        }
    }
}
?>

<div class="container">
    <div class="detail-layout">
        <div class="detail-visual">
            <img src="<?php echo getImagePath($vehicle['image'], $vehicle['brand'], $vehicle['vehicle_id']); ?>" style="width: 80%; object-fit: contain;">
        </div>

        <div class="detail-content">
            <div class="section-label">Request Confirmation</div>
            <h1 style="font-size: 3rem; margin-bottom: 3rem;">Secure Your<br>Selection.</h1>
            
            <?php if (isset($error)): ?>
                <div style="background: #000; color: #fff; padding: 1.5rem; font-weight: 800; font-size: 0.7rem; letter-spacing: 1px; margin-bottom: 2rem;">
                    ! <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="booking.php?id=<?php echo $vehicle_id; ?>" method="POST" id="booking-form">
                <input type="hidden" id="vehicle_id" value="<?php echo $vehicle_id; ?>">
                <input type="hidden" id="price_per_day" value="<?php echo $vehicle['price_per_day']; ?>">
                
                <div class="form-group">
                    <label>Deployment Date</label>
                    <input type="date" name="start_date" id="start_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label>Return Deadline</label>
                    <input type="date" name="end_date" id="end_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div id="availability-status" style="margin-bottom: 2rem; font-weight: 800; font-size: 0.75rem; letter-spacing: 1px; display: none; text-transform: uppercase;"></div>

                <div style="border: 2px solid #000; padding: 2.5rem; margin-bottom: 3rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="font-size: 0.7rem; font-weight: 800; color: #999; text-transform: uppercase;">Per 24 Hours</span>
                        <span style="font-weight: 700;"><?php echo formatCurrency($vehicle['price_per_day']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="font-size: 0.7rem; font-weight: 800; color: #999; text-transform: uppercase;">Timeline Volume</span>
                        <span id="days-count" style="font-weight: 700;">0 Days</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; padding-top: 1.5rem; border-top: 1px solid #eee;">
                        <span style="font-size: 0.8rem; font-weight: 950; text-transform: uppercase;">Total Valuation</span>
                        <span id="total-cost" style="font-size: 1.75rem; font-weight: 950;">$0.00</span>
                    </div>
                </div>

                <button type="submit" class="btn-cta full-width" id="submit-booking" disabled>Confirm Request</button>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function calculateCost() {
        const start = $('#start_date').val();
        const end = $('#end_date').val();
        const price = parseFloat($('#price_per_day').val());
        const vehicleId = $('#vehicle_id').val();

        if (start && end) {
            const d1 = new Date(start);
            const d2 = new Date(end);
            const diffTime = d2 - d1;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (diffDays > 0) {
                $('#days-count').text(diffDays + ' Days');
                $('#total-cost').text('$' + (diffDays * price).toFixed(2));
                
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
                            const statusDiv = $('#availability-status');
                            statusDiv.show();
                            if (data.available) {
                                statusDiv.text('✓ Fleet Item is available').css('color', '#000');
                                $('#submit-booking').prop('disabled', false);
                            } else {
                                statusDiv.text('✗ Item already reserved').css('color', '#999');
                                $('#submit-booking').prop('disabled', true);
                            }
                        } catch(e) { console.error("AJAX Error"); }
                    }
                });
            } else {
                $('#days-count').text('0 Days');
                $('#total-cost').text('$0.00');
                $('#submit-booking').prop('disabled', true);
                $('#availability-status').hide();
            }
        }
    }
    $('#start_date, #end_date').on('change', calculateCost);
});
</script>

<?php require_once '../includes/footer.php'; ?>
