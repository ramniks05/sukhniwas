<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$db = getDB();
$roomId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($roomId <= 0) {
    header('Location: rooms.php');
    exit;
}

// Get room info
$stmt = $db->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$roomId]);
$room = $stmt->fetch();

if (!$room) {
    header('Location: rooms.php');
    exit;
}

$success = false;
$error = '';

// Handle image/video upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Image upload
        $result = uploadImage($_FILES['image'], 'rooms');
        
        if ($result['success']) {
            try {
                // Check if media_type column exists (backward compatibility)
                $checkColumn = $db->query("SHOW COLUMNS FROM room_images LIKE 'media_type'");
                if ($checkColumn->rowCount() > 0) {
                    $stmt = $db->prepare("INSERT INTO room_images (room_id, image_path, media_type, image_order) VALUES (?, ?, 'image', ?)");
                } else {
                    $stmt = $db->prepare("INSERT INTO room_images (room_id, image_path, image_order) VALUES (?, ?, ?)");
                }
                
                $maxOrder = $db->prepare("SELECT COALESCE(MAX(image_order), 0) FROM room_images WHERE room_id = ?");
                $maxOrder->execute([$roomId]);
                $nextOrder = $maxOrder->fetchColumn() + 1;
                
                if ($checkColumn->rowCount() > 0) {
                    $stmt->execute([$roomId, $result['path'], $nextOrder]);
                } else {
                    $stmt->execute([$roomId, $result['path'], $nextOrder]);
                }
                $success = true;
            } catch (Exception $e) {
                $error = 'Failed to save image: ' . $e->getMessage();
                deleteImage($result['path']);
            }
        } else {
            $error = $result['error'];
        }
    } elseif (!empty($_POST['video_url'])) {
        // Video URL
        $videoUrl = sanitize($_POST['video_url']);
        $mediaType = 'video';
        
        // Validate video URL (YouTube, Vimeo, or direct video URL)
        if (filter_var($videoUrl, FILTER_VALIDATE_URL)) {
            try {
                // Check if media_type column exists
                $checkColumn = $db->query("SHOW COLUMNS FROM room_images LIKE 'media_type'");
                $checkVideoColumn = $db->query("SHOW COLUMNS FROM room_images LIKE 'video_url'");
                
                $maxOrder = $db->prepare("SELECT COALESCE(MAX(image_order), 0) FROM room_images WHERE room_id = ?");
                $maxOrder->execute([$roomId]);
                $nextOrder = $maxOrder->fetchColumn() + 1;
                
                if ($checkColumn->rowCount() > 0 && $checkVideoColumn->rowCount() > 0) {
                    // Generate a thumbnail path or use placeholder
                    $thumbnailPath = 'video-thumb-' . time() . '.jpg';
                    $stmt = $db->prepare("INSERT INTO room_images (room_id, image_path, video_url, media_type, image_order) VALUES (?, ?, ?, 'video', ?)");
                    $stmt->execute([$roomId, $thumbnailPath, $videoUrl, $nextOrder]);
                } else {
                    // Fallback: store URL in image_path if columns don't exist
                    $stmt = $db->prepare("INSERT INTO room_images (room_id, image_path, image_order) VALUES (?, ?, ?)");
                    $stmt->execute([$roomId, $videoUrl, $nextOrder]);
                }
                $success = true;
            } catch (Exception $e) {
                $error = 'Failed to save video: ' . $e->getMessage();
            }
        } else {
            $error = 'Please enter a valid video URL.';
        }
    } elseif (isset($_FILES['video_file']) && $_FILES['video_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Video file upload
        $videoFile = $_FILES['video_file'];
        
        if ($videoFile['error'] !== UPLOAD_ERR_OK) {
            $error = 'Upload error: ' . $videoFile['error'];
        } elseif ($videoFile['size'] > 52428800) { // 50MB max for videos
            $error = 'Video file size exceeds 50MB limit.';
        } else {
            $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $videoFile['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedVideoTypes)) {
                $error = 'Invalid video format. Allowed: MP4, WebM, OGG, MOV';
            } else {
                $uploadDir = UPLOAD_PATH . 'rooms/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $extension = pathinfo($videoFile['name'], PATHINFO_EXTENSION);
                $filename = 'video_' . uniqid('vid_', true) . '_' . time() . '.' . $extension;
                $filepath = $uploadDir . $filename;
                
                if (move_uploaded_file($videoFile['tmp_name'], $filepath)) {
                    try {
                        $checkColumn = $db->query("SHOW COLUMNS FROM room_images LIKE 'media_type'");
                        $maxOrder = $db->prepare("SELECT COALESCE(MAX(image_order), 0) FROM room_images WHERE room_id = ?");
                        $maxOrder->execute([$roomId]);
                        $nextOrder = $maxOrder->fetchColumn() + 1;
                        
                        if ($checkColumn->rowCount() > 0) {
                            $stmt = $db->prepare("INSERT INTO room_images (room_id, image_path, media_type, image_order) VALUES (?, ?, 'video', ?)");
                            $stmt->execute([$roomId, $filename, $nextOrder]);
                        } else {
                            $stmt = $db->prepare("INSERT INTO room_images (room_id, image_path, image_order) VALUES (?, ?, ?)");
                            $stmt->execute([$roomId, $filename, $nextOrder]);
                        }
                        $success = true;
                    } catch (Exception $e) {
                        $error = 'Failed to save video: ' . $e->getMessage();
                        @unlink($filepath);
                    }
                } else {
                    $error = 'Failed to move uploaded video file.';
                }
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $imageId = intval($_GET['delete']);
    $stmt = $db->prepare("SELECT image_path FROM room_images WHERE id = ? AND room_id = ?");
    $stmt->execute([$imageId, $roomId]);
    $image = $stmt->fetch();
    
    if ($image) {
        deleteImage($image['image_path']);
        $stmt = $db->prepare("DELETE FROM room_images WHERE id = ?");
        $stmt->execute([$imageId]);
        header('Location: room-images.php?id=' . $roomId . '&deleted=1');
        exit;
    }
}

// Handle reorder
if (isset($_POST['reorder'])) {
    $orders = $_POST['image_order'] ?? [];
    foreach ($orders as $imageId => $order) {
        $stmt = $db->prepare("UPDATE room_images SET image_order = ? WHERE id = ? AND room_id = ?");
        $stmt->execute([intval($order), intval($imageId), $roomId]);
    }
    header('Location: room-images.php?id=' . $roomId . '&reordered=1');
    exit;
}

// Get room images
$stmt = $db->prepare("SELECT * FROM room_images WHERE room_id = ? ORDER BY image_order");
$stmt->execute([$roomId]);
$images = $stmt->fetchAll();

$pageTitle = 'Manage Images - ' . $room['title'];
include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <div class="page-header-admin">
        <div>
            <h1>Manage Images</h1>
            <p><?php echo htmlspecialchars($room['title']); ?></p>
        </div>
        <a href="rooms.php" class="btn btn-outline">← Back to Rooms</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">Image uploaded successfully!</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Image deleted successfully.</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['reordered'])): ?>
        <div class="alert alert-success">Image order updated.</div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Upload Form -->
    <div class="form-section">
        <h3>Upload Image or Video</h3>
        <form method="POST" enctype="multipart/form-data" class="upload-form">
            <div class="form-tabs" style="display: flex; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e5e7eb;">
                <button type="button" class="tab-btn active" onclick="switchTab('image-tab')" style="padding: 0.5rem 1rem; background: none; border: none; cursor: pointer; border-bottom: 2px solid var(--primary-color);">📷 Image</button>
                <button type="button" class="tab-btn" onclick="switchTab('video-url-tab')" style="padding: 0.5rem 1rem; background: none; border: none; cursor: pointer;">🎥 Video URL</button>
                <button type="button" class="tab-btn" onclick="switchTab('video-file-tab')" style="padding: 0.5rem 1rem; background: none; border: none; cursor: pointer;">📁 Upload Video</button>
            </div>
            
            <!-- Image Upload Tab -->
            <div id="image-tab" class="tab-content">
                <div class="form-group">
                    <label for="image">Select Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small>Max size: 5MB | Formats: JPG, PNG, WebP</small>
                </div>
                <button type="submit" class="btn btn-primary">Upload Image</button>
            </div>
            
            <!-- Video URL Tab -->
            <div id="video-url-tab" class="tab-content" style="display: none;">
                <div class="form-group">
                    <label for="video_url">Video URL</label>
                    <input type="url" id="video_url" name="video_url" placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/... or direct video URL">
                    <small>Supports YouTube, Vimeo, or direct video URLs (MP4, WebM, etc.)</small>
                </div>
                <button type="submit" class="btn btn-primary">Add Video</button>
            </div>
            
            <!-- Video File Upload Tab -->
            <div id="video-file-tab" class="tab-content" style="display: none;">
                <div class="form-group">
                    <label for="video_file">Select Video File</label>
                    <input type="file" id="video_file" name="video_file" accept="video/*">
                    <small>Max size: 50MB | Formats: MP4, WebM, OGG, MOV</small>
                </div>
                <button type="submit" class="btn btn-primary">Upload Video</button>
            </div>
        </form>
        
        <script>
        function switchTab(tabId) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.style.borderBottom = 'none';
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabId).style.display = 'block';
            event.target.style.borderBottom = '2px solid var(--primary-color)';
            event.target.classList.add('active');
            
            // Clear other inputs
            if (tabId !== 'image-tab') {
                document.getElementById('image').value = '';
            }
            if (tabId !== 'video-url-tab') {
                document.getElementById('video_url').value = '';
            }
            if (tabId !== 'video-file-tab') {
                document.getElementById('video_file').value = '';
            }
        }
        </script>
    </div>

    <!-- Images/Video Grid -->
    <div class="form-section">
        <h3>Room Media (<?php echo count($images); ?>)</h3>
        
        <?php if (empty($images)): ?>
            <p class="text-muted">No images uploaded yet. Upload your first image above.</p>
        <?php else: ?>
            <form method="POST" id="reorderForm">
                <input type="hidden" name="reorder" value="1">
                <div class="images-grid">
                    <?php foreach ($images as $img): 
                        $mediaType = $img['media_type'] ?? 'image';
                        $isVideo = $mediaType === 'video';
                        $videoUrl = $img['video_url'] ?? '';
                        $isUrlVideo = !empty($videoUrl);
                        $isLocalVideo = $isVideo && !$isUrlVideo && strpos($img['image_path'], 'video_') === 0;
                        
                        // For video URLs, try to get thumbnail
                        if ($isVideo && $isUrlVideo) {
                            $thumbnailUrl = '';
                            if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                                // Extract YouTube video ID
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $matches);
                                if (!empty($matches[1])) {
                                    $thumbnailUrl = 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
                                }
                            } elseif (strpos($videoUrl, 'vimeo.com') !== false) {
                                // Vimeo thumbnail would need API call, use placeholder
                                $thumbnailUrl = '';
                            }
                        } else {
                            $thumbnailUrl = ($isVideo && $isLocalVideo) 
                                ? ROOM_IMAGES_URL . $img['image_path'] 
                                : ROOM_IMAGES_URL . $img['image_path'];
                        }
                    ?>
                        <div class="image-item">
                            <div class="image-preview">
                                <?php if ($isVideo): ?>
                                    <div style="position: relative; background: #000; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                        <?php if ($thumbnailUrl): ?>
                                            <img src="<?php echo $thumbnailUrl; ?>" alt="Video thumbnail" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                                        <?php endif; ?>
                                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 3rem;">▶️</div>
                                        <div style="position: absolute; top: 0.5rem; right: 0.5rem; background: rgba(0,0,0,0.7); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">VIDEO</div>
                                    </div>
                                <?php else: ?>
                                    <img src="<?php echo ROOM_IMAGES_URL . $img['image_path']; ?>" alt="Room image">
                                <?php endif; ?>
                                <div class="image-overlay">
                                    <?php if ($isVideo && $isUrlVideo): ?>
                                        <a href="<?php echo htmlspecialchars($videoUrl); ?>" target="_blank" class="btn btn-sm btn-primary">View Video</a>
                                    <?php elseif ($isVideo && $isLocalVideo): ?>
                                        <a href="<?php echo ROOM_IMAGES_URL . $img['image_path']; ?>" target="_blank" class="btn btn-sm btn-primary">View Video</a>
                                    <?php else: ?>
                                        <a href="<?php echo ROOM_IMAGES_URL . $img['image_path']; ?>" target="_blank" class="btn btn-sm btn-primary">View</a>
                                    <?php endif; ?>
                                    <a href="?id=<?php echo $roomId; ?>&delete=<?php echo $img['id']; ?>" 
                                       onclick="return confirm('Delete this <?php echo $isVideo ? 'video' : 'image'; ?>?');" 
                                       class="btn btn-sm btn-danger">Delete</a>
                                </div>
                            </div>
                            <div class="image-order">
                                <label>Type:</label>
                                <span style="font-size: 0.875rem; color: #666;"><?php echo $isVideo ? '🎥 Video' : '📷 Image'; ?></span>
                                <label style="margin-top: 0.5rem;">Order:</label>
                                <input type="number" name="image_order[<?php echo $img['id']; ?>]" 
                                       value="<?php echo $img['image_order']; ?>" min="0" style="width: 80px;">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn-outline">Update Order</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

