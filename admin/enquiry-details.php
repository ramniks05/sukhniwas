<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$db = getDB();
$enquiryId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($enquiryId <= 0) {
    header('Location: enquiries.php');
    exit;
}

// Get enquiry
$stmt = $db->prepare("SELECT e.*, r.title as room_title, r.slug as room_slug 
                      FROM enquiries e 
                      LEFT JOIN rooms r ON e.room_id = r.id 
                      WHERE e.id = ?");
$stmt->execute([$enquiryId]);
$enquiry = $stmt->fetch();

if (!$enquiry) {
    header('Location: enquiries.php');
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = sanitize($_POST['status']);
    $stmt = $db->prepare("UPDATE enquiries SET status = ? WHERE id = ?");
    $stmt->execute([$status, $enquiryId]);
    $enquiry['status'] = $status;
    header('Location: enquiry-details.php?id=' . $enquiryId . '&updated=1');
    exit;
}

$pageTitle = 'Enquiry Details #' . $enquiryId;
include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <div class="page-header-admin">
        <div>
            <h1>Enquiry Details</h1>
            <p>Enquiry #<?php echo $enquiryId; ?></p>
        </div>
        <a href="enquiries.php" class="btn btn-outline">← Back to Enquiries</a>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Status updated successfully.</div>
    <?php endif; ?>

    <div class="detail-grid">
        <div class="detail-card">
            <h3>Contact Information</h3>
            <div class="detail-item">
                <label>Name:</label>
                <strong><?php echo htmlspecialchars($enquiry['name']); ?></strong>
            </div>
            <div class="detail-item">
                <label>Mobile:</label>
                <a href="tel:<?php echo htmlspecialchars($enquiry['mobile']); ?>">
                    <?php echo htmlspecialchars($enquiry['mobile']); ?>
                </a>
                <a href="<?php echo getWhatsAppLink(getSiteSetting('whatsapp_number'), 'Hi ' . $enquiry['name']); ?>" 
                   target="_blank" class="btn btn-sm btn-whatsapp">💬 WhatsApp</a>
            </div>
            <div class="detail-item">
                <label>Email:</label>
                <?php if ($enquiry['email']): ?>
                    <a href="mailto:<?php echo htmlspecialchars($enquiry['email']); ?>">
                        <?php echo htmlspecialchars($enquiry['email']); ?>
                    </a>
                <?php else: ?>
                    <span class="text-muted">N/A</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-card">
            <h3>Booking Details</h3>
            <div class="detail-item">
                <label>Check-in Date:</label>
                <?php echo $enquiry['check_in_date'] ? formatDate($enquiry['check_in_date']) : '<span class="text-muted">Not specified</span>'; ?>
            </div>
            <div class="detail-item">
                <label>Check-out Date:</label>
                <?php echo $enquiry['check_out_date'] ? formatDate($enquiry['check_out_date']) : '<span class="text-muted">Not specified</span>'; ?>
            </div>
            <div class="detail-item">
                <label>Room:</label>
                <?php if ($enquiry['room_title']): ?>
                    <a href="<?php echo SITE_URL; ?>/public/room-details.php?slug=<?php echo $enquiry['room_slug']; ?>" target="_blank">
                        <?php echo htmlspecialchars($enquiry['room_title']); ?>
                    </a>
                <?php else: ?>
                    <span class="text-muted">Not specified</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-card full-width">
            <h3>Message</h3>
            <div class="message-box">
                <?php echo $enquiry['message'] ? nl2br(htmlspecialchars($enquiry['message'])) : '<span class="text-muted">No message provided.</span>'; ?>
            </div>
        </div>

        <div class="detail-card">
            <h3>Status</h3>
            <form method="POST" class="status-form">
                <div class="form-group">
                    <select name="status" class="status-select">
                        <option value="new" <?php echo $enquiry['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                        <option value="contacted" <?php echo $enquiry['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                        <option value="confirmed" <?php echo $enquiry['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="rejected" <?php echo $enquiry['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <input type="hidden" name="update_status" value="1">
                <button type="submit" class="btn btn-primary">Update Status</button>
            </form>
        </div>

        <div class="detail-card">
            <h3>Additional Information</h3>
            <div class="detail-item">
                <label>Submitted:</label>
                <?php echo formatDate($enquiry['created_at'], 'd M Y, h:i A'); ?>
            </div>
            <?php if ($enquiry['ip_address']): ?>
                <div class="detail-item">
                    <label>IP Address:</label>
                    <code><?php echo htmlspecialchars($enquiry['ip_address']); ?></code>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

