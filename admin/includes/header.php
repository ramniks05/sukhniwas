<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Admin Panel</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p><?php echo getSiteSetting('site_name', 'Sukhniwas'); ?></p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <span class="nav-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="rooms.php" class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'rooms') !== false ? 'active' : ''; ?>">
                    <span class="nav-icon">🏠</span>
                    <span>Rooms</span>
                </a>
                <a href="enquiries.php" class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'enquir') !== false ? 'active' : ''; ?>">
                    <span class="nav-icon">📧</span>
                    <span>Enquiries</span>
                    <?php
                    $db = getDB();
                    $newCount = $db->query("SELECT COUNT(*) FROM enquiries WHERE status = 'new'")->fetchColumn();
                    if ($newCount > 0):
                    ?>
                        <span class="badge"><?php echo $newCount; ?></span>
                    <?php endif; ?>
                </a>
                <a href="settings.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                    <span class="nav-icon">⚙️</span>
                    <span>Settings</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="<?php echo SITE_URL; ?>/public/index.php" target="_blank" class="nav-item">
                    <span class="nav-icon">🌐</span>
                    <span>View Website</span>
                </a>
                <a href="logout.php" class="nav-item">
                    <span class="nav-icon">🚪</span>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="admin-main">
            <!-- Top Bar -->
            <header class="admin-topbar">
                <div class="topbar-content">
                    <h1><?php echo $pageTitle ?? 'Admin Panel'; ?></h1>
                    <div class="topbar-actions">
                        <span class="user-info"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin_username']); ?></span>
                    </div>
                </div>
            </header>

