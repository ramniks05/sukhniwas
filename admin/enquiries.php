<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$db = getDB();

// Handle status update
if (isset($_POST['update_status'])) {
    $enquiryId = intval($_POST['enquiry_id']);
    $status = sanitize($_POST['status']);
    $stmt = $db->prepare("UPDATE enquiries SET status = ? WHERE id = ?");
    $stmt->execute([$status, $enquiryId]);
    header('Location: enquiries.php?updated=1');
    exit;
}

// Handle delete
if (isset($_POST['delete_enquiry'])) {
    $enquiryId = intval($_POST['enquiry_id']);
    $stmt = $db->prepare("DELETE FROM enquiries WHERE id = ?");
    $stmt->execute([$enquiryId]);
    header('Location: enquiries.php?deleted=1');
    exit;
}

// Handle export
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="enquiries_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Mobile', 'Email', 'Check-in', 'Check-out', 'Room', 'Message', 'Status', 'Date']);
    
    $stmt = $db->query("SELECT e.*, r.title as room_title 
                        FROM enquiries e 
                        LEFT JOIN rooms r ON e.room_id = r.id 
                        ORDER BY e.created_at DESC");
    
    while ($row = $stmt->fetch()) {
        fputcsv($output, [
            $row['id'],
            $row['name'],
            $row['mobile'],
            $row['email'] ?: 'N/A',
            $row['check_in_date'] ?: 'N/A',
            $row['check_out_date'] ?: 'N/A',
            $row['room_title'] ?: 'N/A',
            substr($row['message'] ?: 'N/A', 0, 50),
            $row['status'],
            $row['created_at']
        ]);
    }
    
    fclose($output);
    exit;
}

// Filter
$statusFilter = $_GET['status'] ?? 'all';
$where = '';
if ($statusFilter !== 'all') {
    $where = "WHERE e.status = " . $db->quote($statusFilter);
}

$stmt = $db->query("SELECT e.*, r.title as room_title 
                    FROM enquiries e 
                    LEFT JOIN rooms r ON e.room_id = r.id 
                    $where
                    ORDER BY e.created_at DESC");
$enquiries = $stmt->fetchAll();

$pageTitle = 'Enquiries';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <div class="page-header-admin">
        <div>
            <h1>Enquiries</h1>
            <p>Manage customer enquiries and leads</p>
        </div>
        <div class="header-actions">
            <a href="?export=1" class="btn btn-outline">📥 Export CSV</a>
        </div>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Enquiry status updated.</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Enquiry deleted.</div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="filters">
        <a href="?status=all" class="filter-btn <?php echo $statusFilter === 'all' ? 'active' : ''; ?>">All</a>
        <a href="?status=new" class="filter-btn <?php echo $statusFilter === 'new' ? 'active' : ''; ?>">New</a>
        <a href="?status=contacted" class="filter-btn <?php echo $statusFilter === 'contacted' ? 'active' : ''; ?>">Contacted</a>
        <a href="?status=confirmed" class="filter-btn <?php echo $statusFilter === 'confirmed' ? 'active' : ''; ?>">Confirmed</a>
        <a href="?status=rejected" class="filter-btn <?php echo $statusFilter === 'rejected' ? 'active' : ''; ?>">Rejected</a>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Dates</th>
                    <th>Room</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($enquiries)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No enquiries found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($enquiries as $enquiry): ?>
                        <tr class="<?php echo $enquiry['status'] === 'new' ? 'row-new' : ''; ?>">
                            <td><?php echo $enquiry['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($enquiry['name']); ?></strong></td>
                            <td>
                                <div><?php echo htmlspecialchars($enquiry['mobile']); ?></div>
                                <?php if ($enquiry['email']): ?>
                                    <small><?php echo htmlspecialchars($enquiry['email']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($enquiry['check_in_date']): ?>
                                    <div>In: <?php echo formatDate($enquiry['check_in_date']); ?></div>
                                <?php endif; ?>
                                <?php if ($enquiry['check_out_date']): ?>
                                    <div>Out: <?php echo formatDate($enquiry['check_out_date']); ?></div>
                                <?php endif; ?>
                                <?php if (!$enquiry['check_in_date'] && !$enquiry['check_out_date']): ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($enquiry['room_title'] ?: 'N/A'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $enquiry['status']; ?>">
                                    <?php echo ucfirst($enquiry['status']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($enquiry['created_at'], 'd M Y H:i'); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="enquiry-details.php?id=<?php echo $enquiry['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this enquiry?');">
                                        <input type="hidden" name="enquiry_id" value="<?php echo $enquiry['id']; ?>">
                                        <input type="hidden" name="delete_enquiry" value="1">
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

