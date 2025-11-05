<?php
require_once __DIR__ . '/../config/config.php';

if (!isset($_GET['slug'])) {
    header('Location: rooms.php');
    exit;
}

$slug = sanitize($_GET['slug']);
$db = getDB();

$stmt = $db->prepare("SELECT * FROM rooms WHERE slug = ? AND is_visible = 1");
$stmt->execute([$slug]);
$room = $stmt->fetch();

if (!$room) {
    header('Location: rooms.php');
    exit;
}

// Get room images
$stmt = $db->prepare("SELECT * FROM room_images WHERE room_id = ? ORDER BY image_order");
$stmt->execute([$room['id']]);
$images = $stmt->fetchAll();

$amenities = !empty($room['amenities']) ? explode(',', $room['amenities']) : [];

$pageTitle = $room['title'] . ' - ' . getSiteSetting('site_name');
$pageDescription = substr($room['description'], 0, 160);

include __DIR__ . '/../includes/header.php';
?>

<main class="page-main">
    <div class="page-header">
        <div class="container">
            <nav class="breadcrumb">
                <a href="<?php echo SITE_URL; ?>/public/index.php">Home</a> / 
                <a href="<?php echo SITE_URL; ?>/public/rooms.php">Rooms</a> / 
                <span><?php echo htmlspecialchars($room['title']); ?></span>
            </nav>
            <h1><?php echo htmlspecialchars($room['title']); ?></h1>
        </div>
    </div>

    <section class="room-details-section">
        <div class="container">
            <div class="room-details-grid">
                <!-- Image Gallery -->
                <div class="room-gallery">
                    <?php if (!empty($images)): ?>
                        <?php 
                        // Check if first image is a URL (sample) or local path
                        $firstImgUrl = (strpos($images[0]['image_path'], 'http') === 0) 
                            ? $images[0]['image_path'] 
                            : ROOM_IMAGES_URL . $images[0]['image_path'];
                        ?>
                        <div class="gallery-main">
                            <img src="<?php echo $firstImgUrl; ?>" 
                                 alt="<?php echo htmlspecialchars($images[0]['alt_text'] ?? $room['title']); ?>" 
                                 id="main-image">
                        </div>
                        <?php if (count($images) > 1): ?>
                            <div class="gallery-thumbs">
                                <?php foreach ($images as $img): 
                                    $thumbUrl = (strpos($img['image_path'], 'http') === 0) 
                                        ? $img['image_path'] 
                                        : ROOM_IMAGES_URL . $img['image_path'];
                                ?>
                                    <img src="<?php echo $thumbUrl; ?>" 
                                         alt="<?php echo htmlspecialchars($img['alt_text'] ?? ''); ?>"
                                         onclick="changeMainImage(this.src)">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="gallery-main">
                            <img src="<?php echo getSampleRoomImage($room['id'], 800, 600); ?>" alt="<?php echo htmlspecialchars($room['title']); ?>">
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Room Info -->
                <div class="room-info">
                    <div class="room-price-box">
                        <div class="price-main">
                            <?php 
                            $price = $room['price_per_night'] ?? 0;
                            $pricingType = $room['pricing_type'] ?? 'per_night';
                            if (($room['room_type'] ?? '') === 'pg' && $pricingType === 'per_night') {
                                $pricingType = 'per_month';
                            }
                            echo formatPrice($price, $pricingType);
                            ?>
                        </div>
                        <?php if (!$room['is_available']): ?>
                            <div class="availability-badge unavailable">Currently Not Available</div>
                        <?php else: ?>
                            <div class="availability-badge available">Available</div>
                        <?php endif; ?>
                    </div>

                    <div class="room-description-full">
                        <h3>Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                    </div>

                    <div class="room-specs">
                        <div class="spec-item">
                            <span class="spec-icon">👥</span>
                            <div>
                                <strong>Max Occupancy</strong>
                                <p><?php echo $room['max_occupancy']; ?> guests</p>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($amenities)): ?>
                    <div class="room-amenities-full">
                        <h3>Amenities</h3>
                        <div class="amenities-grid">
                            <?php foreach ($amenities as $amenity): ?>
                                <div class="amenity-item">
                                    <span class="amenity-icon">✓</span>
                                    <span><?php echo htmlspecialchars(trim($amenity)); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="room-actions-full">
                        <a href="contact.php?room=<?php echo $room['id']; ?>" class="btn btn-primary btn-large">
                            Enquire Now
                        </a>
                        <a href="<?php echo getWhatsAppLink(getSiteSetting('whatsapp_number'), 'Hi, I am interested in ' . $room['title']); ?>" 
                           target="_blank" 
                           class="btn btn-whatsapp btn-large">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.386 1.262.617 1.694.789.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.372a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function changeMainImage(src) {
    document.getElementById('main-image').src = src;
    // Add active class to clicked thumbnail
    document.querySelectorAll('.gallery-thumbs img').forEach(img => {
        img.classList.remove('active');
        if (img.src === src) img.classList.add('active');
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

