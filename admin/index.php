<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$db = getDB();

// Get statistics
$stats = [
    'total_rooms' => $db->query("SELECT COUNT(*) FROM rooms")->fetchColumn(),
    'visible_rooms' => $db->query("SELECT COUNT(*) FROM rooms WHERE is_visible = 1")->fetchColumn(),
    'total_enquiries' => $db->query("SELECT COUNT(*) FROM enquiries")->fetchColumn(),
    'new_enquiries' => $db->query("SELECT COUNT(*) FROM enquiries WHERE status = 'new'")->fetchColumn(),
];

// Get recent enquiries
$stmt = $db->query("SELECT e.*, r.title as room_title 
                    FROM enquiries e 
                    LEFT JOIN rooms r ON e.room_id = r.id 
                    ORDER BY e.created_at DESC 
                    LIMIT 5");
$recentEnquiries = $stmt->fetchAll();

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <div class="page-header-admin">
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin_username']); ?>!</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #3b82f6;">🏠</div>
            <div class="stat-content">
                <h3><?php echo $stats['total_rooms']; ?></h3>
                <p>Total Rooms</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #10b981;">✓</div>
            <div class="stat-content">
                <h3><?php echo $stats['visible_rooms']; ?></h3>
                <p>Visible Rooms</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #f59e0b;">📧</div>
            <div class="stat-content">
                <h3><?php echo $stats['total_enquiries']; ?></h3>
                <p>Total Enquiries</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #ef4444;">🔔</div>
            <div class="stat-content">
                <h3><?php echo $stats['new_enquiries']; ?></h3>
                <p>New Enquiries</p>
            </div>
        </div>
    </div>

    <!-- Recent Enquiries -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Recent Enquiries</h2>
            <a href="enquiries.php" class="btn btn-outline">View All</a>
        </div>
        
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Room</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentEnquiries)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No enquiries yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentEnquiries as $enquiry): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($enquiry['name']); ?></td>
                                <td><a href="tel:<?php echo htmlspecialchars($enquiry['mobile']); ?>"><?php echo htmlspecialchars($enquiry['mobile']); ?></a></td>
                                <td><?php echo htmlspecialchars($enquiry['email'] ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($enquiry['room_title'] ?: 'N/A'); ?></td>
                                <td><?php echo formatDate($enquiry['created_at'], 'd M Y'); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $enquiry['status']; ?>">
                                        <?php echo ucfirst($enquiry['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="enquiry-details.php?id=<?php echo $enquiry['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-section">
        <h2>Quick Actions</h2>
        <div class="quick-actions">
            <a href="rooms.php?action=add" class="action-card">
                <span class="action-icon">➕</span>
                <h3>Add New Room</h3>
                <p>Create a new room listing</p>
            </a>
            <a href="rooms.php" class="action-card">
                <span class="action-icon">🏠</span>
                <h3>Manage Rooms</h3>
                <p>Edit existing rooms</p>
            </a>
            <a href="enquiries.php" class="action-card">
                <span class="action-icon">📧</span>
                <h3>View Enquiries</h3>
                <p>Manage customer enquiries</p>
            </a>
            <a href="settings.php" class="action-card">
                <span class="action-icon">⚙️</span>
                <h3>Settings</h3>
                <p>Update site settings</p>
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

