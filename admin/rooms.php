<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$db = getDB();
$action = $_GET['action'] ?? 'list';
$roomId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_room'])) {
        $id = intval($_POST['room_id']);
        $stmt = $db->prepare("DELETE FROM rooms WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: rooms.php?deleted=1');
        exit;
    }
}

$pageTitle = 'Manage Rooms';
include __DIR__ . '/includes/header.php';

// Get all rooms
$stmt = $db->query("SELECT r.*, 
                    (SELECT COUNT(*) FROM room_images WHERE room_id = r.id) as image_count
                    FROM rooms r 
                    ORDER BY r.room_type, r.price_per_night ASC");
$rooms = $stmt->fetchAll();
?>

<div class="admin-content">
    <div class="page-header-admin">
        <div>
            <h1>Manage Rooms</h1>
            <p>Add, edit, or remove room listings</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="clear-all-rooms.php" class="btn btn-outline" style="background: #fee2e2; color: #991b1b; border-color: #fca5a5;">🗑️ Clear All</a>
            <a href="room-edit.php?action=add" class="btn btn-primary">➕ Add New Room</a>
        </div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Room deleted successfully.</div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Max</th>
                    <th>Images</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rooms)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No rooms added yet. <a href="room-edit.php?action=add">Add your first room</a></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rooms as $room): 
                        $roomType = $room['room_type'] ?? 'guest_house';
                        $pricingType = $room['pricing_type'] ?? 'per_night';
                        if ($roomType === 'pg' && $pricingType === 'per_night') {
                            $pricingType = 'per_month';
                        }
                    ?>
                        <tr>
                            <td><?php echo $room['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($room['title']); ?></strong>
                                <br><small class="text-muted"><?php echo htmlspecialchars($room['slug']); ?></small>
                            </td>
                            <td>
                                <span class="badge" style="background: <?php echo $roomType === 'pg' ? '#0d9488' : '#f97316'; ?>;">
                                    <?php echo strtoupper($roomType === 'pg' ? 'PG' : 'Guest House'); ?>
                                </span>
                            </td>
                            <td><?php 
                                $price = $room['price_per_night'] ?? 0;
                                $pricingType = $room['pricing_type'] ?? 'per_night';
                                if ($roomType === 'pg' && $pricingType === 'per_night') {
                                    $pricingType = 'per_month';
                                }
                                echo formatPrice($price, $pricingType); 
                            ?></td>
                            <td><?php echo $room['max_occupancy']; ?> <?php echo ($room['area_sqft'] ?? '') ? '(' . $room['area_sqft'] . ' sq ft)' : ''; ?></td>
                            <td>
                                <span class="badge"><?php echo $room['image_count']; ?> images</span>
                                <a href="room-images.php?id=<?php echo $room['id']; ?>" class="btn btn-sm btn-outline">Manage</a>
                            </td>
                            <td>
                                <?php if ($room['is_visible']): ?>
                                    <span class="status-badge status-available">Visible</span>
                                <?php else: ?>
                                    <span class="status-badge status-unavailable">Hidden</span>
                                <?php endif; ?>
                                <?php if (!$room['is_available']): ?>
                                    <span class="status-badge status-unavailable">Unavailable</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="room-edit.php?id=<?php echo $room['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                        <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                                        <input type="hidden" name="delete_room" value="1">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

