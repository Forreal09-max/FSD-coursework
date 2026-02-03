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
        $source = $_FILES['image']['tmp_name'];
        
        // Determine if we need to resize
        if (extension_loaded('gd')) {
            $imgInfo = getimagesize($source);
            $mime = $imgInfo['mime'];
            
            // Limit dimensions
            $maxWidth = 800; 
            $maxHeight = 800;
            
            list($origWidth, $origHeight) = $imgInfo;
            $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight, 1);
            
            $newWidth = $origWidth * $ratio;
            $newHeight = $origHeight * $ratio;
            
            $srcObj = imagecreatefromstring(file_get_contents($source));
            if ($srcObj) {
                $dstObj = imagecreatetruecolor($newWidth, $newHeight);
                
                // Transparency support
                if ($mime == 'image/png' || $mime == 'image/gif') {
                    imagecolortransparent($dstObj, imagecolorallocatealpha($dstObj, 0, 0, 0, 127));
                    imagealphablending($dstObj, false);
                    imagesavealpha($dstObj, true);
                }
                
                imagecopyresampled($dstObj, $srcObj, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                
                ob_start();
                if ($mime == 'image/jpeg') {
                    imagejpeg($dstObj, null, 75);
                } elseif ($mime == 'image/png') {
                    imagepng($dstObj, null, 6);
                } else {
                    imagejpeg($dstObj, null, 75);
                }
                $image_data = ob_get_clean();
                
                imagedestroy($srcObj);
                imagedestroy($dstObj);
            } else {
                $image_data = file_get_contents($source);
            }
        } else {
             // Fallback: Check size to prevent crash
             if ($_FILES['image']['size'] > 1000000) { 
                $error = "Update Error: Image > 1MB and cannot be resized (GD Missing).";
                // Keep old image
                $image_data = $vehicle['image'];
            } else {
                $image_data = file_get_contents($source);
            }
        }
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
                    <input type="text" name="brand" required value="<?php echo isset($_POST['brand']) ? sanitize($_POST['brand']) : $vehicle['brand']; ?>">
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
                    <input type="number" name="price_per_day" step="0.01" required value="<?php echo isset($_POST['price_per_day']) ? $_POST['price_per_day'] : $vehicle['price_per_day']; ?>">
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
                    <input type="file" name="image" id="fileInput" accept="image/*">
                </div>
            </div>

            <button type="submit" class="btn-cta full-width">Update Inventory Record</button>
        </form>
    </div>
</div>

<section style="height: 10vh;"></section>

<?php require_once '../includes/footer.php'; ?>

<script>
// Client-Side Image Compression to bypass Server Limits
document.getElementById('fileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    // Only process if image and > 500KB
    if (file.type.match(/image.*/) && file.size > 500000) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                // Resize logic
                const MAX_WIDTH = 800;
                const MAX_HEIGHT = 800;
                let width = img.width;
                let height = img.height;
                
                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }
                
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);
                
                // Convert to Blob (JPEG 70%)
                canvas.toBlob(function(blob) {
                    const newFile = new File([blob], file.name, {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });
                    
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(newFile);
                    document.getElementById('fileInput').files = dataTransfer.files;
                    
                }, 'image/jpeg', 0.7);
            }
            img.src = event.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>
