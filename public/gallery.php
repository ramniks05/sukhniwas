<?php
require_once __DIR__ . '/../config/config.php';

$pageTitle = 'Gallery - ' . getSiteSetting('site_name');
$pageDescription = 'View photos of our comfortable rooms and facilities.';

// Load images and videos from assets/images folder
function loadGalleryFromAssets() {
    $galleryPath = __DIR__ . '/../assets/images/';
    $mediaItems = [];
    
    if (!is_dir($galleryPath)) {
        return [];
    }
    
    // Get all files from the directory
    $files = scandir($galleryPath);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $filePath = $galleryPath . $file;
        $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        // Check if it's an image
        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $mediaItems[] = [
                'type' => 'image',
                'url' => SITE_URL . '/assets/images/' . $file,
                'title' => ucfirst(str_replace(['-', '_'], ' ', pathinfo($file, PATHINFO_FILENAME))),
                'caption' => ucfirst(str_replace(['-', '_'], ' ', pathinfo($file, PATHINFO_FILENAME)))
            ];
        }
        // Check if it's a video
        elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg', 'mov', 'avi'])) {
            $mediaItems[] = [
                'type' => 'video',
                'url' => SITE_URL . '/assets/images/' . $file,
                'title' => ucfirst(str_replace(['-', '_'], ' ', pathinfo($file, PATHINFO_FILENAME))),
                'caption' => ucfirst(str_replace(['-', '_'], ' ', pathinfo($file, PATHINFO_FILENAME))),
                'thumbnail' => '' // Will use video thumbnail or placeholder
            ];
        }
    }
    
    // Sort by filename
    usort($mediaItems, function($a, $b) {
        return strcmp($a['url'], $b['url']);
    });
    
    return $mediaItems;
}

// Load media from assets/images folder
$hardcodedMedia = loadGalleryFromAssets();

// Get all room images and videos from database (for future use)
$db = getDB();
$stmt = $db->query("SELECT ri.*, r.title as room_title, r.slug as room_slug 
                    FROM room_images ri 
                    JOIN rooms r ON ri.room_id = r.id 
                    WHERE r.is_visible = 1 
                    ORDER BY r.id, ri.image_order");
$mediaItems = $stmt->fetchAll();

// Combine assets/images media with database media (assets/images first)
$allMediaItems = array_merge($hardcodedMedia, $mediaItems);

include __DIR__ . '/../includes/header.php';
?>

<main class="page-main">
    <div class="page-header">
        <div class="container">
            <h1>Gallery</h1>
            <p>Take a virtual tour of our rooms and facilities</p>
        </div>
    </div>

    <section class="gallery-section">
        <div class="container">
            <?php if (empty($allMediaItems)): ?>
                <div class="empty-state">
                    <p>Gallery images and videos coming soon.</p>
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($allMediaItems as $item): 
                                                 // Check if this is an assets/images item or database item
                         if (isset($item['type'])) {
                             // Assets/images item
                             $isVideo = $item['type'] === 'video';
                             if ($isVideo) {
                                 // For local videos, use the video file itself as thumbnail source (browser will show first frame)
                                 $displayUrl = $item['url'];
                                 $mediaUrl = $item['url'];
                                 $videoUrl = $item['url'];
                             } else {
                                 // For images
                                 $displayUrl = $item['url'];
                                 $mediaUrl = $item['url'];
                                 $videoUrl = '';
                             }
                             $title = $item['title'] ?? '';
                             $caption = $item['caption'] ?? $title;
                         } else {
                            // Database item
                            $mediaType = $item['media_type'] ?? 'image';
                            $isVideo = $mediaType === 'video';
                            $videoUrl = $item['video_url'] ?? '';
                            $isUrlVideo = !empty($videoUrl);
                            $isLocalVideo = $isVideo && !$isUrlVideo && strpos($item['image_path'], 'video_') === 0;
                            
                            if ($isVideo && $isUrlVideo) {
                                // Video URL (YouTube, Vimeo, etc.)
                                $thumbnailUrl = '';
                                if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $matches);
                                    if (!empty($matches[1])) {
                                        $thumbnailUrl = 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
                                    }
                                }
                                $displayUrl = $thumbnailUrl ?: ROOM_IMAGES_URL . 'video-placeholder.jpg';
                                $mediaUrl = $videoUrl;
                            } elseif ($isVideo && $isLocalVideo) {
                                // Local video file
                                $displayUrl = ROOM_IMAGES_URL . $item['image_path'];
                                $mediaUrl = ROOM_IMAGES_URL . $item['image_path'];
                            } else {
                                // Image
                                $displayUrl = (strpos($item['image_path'], 'http') === 0) 
                                    ? $item['image_path'] 
                                    : ROOM_IMAGES_URL . $item['image_path'];
                                $mediaUrl = $displayUrl;
                            }
                            $title = $item['room_title'] ?? '';
                            $caption = $item['alt_text'] ?? $title;
                        }
                    ?>
                        <div class="gallery-item <?php echo $isVideo ? 'gallery-video' : ''; ?>" 
                             onclick="openLightbox('<?php echo htmlspecialchars($mediaUrl); ?>', '<?php echo htmlspecialchars($caption); ?>', '<?php echo $isVideo ? 'video' : 'image'; ?>', '<?php echo htmlspecialchars($videoUrl); ?>')">
                                                         <?php if ($isVideo): ?>
                                <div class="video-thumbnail">
                                    <?php if (!empty($displayUrl) && (strpos($displayUrl, 'youtube.com') !== false || strpos($displayUrl, 'img.youtube.com') !== false)): ?>
                                        <!-- YouTube thumbnail -->
                                        <img src="<?php echo $displayUrl; ?>" 
                                             alt="<?php echo htmlspecialchars($caption); ?>"
                                             loading="lazy"
                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23000\' width=\'400\' height=\'300\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' fill=\'%23fff\' text-anchor=\'middle\' dy=\'.3em\' font-size=\'20\'%3EVideo%3C/text%3E%3C/svg%3E'">
                                    <?php else: ?>
                                        <!-- Local video - show video element as thumbnail -->
                                        <video muted playsinline preload="metadata" style="width: 100%; height: 100%; object-fit: cover;">
                                            <source src="<?php echo htmlspecialchars($displayUrl); ?>" type="video/mp4">
                                        </video>
                                    <?php endif; ?>
                                    <div class="video-play-icon">▶️</div>
                                    <span class="video-badge">VIDEO</span>
                                </div>
                            <?php else: ?>
                                <img src="<?php echo $displayUrl; ?>" 
                                     alt="<?php echo htmlspecialchars($caption); ?>"
                                     loading="lazy">
                            <?php endif; ?>
                            <div class="gallery-overlay">
                                <span class="gallery-room-name"><?php echo htmlspecialchars($title); ?></span>
                                <?php if ($isVideo): ?>
                                    <span class="gallery-media-type">🎥 Video</span>
                                <?php else: ?>
                                    <span class="gallery-media-type">📷 <?php echo htmlspecialchars($caption); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<!-- Lightbox Modal -->
<div id="lightbox" class="lightbox" onclick="closeLightbox(event)">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <div id="lightbox-content"></div>
    <div id="lightbox-caption"></div>
</div>

<script>
function openLightbox(src, caption, mediaType = 'image', videoUrl = '') {
    const lightbox = document.getElementById('lightbox');
    const content = document.getElementById('lightbox-content');
    const captionEl = document.getElementById('lightbox-caption');
    
    content.innerHTML = '';
    
    if (mediaType === 'video') {
        let videoHtml = '';
        if (videoUrl) {
            // YouTube or Vimeo embed
            if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
                let videoId = '';
                const match = videoUrl.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
                if (match) videoId = match[1];
                if (videoId) {
                    videoHtml = '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' + videoId + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                } else {
                    videoHtml = '<a href="' + videoUrl + '" target="_blank" style="display: block; padding: 2rem; text-align: center; color: white;">Click to view video</a>';
                }
            } else if (videoUrl.includes('vimeo.com')) {
                const match = videoUrl.match(/vimeo\.com\/(\d+)/);
                if (match) {
                    videoHtml = '<iframe width="100%" height="100%" src="https://player.vimeo.com/video/' + match[1] + '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                } else {
                    videoHtml = '<a href="' + videoUrl + '" target="_blank" style="display: block; padding: 2rem; text-align: center; color: white;">Click to view video</a>';
                }
            } else {
                // Direct video URL or local video file
                // Detect video format from URL
                let videoType = 'video/mp4';
                if (src.includes('.webm')) videoType = 'video/webm';
                else if (src.includes('.ogg')) videoType = 'video/ogg';
                else if (src.includes('.mov')) videoType = 'video/quicktime';
                
                videoHtml = '<video controls autoplay style="width: 100%; max-width: 90vw; max-height: 90vh;"><source src="' + src + '" type="' + videoType + '">Your browser does not support the video tag.</video>';
            }
        } else {
            // Local video file
            videoHtml = '<video controls autoplay style="width: 100%; max-width: 90vw; max-height: 90vh;"><source src="' + src + '" type="video/mp4">Your browser does not support the video tag.</video>';
        }
        content.innerHTML = videoHtml;
    } else {
        // Image
        const img = document.createElement('img');
        img.src = src;
        img.alt = caption;
        img.id = 'lightbox-img';
        content.appendChild(img);
    }
    
    captionEl.textContent = caption;
    lightbox.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeLightbox(event) {
    if (event && event.target !== event.currentTarget && !event.target.classList.contains('lightbox-close')) {
        return;
    }
    const lightbox = document.getElementById('lightbox');
    lightbox.style.display = 'none';
    document.body.style.overflow = 'auto';
    
    // Stop video playback
    const content = document.getElementById('lightbox-content');
    if (content) {
        const videos = content.querySelectorAll('video, iframe');
        videos.forEach(v => {
            if (v.tagName === 'VIDEO') {
                v.pause();
                v.currentTime = 0;
            } else if (v.tagName === 'IFRAME') {
                v.src = v.src; // Reset iframe to stop playback
            }
        });
    }
}

// Close on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const lightbox = document.getElementById('lightbox');
        if (lightbox && lightbox.style.display === 'flex') {
            closeLightbox();
        }
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

