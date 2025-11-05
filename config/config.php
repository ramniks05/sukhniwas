<?php
/**
 * Main Configuration File
 */

// Site Configuration
define('SITE_NAME', 'Sukhniwas Guest House');
define('SITE_URL', 'http://localhost/sukhniwas');
define('BASE_PATH', __DIR__ . '/..');

// Paths
define('UPLOAD_PATH', BASE_PATH . '/public/uploads/');
define('ROOM_IMAGES_PATH', UPLOAD_PATH . 'rooms/');
define('THUMBNAIL_PATH', UPLOAD_PATH . 'thumbnails/');

// URLs
define('UPLOAD_URL', SITE_URL . '/public/uploads/');
define('ROOM_IMAGES_URL', UPLOAD_URL . 'rooms/');
define('THUMBNAIL_URL', UPLOAD_URL . 'thumbnails/');

// Security
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('MAX_VIDEO_SIZE', 52428800); // 50MB for videos
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-msvideo']); // MP4, WebM, OGG, MOV, AVI

// Admin
define('ADMIN_LOGIN_URL', SITE_URL . '/admin/login.php');
define('ADMIN_DASHBOARD_URL', SITE_URL . '/admin/index.php');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 in production

// Include database
require_once __DIR__ . '/database.php';

// Helper Functions
require_once __DIR__ . '/../includes/functions.php';

