<?php
/**
 * Helper Functions
 */

// Get sample room image (using Unsplash)
function getSampleRoomImage($roomId = null, $width = 600, $height = 400) {
    // Use Unsplash Source API for free sample images
    $images = [
        1 => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=' . $width . '&h=' . $height . '&fit=crop', // Deluxe room
        2 => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=' . $width . '&h=' . $height . '&fit=crop', // Standard room
        3 => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=' . $width . '&h=' . $height . '&fit=crop', // Premium room
    ];
    
    // Default hotel room image
    $default = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=' . $width . '&h=' . $height . '&fit=crop';
    
    return $roomId && isset($images[$roomId]) ? $images[$roomId] : $default;
}

// Get site settings
function getSiteSetting($key, $default = '') {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

// Update site setting
function updateSiteSetting($key, $value) {
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) 
                              ON DUPLICATE KEY UPDATE setting_value = ?");
        return $stmt->execute([$key, $value, $value]);
    } catch (Exception $e) {
        return false;
    }
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Generate slug
function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

// Format price
function formatPrice($price, $pricingType = 'per_night') {
    $formatted = '₹' . number_format($price, 2);
    
    if ($pricingType === 'per_month') {
        return $formatted . '/month';
    } else {
        return $formatted . '/night';
    }
}

// Format price with room data (handles room array)
function formatRoomPrice($room) {
    $price = $room['price_per_night'] ?? $room['price'] ?? 0;
    $pricingType = $room['pricing_type'] ?? 'per_night';
    
    // If room_type is pg but pricing_type not set, default to per_month
    if (($room['room_type'] ?? '') === 'pg' && $pricingType === 'per_night') {
        $pricingType = 'per_month';
    }
    
    return formatPrice($price, $pricingType);
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

// Require admin login
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: ' . ADMIN_LOGIN_URL);
        exit;
    }
}

// Upload image
function uploadImage($file, $folder = 'rooms') {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }

    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error: ' . $file['error']];
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'File size exceeds maximum allowed size'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }

    // Create directories
    $uploadDir = UPLOAD_PATH . $folder . '/';
    $thumbDir = THUMBNAIL_PATH;
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    if (!is_dir($thumbDir)) {
        mkdir($thumbDir, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    $thumbpath = $thumbDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }

    // Create thumbnail
    createThumbnail($filepath, $thumbpath, 300, 300);

    return [
        'success' => true,
        'filename' => $filename,
        'path' => $folder . '/' . $filename,
        'full_path' => $filepath
    ];
}

// Create thumbnail
function createThumbnail($source, $destination, $maxWidth = 300, $maxHeight = 300) {
    $imageInfo = getimagesize($source);
    if (!$imageInfo) return false;

    $width = $imageInfo[0];
    $height = $imageInfo[1];
    $mimeType = $imageInfo['mime'];

    // Calculate new dimensions
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = (int)($width * $ratio);
    $newHeight = (int)($height * $ratio);

    // Create image resource
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($source);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }

    // Create thumbnail
    $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG
    if ($mimeType === 'image/png') {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Save thumbnail
    switch ($mimeType) {
        case 'image/jpeg':
            imagejpeg($thumbnail, $destination, 85);
            break;
        case 'image/png':
            imagepng($thumbnail, $destination, 8);
            break;
        case 'image/webp':
            imagewebp($thumbnail, $destination, 85);
            break;
    }

    imagedestroy($sourceImage);
    imagedestroy($thumbnail);

    return true;
}

// Delete image
function deleteImage($path) {
    $fullPath = UPLOAD_PATH . $path;
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }
    
    // Delete thumbnail
    $filename = basename($path);
    $thumbPath = THUMBNAIL_PATH . $filename;
    if (file_exists($thumbPath)) {
        unlink($thumbPath);
    }
}

// Get WhatsApp link
function getWhatsAppLink($phone, $message = '') {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    $message = urlencode($message ?: getSiteSetting('whatsapp_message', 'Hi, I would like to know more about your rooms.'));
    return "https://wa.me/{$phone}?text={$message}";
}

// Send email notification
function sendEnquiryEmail($enquiry) {
    $to = getSiteSetting('site_email', 'admin@sukhniwas.com');
    $subject = "New Enquiry from " . $enquiry['name'];
    
    $message = "New enquiry received:\n\n";
    $message .= "Name: " . $enquiry['name'] . "\n";
    $message .= "Mobile: " . $enquiry['mobile'] . "\n";
    $message .= "Email: " . ($enquiry['email'] ?? 'N/A') . "\n";
    if (!empty($enquiry['check_in_date'])) {
        $message .= "Check-in: " . $enquiry['check_in_date'] . "\n";
    }
    if (!empty($enquiry['check_out_date'])) {
        $message .= "Check-out: " . $enquiry['check_out_date'] . "\n";
    }
    if (!empty($enquiry['room_id'])) {
        $db = getDB();
        $stmt = $db->prepare("SELECT title FROM rooms WHERE id = ?");
        $stmt->execute([$enquiry['room_id']]);
        $room = $stmt->fetch();
        $message .= "Room: " . ($room['title'] ?? 'N/A') . "\n";
    }
    $message .= "Message: " . ($enquiry['message'] ?? 'N/A') . "\n";
    
    $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headers .= "Reply-To: " . ($enquiry['email'] ?? $to) . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Format date
function formatDate($date, $format = 'd M Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

