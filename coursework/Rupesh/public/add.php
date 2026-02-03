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
    $image_data = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $source = $_FILES['image']['tmp_name'];
        
        // Check if GD extension is enabled
        if (extension_loaded('gd')) {
            $imgInfo = getimagesize($source);
            $mime = $imgInfo['mime'];
            
            // Target: Max 800px width/height to stay safely under max_allowed_packet
            $maxWidth = 800;
            $maxHeight = 800;
            
            list($origWidth, $origHeight) = $imgInfo;
            $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight, 1);
            
            $newWidth = $origWidth * $ratio;
            $newHeight = $origHeight * $ratio;
            
            $srcObj = imagecreatefromstring(file_get_contents($source));
            if ($srcObj) {
                $dstObj = imagecreatetruecolor($newWidth, $newHeight);
                
                // Preserve transparency for PNG/GIF
                if ($mime == 'image/png' || $mime == 'image/gif') {
                    imagecolortransparent($dstObj, imagecolorallocatealpha($dstObj, 0, 0, 0, 127));
                    imagealphablending($dstObj, false);
                    imagesavealpha($dstObj, true);
                }
                
                imagecopyresampled($dstObj, $srcObj, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                
                // Capture output buffer
                ob_start();
                if ($mime == 'image/jpeg') {
                    imagejpeg($dstObj, null, 75); // 75% quality
                } elseif ($mime == 'image/png') {
                    imagepng($dstObj, null, 6); // Compression level 6
                } else {
                    imagejpeg($dstObj, null, 75); // Fallback
                }
                $image_data = ob_get_clean();
                
                imagedestroy($srcObj);
                imagedestroy($dstObj);
            } else {
                // GD failed to load string
                $image_data = file_get_contents($source);
            }
        } else {
            // GD Extension is MISSING - Fallback
            // If file is > 1MB, prevent upload to avoid Packet Error
            if ($_FILES['image']['size'] > 1000000) { 
                $error = "System Error: Image is too large (>1MB) and server cannot resize it because 'GD Extension' is missing. Please upload a smaller image.";
            } else {
                $image_data = file_get_contents($source);
            }
        }
    }

    if (!isset($error)) {
        $stmt = $pdo->prepare("INSERT INTO vehicles (brand, type, price_per_day, description, availability, image) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$brand, $type, $price, $description, $availability, $image_data])) {
            redirect('dashboard.php', 'Fleet Addition Successful', 'success');
        } else { $error = "Inventory Update Failed"; }
    }
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
                    // Create new File object
                    const newFile = new File([blob], file.name, {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });
                    
                    // Replace input file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(newFile);
                    document.getElementById('fileInput').files = dataTransfer.files;
                    
                    // Update preview
                    const preview = document.getElementById('preview-img');
                    preview.src = URL.createObjectURL(newFile);
                    document.getElementById('image-preview').style.display = 'block';
                    
                }, 'image/jpeg', 0.7);
            }
            img.src = event.target.result;
        }
        reader.readAsDataURL(file);
    } else {
        // Just show preview for small images
        if (file.type.match(/image.*/)) {
            const reader = new FileReader();
            reader.onload = function(e) {
                 const preview = document.getElementById('preview-img');
                 preview.src = e.target.result;
                 document.getElementById('image-preview').style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }
});
</script>
