<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$db = getDB();
$success = false;
$error = '';
$roomsUpdated = 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_pricing'])) {
    $newPrice = floatval($_POST['price'] ?? 1200);
    
    if ($newPrice > 0) {
        try {
            $stmt = $db->prepare("UPDATE rooms SET price_per_night = ?, updated_at = NOW() WHERE is_visible = 1");
            $stmt->execute([$newPrice]);
            $roomsUpdated = $stmt->rowCount();
            $success = true;
        } catch (Exception $e) {
            $error = 'Failed to update pricing: ' . $e->getMessage();
        }
    } else {
        $error = 'Please enter a valid price.';
    }
}

// Get current rooms
$stmt = $db->query("SELECT id, title, slug, price_per_night, is_visible FROM rooms ORDER BY price_per_night ASC");
$allRooms = $stmt->fetchAll();

$pageTitle = 'Quick Update Pricing';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <div class="page-header-admin">
        <div>
            <h1>Quick Update Room Pricing</h1>
            <p>Update all visible room prices at once</p>
        </div>
        <a href="rooms.php" class="btn btn-outline">← Back to Rooms</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            ✅ Successfully updated <?php echo $roomsUpdated; ?> room(s) to ₹<?php echo number_format($_POST['price'] ?? 1200, 2); ?> per night.
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="form-wrapper">
        <form method="POST" class="admin-form">
            <div class="form-group">
                <label for="price">New Price per Night (₹) <span class="required">*</span></label>
                <input type="number" id="price" name="price" step="0.01" min="0" value="1200" required>
                <small class="form-help">This will update ALL visible rooms to this price.</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="update_pricing" class="btn btn-primary">Update All Rooms</button>
                <a href="rooms.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>

    <div class="table-wrapper" style="margin-top: 2rem;">
        <h2>Current Room Pricing</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Room Title</th>
                    <th>Current Price/Night</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($allRooms)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No rooms found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($allRooms as $room): ?>
                        <tr>
                            <td><?php echo $room['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($room['title']); ?></strong></td>
                            <td>₹<?php echo number_format($room['price_per_night'], 2); ?></td>
                            <td>
                                <?php if ($room['is_visible']): ?>
                                    <span class="status-badge status-available">Visible</span>
                                <?php else: ?>
                                    <span class="status-badge status-unavailable">Hidden</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
