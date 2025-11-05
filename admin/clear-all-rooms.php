<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$db = getDB();
$success = false;
$error = '';
$roomsDeleted = 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $confirmText = trim($_POST['confirm_text'] ?? '');
    
    if ($confirmText === 'DELETE ALL') {
        try {
            // Get count before deletion
            $stmt = $db->query("SELECT COUNT(*) as count FROM rooms");
            $result = $stmt->fetch();
            $roomsDeleted = $result['count'];
            
            // Delete all rooms (images will be deleted automatically due to CASCADE)
            $db->exec("DELETE FROM rooms");
            
            // Reset AUTO_INCREMENT
            $db->exec("ALTER TABLE rooms AUTO_INCREMENT = 1");
            
            $success = true;
        } catch (Exception $e) {
            $error = 'Failed to delete rooms: ' . $e->getMessage();
        }
    } else {
        $error = 'Confirmation text did not match. Please type "DELETE ALL" exactly.';
    }
}

// Get current rooms count
$stmt = $db->query("SELECT COUNT(*) as count FROM rooms");
$roomsCount = $stmt->fetch()['count'];

// Get current rooms list
$stmt = $db->query("SELECT id, title, slug, price_per_night FROM rooms ORDER BY id");
$currentRooms = $stmt->fetchAll();

$pageTitle = 'Clear All Rooms';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <div class="page-header-admin">
        <div>
            <h1>Clear All Rooms</h1>
            <p>⚠️ Remove all existing rooms to prepare for new room data</p>
        </div>
        <a href="rooms.php" class="btn btn-outline">← Back to Rooms</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            ✅ Successfully deleted <?php echo $roomsDeleted; ?> room(s) and all associated images.<br>
            Database is now ready for new room entries.
        </div>
        <div class="form-actions" style="margin-top: 1rem;">
            <a href="room-edit.php?action=add" class="btn btn-primary">➕ Add First New Room</a>
            <a href="rooms.php" class="btn btn-outline">View Rooms List</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="alert alert-warning" style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem; margin: 1.5rem 0;">
            <strong>⚠️ Warning:</strong> This action cannot be undone!<br>
            <ul style="margin: 0.5rem 0 0 1.5rem;">
                <li>All rooms will be permanently deleted</li>
                <li>All room images will be automatically deleted (CASCADE)</li>
                <li>Enquiries will remain but room references will be cleared</li>
                <li>Use this before importing new rooms from client's PDF</li>
            </ul>
        </div>

        <?php if ($roomsCount > 0): ?>
        <div class="table-wrapper" style="margin-bottom: 2rem;">
            <h2>Current Rooms (<?php echo $roomsCount; ?> room(s))</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room Title</th>
                        <th>Slug</th>
                        <th>Price/Night</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currentRooms as $room): ?>
                        <tr>
                            <td><?php echo $room['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($room['title']); ?></strong></td>
                            <td><small><?php echo htmlspecialchars($room['slug']); ?></small></td>
                            <td>₹<?php echo number_format($room['price_per_night'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="alert alert-info">
                No rooms found. Database is already empty and ready for new entries.
            </div>
        <?php endif; ?>

        <?php if ($roomsCount > 0): ?>
        <div class="form-wrapper">
            <form method="POST" class="admin-form" onsubmit="return confirm('Are you absolutely sure you want to delete ALL rooms? This cannot be undone!');">
                <div class="form-group">
                    <label for="confirm_text">
                        Type <strong>"DELETE ALL"</strong> to confirm <span class="required">*</span>
                    </label>
                    <input type="text" id="confirm_text" name="confirm_text" required 
                           placeholder="Type DELETE ALL here" autocomplete="off">
                    <small class="form-help">This is a safety measure to prevent accidental deletion.</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="confirm_delete" class="btn btn-danger">🗑️ Delete All Rooms</button>
                    <a href="rooms.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <div style="margin-top: 2rem; padding: 1rem; background: #f3f4f6; border-radius: 8px;">
        <h3>Next Steps After Clearing:</h3>
        <ol style="margin-left: 1.5rem;">
            <li>Review the client's PDF for room details</li>
            <li>Add new rooms via Admin Panel (Rooms → Add New Room)</li>
            <li>Or provide me the room details and I'll create a SQL insert file</li>
            <li>Upload images for each room</li>
        </ol>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
