<?php
require_once __DIR__ . '/../config/config.php';

$pageTitle = getSiteSetting('seo_title', 'Sukhniwas Guest House - Comfortable Stay');
$pageDescription = getSiteSetting('seo_description', 'Book your stay at Sukhniwas Guest House. Affordable rooms with modern amenities.');

// Get featured rooms
$db = getDB();
$stmt = $db->prepare("SELECT * FROM rooms WHERE is_visible = 1 ORDER BY price_per_night ASC LIMIT 3");
$stmt->execute();
$featuredRooms = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Welcome to <?php echo getSiteSetting('site_name', 'Sukhniwas Guest House'); ?></h1>
            <p class="hero-subtitle">Your Comfortable Home Away From Home</p>
            <div class="hero-buttons">
                <a href="rooms.php" class="btn btn-primary">View Rooms</a>
                <a href="guest-house-pricing.php" class="btn btn-outline-light">Book Guest House</a>
                <a href="contact.php" class="btn btn-outline-light">Contact Us</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🏠</div>
                    <h3>Comfortable Rooms</h3>
                    <p>Spacious and well-furnished rooms with all modern amenities</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>24/7 Security</h3>
                    <p>Round-the-clock security for your peace of mind</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📶</div>
                    <h3>Free WiFi</h3>
                    <p>High-speed internet connection in all rooms</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Affordable Prices</h3>
                    <p>Best value for money accommodation</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Rooms Section -->
    <?php if (!empty($featuredRooms)): ?>
    <section class="rooms-preview-section">
        <div class="container">
            <h2 class="section-title">Our Rooms</h2>
            <p class="section-subtitle">Choose from our range of comfortable accommodations</p>
            
            <div class="rooms-grid">
                <?php foreach ($featuredRooms as $room): 
                    $stmt = $db->prepare("SELECT image_path FROM room_images WHERE room_id = ? ORDER BY image_order LIMIT 1");
                    $stmt->execute([$room['id']]);
                    $image = $stmt->fetch();
                    if ($image) {
                        // Check if image is a URL (sample) or local path
                        $imageUrl = (strpos($image['image_path'], 'http') === 0) 
                            ? $image['image_path'] 
                            : ROOM_IMAGES_URL . $image['image_path'];
                    } else {
                        $imageUrl = getSampleRoomImage($room['id'], 600, 400);
                    }
                    $amenities = !empty($room['amenities']) ? explode(',', $room['amenities']) : [];
                ?>
                <div class="room-card">
                    <div class="room-image">
                        <img src="<?php echo $imageUrl; ?>" alt="<?php echo htmlspecialchars($room['title']); ?>" loading="lazy">
                        <div class="room-price-badge">
                            <?php 
                            $price = $room['price_per_night'] ?? 0;
                            $pricingType = $room['pricing_type'] ?? 'per_night';
                            if (($room['room_type'] ?? '') === 'pg' && $pricingType === 'per_night') {
                                $pricingType = 'per_month';
                            }
                            echo formatPrice($price, $pricingType); 
                            ?>
                        </div>
                    </div>
                    <div class="room-content">
                        <h3><?php echo htmlspecialchars($room['title']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($room['description'], 0, 100)) . '...'; ?></p>
                        <div class="room-amenities">
                            <?php foreach (array_slice($amenities, 0, 4) as $amenity): ?>
                                <span class="amenity-tag"><?php echo htmlspecialchars(trim($amenity)); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="room-actions">
                            <a href="room-details.php?slug=<?php echo $room['slug']; ?>" class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center" style="margin-top: 3rem;">
                <a href="rooms.php" class="btn btn-outline">View All Rooms</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Book Your Stay?</h2>
                <p>Contact us now for the best rates and availability</p>
                <div class="cta-buttons">
                    <a href="guest-house-pricing.php" class="btn btn-primary">Book Guest House</a>
                    <a href="<?php echo getWhatsAppLink(getSiteSetting('whatsapp_number')); ?>" target="_blank" class="btn btn-whatsapp">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.386 1.262.617 1.694.789.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.372a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Contact on WhatsApp
                    </a>
                    <a href="contact.php" class="btn btn-outline-light">Send Enquiry</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

