<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$db = getDB();
$action = $_GET['action'] ?? 'edit';
$roomId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$room = null;

$success = false;
$error = '';

// Get room data if editing
if ($roomId > 0) {
    $stmt = $db->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$roomId]);
    $room = $stmt->fetch();
    if (!$room) {
        header('Location: rooms.php');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $room_type = sanitize($_POST['room_type'] ?? 'guest_house');
    $pricing_type = sanitize($_POST['pricing_type'] ?? 'per_night');
    $max_occupancy = intval($_POST['max_occupancy'] ?? 2);
    $area_sqft = !empty($_POST['area_sqft']) ? intval($_POST['area_sqft']) : null;
    $amenities = sanitize($_POST['amenities'] ?? '');
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;
    
    // Auto-set pricing type based on room type if not explicitly set
    if ($room_type === 'pg' && $pricing_type === 'per_night') {
        $pricing_type = 'per_month';
    } elseif ($room_type === 'guest_house' && $pricing_type === 'per_month') {
        $pricing_type = 'per_night';
    }
    
    if (empty($title) || $price <= 0) {
        $error = 'Title and valid price are required.';
    } else {
        try {
            $slug = generateSlug($title);
            
            // Ensure unique slug
            if ($roomId > 0) {
                $stmt = $db->prepare("SELECT id FROM rooms WHERE slug = ? AND id != ?");
                $stmt->execute([$slug, $roomId]);
            } else {
                $stmt = $db->prepare("SELECT id FROM rooms WHERE slug = ?");
                $stmt->execute([$slug]);
            }
            
            if ($stmt->fetch()) {
                $slug = $slug . '-' . time();
            }
            
            if ($roomId > 0) {
                // Update - check if columns exist first (backward compatibility)
                $checkColumns = $db->query("SHOW COLUMNS FROM rooms LIKE 'room_type'");
                if ($checkColumns->rowCount() > 0) {
                    $stmt = $db->prepare("UPDATE rooms SET title = ?, slug = ?, description = ?, price_per_night = ?, 
                                          room_type = ?, pricing_type = ?, max_occupancy = ?, area_sqft = ?, 
                                          amenities = ?, is_available = ?, is_visible = ? 
                                          WHERE id = ?");
                    $stmt->execute([$title, $slug, $description, $price, $room_type, $pricing_type, $max_occupancy, $area_sqft, $amenities, $is_available, $is_visible, $roomId]);
                } else {
                    // Fallback for old schema
                    $stmt = $db->prepare("UPDATE rooms SET title = ?, slug = ?, description = ?, price_per_night = ?, 
                                          max_occupancy = ?, amenities = ?, is_available = ?, is_visible = ? 
                                          WHERE id = ?");
                    $stmt->execute([$title, $slug, $description, $price, $max_occupancy, $amenities, $is_available, $is_visible, $roomId]);
                }
                $success = true;
            } else {
                // Insert - check if columns exist first
                $checkColumns = $db->query("SHOW COLUMNS FROM rooms LIKE 'room_type'");
                if ($checkColumns->rowCount() > 0) {
                    $stmt = $db->prepare("INSERT INTO rooms (title, slug, description, price_per_night, room_type, pricing_type, max_occupancy, area_sqft, amenities, is_available, is_visible) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $slug, $description, $price, $room_type, $pricing_type, $max_occupancy, $area_sqft, $amenities, $is_available, $is_visible]);
                } else {
                    // Fallback for old schema
                    $stmt = $db->prepare("INSERT INTO rooms (title, slug, description, price_per_night, max_occupancy, amenities, is_available, is_visible) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $slug, $description, $price, $max_occupancy, $amenities, $is_available, $is_visible]);
                }
                $roomId = $db->lastInsertId();
                $success = true;
            }
            
            if ($success) {
                header('Location: rooms.php?saved=1');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Failed to save room: ' . $e->getMessage();
        }
    }
}

$pageTitle = ($roomId > 0 ? 'Edit' : 'Add') . ' Room';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <div class="page-header-admin">
        <div>
            <h1><?php echo $roomId > 0 ? 'Edit Room' : 'Add New Room'; ?></h1>
            <p><?php echo $roomId > 0 ? 'Update room information' : 'Create a new room listing'; ?></p>
        </div>
        <a href="rooms.php" class="btn btn-outline">← Back to Rooms</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="form-wrapper">
        <form method="POST" class="admin-form" id="roomForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="title">Room Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" required 
                           value="<?php echo htmlspecialchars($room['title'] ?? $_POST['title'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="room_type">Room Type <span class="required">*</span></label>
                    <select id="room_type" name="room_type" required onchange="updatePricingFields()">
                        <option value="guest_house" <?php echo ($room['room_type'] ?? 'guest_house') === 'guest_house' ? 'selected' : ''; ?>>Guest House</option>
                        <option value="pg" <?php echo ($room['room_type'] ?? '') === 'pg' ? 'selected' : ''; ?>>PG (Paying Guest)</option>
                    </select>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="price" id="price_label">Price per Night (₹) <span class="required">*</span></label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required 
                           value="<?php echo $room['price_per_night'] ?? $_POST['price'] ?? ''; ?>">
                    <input type="hidden" id="pricing_type" name="pricing_type" value="<?php echo $room['pricing_type'] ?? 'per_night'; ?>">
                </div>
                
                <div class="form-group">
                    <label for="max_occupancy">Max Occupancy <span class="required">*</span></label>
                    <input type="number" id="max_occupancy" name="max_occupancy" min="1" required
                           value="<?php echo $room['max_occupancy'] ?? $_POST['max_occupancy'] ?? 2; ?>">
                    <small class="form-help">For PG: Number of sharing (2, 3, 4, 6, etc.)</small>
                </div>
            </div>
            
            <div class="form-group" id="area_sqft_group" style="display: none;">
                <label for="area_sqft">Area (Square Feet)</label>
                <input type="number" id="area_sqft" name="area_sqft" min="0" 
                       placeholder="e.g., 275, 300, 350, 450"
                       value="<?php echo $room['area_sqft'] ?? $_POST['area_sqft'] ?? ''; ?>">
                <small class="form-help">Optional: Room area in square feet (for PG rooms)</small>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($room['description'] ?? $_POST['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="amenities">Amenities (comma-separated)</label>
                <input type="text" id="amenities" name="amenities" 
                       placeholder="e.g., WiFi, AC, TV, Attached Bathroom, Hot Water, 24/7 Security"
                       value="<?php echo htmlspecialchars($room['amenities'] ?? $_POST['amenities'] ?? ''); ?>">
            </div>
            
            <script>
            function updatePricingFields() {
                const roomType = document.getElementById('room_type').value;
                const priceLabel = document.getElementById('price_label');
                const pricingType = document.getElementById('pricing_type');
                const areaGroup = document.getElementById('area_sqft_group');
                
                if (roomType === 'pg') {
                    priceLabel.innerHTML = 'Price per Month (₹) <span class="required">*</span>';
                    pricingType.value = 'per_month';
                    areaGroup.style.display = 'block';
                } else {
                    priceLabel.innerHTML = 'Price per Night (₹) <span class="required">*</span>';
                    pricingType.value = 'per_night';
                    areaGroup.style.display = 'none';
                }
            }
            
            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                updatePricingFields();
            });
            </script>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_available" value="1" 
                           <?php echo ($room['is_available'] ?? 1) ? 'checked' : ''; ?>>
                    <span>Room is Available</span>
                </label>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_visible" value="1" 
                           <?php echo ($room['is_visible'] ?? 1) ? 'checked' : ''; ?>>
                    <span>Visible on Website</span>
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Room</button>
                <a href="rooms.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
        
        <?php if ($roomId > 0): ?>
            <div class="form-section">
                <h3>Room Images</h3>
                <p>Manage images for this room</p>
                <a href="room-images.php?id=<?php echo $roomId; ?>" class="btn btn-outline">Manage Images</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

